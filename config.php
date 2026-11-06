<?php

class Database
{
    private $host = 'localhost';
    private $port = '3307';
    private $db_name = 'university_db';
    private $username = 'root';
    private $password = '';
    private $pdo;

    public function connect()
    {
        if ($this->pdo === null) {
            try {
                $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4";

                $this->pdo = new PDO($dsn, $this->username, $this->password, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $e) {
                die("Грешка при връзка с базата данни: " . $e->getMessage());
            }
        }

        return $this->pdo;
    }
}

$database = new Database();
$pdo = $database->connect();