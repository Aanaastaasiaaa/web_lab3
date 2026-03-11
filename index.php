<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

echo "<!DOCTYPE html><html><head><style>
    body { background: #1e1e2e; color: #fff; font-family: monospace; padding: 20px; }
    .success { color: #a6e3a1; }
    .error { color: #f38ba8; }
    .info { color: #89b4fa; }
    pre { background: #313244; padding: 10px; border-radius: 5px; }
</style></head><body>";
echo "<h2>🔍 РЕЖИМ ОТЛАДКИ</h2>";

// Функция валидации
function validateForm($data) {
    $errors = [];
    
    if (empty($data['full_name'])) {
        $errors[] = 'ФИО обязательно';
    } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s-]+$/u', $data['full_name'])) {
        $errors[] = 'ФИО только буквы и пробелы';
    } elseif (strlen($data['full_name']) > 150) {
        $errors[] = 'ФИО максимум 150 символов';
    }
    
    if (empty($data['phone'])) {
        $errors[] = 'Телефон обязателен';
    } elseif (!preg_match('/^[\+\d\s\-\(\)]{1,20}$/', $data['phone'])) {
        $errors[] = 'Телефон неверного формата';
    }
    
    if (empty($data['email'])) {
        $errors[] = 'Email обязателен';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email неверного формата';
    }
    
    if (empty($data['birth_date'])) {
        $errors[] = 'Дата рождения обязательна';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
        if (!$date || $date > new DateTime()) {
            $errors[] = 'Дата некорректна';
        }
    }
    
    if (empty($data['gender'])) {
        $errors[] = 'Пол обязателен';
    } elseif (!in_array($data['gender'], ['male', 'female'])) {
        $errors[] = 'Недопустимый пол';
    }
    
    if (empty($data['languages']) || !is_array($data['languages'])) {
        $errors[] = 'Выберите язык';
    } else {
        foreach ($data['languages'] as $lang_id) {
            if (!in_array((int)$lang_id, range(1,12))) {
                $errors[] = 'Недопустимый язык';
                break;
            }
        }
    }
    
    if (empty($data['biography'])) {
        $errors[] = 'Биография обязательна';
    } elseif (strlen($data['biography']) > 5000) {
        $errors[] = 'Биография максимум 5000 символов';
    }
    
    if (!isset($data['contract_accepted'])) {
        $errors[] = 'Примите условия';
    }
    
    return $errors;
}

// GET запрос - показываем форму
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "<p class='info'>📋 GET запрос - показываем форму</p>";
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    unset($_SESSION['success_message']);
    include 'form.php';
    exit;
}

echo "<p class='info'>📨 POST запрос получен</p>";

// Сохраняем данные
$_SESSION['form_data'] = $_POST;

echo "<h3>📦 Данные из формы:</h3>";
echo "<pre>";
print_r($_POST);
echo "</pre>";

// Валидация
echo "<h3>🔍 Проверка валидации...</h3>";
$errors = validateForm($_POST);

if (!empty($errors)) {
    echo "<p class='error'>❌ Ошибки валидации:</p>";
    echo "<pre>";
    print_r($errors);
    echo "</pre>";
    
    $_SESSION['errors'] = $errors;
    echo "<p>🔙 Возвращаем на форму</p>";
    echo "<script>setTimeout(() => { window.location.href = 'form.php'; }, 3000);</script>";
    exit;
}

echo "<p class='success'>✅ Валидация пройдена</p>";

// Сохранение в БД
echo "<h3>💾 Сохраняем в БД...</h3>";

try {
    $pdo = getDB();
    echo "<p class='success'>✅ Подключение к БД успешно</p>";
    
    // Проверяем структуру таблицы
    $columns = $pdo->query("DESCRIBE applications")->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>📊 Поля таблицы: " . implode(', ', $columns) . "</p>";
    
    // Вставка
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $contract = isset($_POST['contract_accepted']) ? 1 : 0;
    
    echo "<p>📝 Выполняем запрос с данными:</p>";
    echo "<pre>";
    print_r([
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        $contract
    ]);
    echo "</pre>";
    
    $result = $stmt->execute([
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        $contract
    ]);
    
    if (!$result) {
        throw new Exception("Ошибка выполнения запроса");
    }
    
    $application_id = $pdo->lastInsertId();
    echo "<p class='success'>✅ Запись создана! ID: $application_id</p>";
    
    // Проверяем, что запись действительно есть
    $check = $pdo->prepare("SELECT * FROM applications WHERE id = ?");
    $check->execute([$application_id]);
    $record = $check->fetch();
    
    if ($record) {
        echo "<p class='success'>✅ Запись найдена в БД:</p>";
        echo "<pre>";
        print_r($record);
        echo "</pre>";
    } else {
        echo "<p class='error'>❌ Запись НЕ найдена после вставки!</p>";
    }
    
    // Вставка языков
    if (!empty($_POST['languages'])) {
        echo "<p>📝 Вставляем языки:</p>";
        $link_stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id) 
            VALUES (?, ?)
        ");
        
        foreach ($_POST['languages'] as $lang_id) {
            echo "  - язык ID: $lang_id<br>";
            $link_stmt->execute([$application_id, $lang_id]);
        }
        echo "<p class='success'>✅ Языки сохранены</p>";
    }
    
    // Успех
    $_SESSION['success_message'] = "Анкета успешно сохранена! ID: $application_id";
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    
    echo "<p class='success'>✅ ГОТОВО! Перенаправляем через 3 секунды...</p>";
    echo "<script>setTimeout(() => { window.location.href = 'form.php'; }, 3000);</script>";
    
} catch (Exception $e) {
    echo "<p class='error'>❌ ОШИБКА: " . $e->getMessage() . "</p>";
    echo "<pre>";
    echo "Файл: " . $e->getFile() . "\n";
    echo "Строка: " . $e->getLine() . "\n";
    echo "Трассировка:\n" . $e->getTraceAsString();
    echo "</pre>";
    
    error_log("ERROR: " . $e->getMessage());
    
    $_SESSION['errors'] = ["Ошибка БД: " . $e->getMessage()];
    echo "<p>🔙 Возвращаем на форму через 5 секунд...</p>";
    echo "<script>setTimeout(() => { window.location.href = 'form.php'; }, 5000);</script>";
}

echo "</body></html>";
?>
