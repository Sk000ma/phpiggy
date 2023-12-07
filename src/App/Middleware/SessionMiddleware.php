<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use App\Exceptions\SessionException;

class SessionMiddleware implements MiddlewareInterface
{
    public function process(callable $next)
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            throw new SessionException('Session already started');
        }


        // This is a test to see if headers are sent before session_start()
        // ob_end_clean();
        // echo 'Hello';

        if (headers_sent($filename, $line)) {
            throw new SessionException("Headers already sent. Consider using output buffering. Data output started at {$filename}:{$line}");
        }

        session_set_cookie_params(
            [
                'secure' => $_ENV['APP_ENV'] === 'production',
                'httponly' => true,
                'samesite' => 'lax',
            ]
        );

        session_start();
        $next();
        session_write_close();
    }
}
