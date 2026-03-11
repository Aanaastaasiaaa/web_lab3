<?php
require_once 'config.php';

try {
    $pdo = getDB();
    echo "✅ Подключение к БД успешно!<br>";
    
    // Проверим, существует ли таблица
    $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "📊 Таблицы в БД: " . implode(', ', $tables) . "<br>";
    
    // Проверим структуру таблицы applications
    $columns = $pdo->query("DESCRIBE applications")->fetchAll(PDO::FETCH_ASSOC);
    echo "📋 Структура таблицы applications:<br>";
    echo "<pre>";
    print_r($columns);
    echo "</pre>";
    
} catch (Exception $e) {
    echo "❌ Ошибка: " . $e->getMessage();
}
