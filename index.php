<?php
session_start();
require_once 'config.php';

// Если форма отправлена
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = getDB();
        
        $stmt = $pdo->prepare("
            INSERT INTO applications 
            (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $result = $stmt->execute([
            $_POST['full_name'] ?? 'тест',
            $_POST['phone'] ?? '123',
            $_POST['email'] ?? 'test@test.com',
            $_POST['birth_date'] ?? '2000-01-01',
            $_POST['gender'] ?? 'male',
            $_POST['biography'] ?? 'тест',
            isset($_POST['contract_accepted']) ? 1 : 0
        ]);
        
        if ($result) {
            $id = $pdo->lastInsertId();
            echo "✅ СОХРАНЕНО! ID: $id";
            
            // Проверим, что запись действительно есть
            $check = $pdo->query("SELECT * FROM applications WHERE id = $id")->fetch();
            echo "<pre>";
            print_r($check);
            echo "</pre>";
        } else {
            echo "❌ НЕ сохранилось";
            print_r($stmt->errorInfo());
        }
        
    } catch (Exception $e) {
        echo "❌ ОШИБКА: " . $e->getMessage();
    }
    exit;
}
?>
<form method="POST">
    <input type="text" name="full_name" placeholder="ФИО" value="Тест"><br>
    <input type="text" name="phone" value="1234567890"><br>
    <input type="email" name="email" value="test@test.com"><br>
    <input type="date" name="birth_date" value="2000-01-01"><br>
    <select name="gender">
        <option value="male">Мужской</option>
        <option value="female">Женский</option>
    </select><br>
    <textarea name="biography">Тест</textarea><br>
    <input type="checkbox" name="contract_accepted" value="1" checked> Согласен<br>
    <button type="submit">Сохранить</button>
</form>
