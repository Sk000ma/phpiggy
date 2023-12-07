<?php

declare(strict_types=1);

namespace App\Services;

use Framework\Database;

class TransactionService
{
    public function __construct(private Database $db)
    {
    }
    public function create(array $formData)
    {
        //this is a good way to format the date to be inserted into the database as it is in the correct format for the database to accept it
        $formattedDate = "{$formData['date']} 00:00:00";

        //prepared statement such as :user_id is a good way to prevent SQL injection attacks which it gets from the $formData array
        $this->db->query(
            "INSERT INTO transactions(user_id, description, amount, date)
            VALUES (:user_id, :description, :amount, :date)",
            [
                'user_id' => $_SESSION['user'],
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate
            ]
        );
    }
    public function getUserTransactions(int $length, int $offset)
    {
        $searchTerm = addcslashes($_GET['search'] ?? '', '%_');
        $params = [
            'user_id' => $_SESSION['user'],
            'description' => "%{$searchTerm}%"
        ];

        $transaction = $this->db->query(
            "SELECT *, DATE_FORMAT(date, '%m/%d/%Y') as formatted_date FROM transactions 
            WHERE user_id = :user_id AND description LIKE :description LIMIT {$length} OFFSET {$offset}",
            $params

        )->findAll();

        $transactionCount = $this->db->query(
            "SELECT COUNT(*) FROM transactions 
            WHERE user_id = :user_id AND description LIKE :description",
            $params
        )->count();

        return [$transaction, $transactionCount];
    }

    public function getUserTransaction(string $id)
    {
        return $this->db->query(
            "SELECT *, DATE_FORMAT(date, '%Y-%m-%d') as formatted_date
             FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        )->find();
    }

    public function update(array $formData, int $id)
    {
        $formattedDate = "{$formData['date']} 00:00:00";

        $this->db->query(
            "UPDATE transactions SET description = :description,
             amount = :amount, 
             date = :date
            WHERE id = :id
             AND user_id = :user_id",
            [
                'description' => $formData['description'],
                'amount' => $formData['amount'],
                'date' => $formattedDate,
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        );
    }
    public function delete(int $id)
    {
        $this->db->query(
            "DELETE FROM transactions WHERE id = :id AND user_id = :user_id",
            [
                'id' => $id,
                'user_id' => $_SESSION['user']
            ]
        );
    }
}
