<?php
class Database {
    private static $instance = null;
    private $pdo;

    private function __construct() {
        try {
            $this->pdo = new PDO(
                'pgsql:host=' . DB_HOST . 
                ';port=' . DB_PORT . 
                ';dbname=' . DB_NAME,
                DB_USER,
                DB_PASS
            );
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("PostgreSQL connection failed: " . $e->getMessage());
        }
        if (!defined('DB_HOST') || !defined('DB_PORT') || !defined('DB_NAME') || 
        !defined('DB_USER') || !defined('DB_PASS')) {
        die("Database configuration constants are not defined");
    }
    
    try {
        $this->pdo = new PDO(
            'pgsql:host=' . DB_HOST . 
            ';port=' . DB_PORT . 
            ';dbname=' . DB_NAME,
            DB_USER,
            DB_PASS
        );
        // ... остальной код
    } catch (PDOException $e) {
        die("PostgreSQL connection failed: " . $e->getMessage());
    }
    }
    

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance->pdo;
    }
    
}
