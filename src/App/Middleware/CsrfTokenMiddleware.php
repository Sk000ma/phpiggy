<?php

declare(strict_types=1);

namespace App\Middleware;

use Framework\Contracts\MiddlewareInterface;
use Framework\TemplateEngine;

//this middleware will generate a CSRF token for each request. 
//CSFR stands for Cross-Site Request Forgery. 
//Which is a type of attack that occurs when a malicious website, email, 
//or program causes a user's web browser to perform an unwanted action on a trusted site when the user is authenticated.
//how it works: is that it will generate a random string of 32 bytes and then convert it to a hexadecimal string.
class CsrfTokenMiddleware implements MiddlewareInterface
{
    public function __construct(private TemplateEngine $view)
    {
    }

    public function process(callable $next)
    {
        //Generate a CSRF token if one doesn't exist for this session.  
        //bin2hex() ensures we get a valid ASCII string. 
        //because random_bytes() might return non-ASCII bytes which cannot be used in URLs.
        $_SESSION['token'] = $_SESSION['token'] ?? bin2hex(random_bytes(32));

        $this->view->addGlobal('csrfToken', $_SESSION['token']);

        $next();
    }
}
