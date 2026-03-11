<?php

function getDB() {
    static $pdo = null;
    
    if ($pdo === null) {
        $host = 'localhost';
        $dbname = 'u82277';     
        $username = 'u82277';     
        $password = '1452026'; 
        
        try {
            $pdo = new PDO(
                "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
                $username,
                $password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Режим ошибок - исключения
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Ассоциативные массивы
                    PDO::ATTR_EMULATE_PREPARES => false // Отключаем эмуляцию подготовленных запросов
                ]
            );
        } catch (PDOException $e) {
            // Логируем ошибку и показываем пользователю общее сообщение
            error_log("Database connection error: " . $e->getMessage());
            die("Ошибка подключения к базе данных. Пожалуйста, попробуйте позже.");
        }
    }
    
    return $pdo;
}
