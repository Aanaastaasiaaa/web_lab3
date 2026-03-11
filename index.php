<?php
// Включаем отображение всех ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Функция валидации
function validateForm($data) {
    $errors = [];
    
    // ФИО
    if (empty($data['full_name'])) {
        $errors[] = 'ФИО обязательно';
    } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s-]+$/u', $data['full_name'])) {
        $errors[] = 'ФИО только буквы и пробелы';
    } elseif (strlen($data['full_name']) > 150) {
        $errors[] = 'ФИО максимум 150 символов';
    }
    
    // Телефон
    if (empty($data['phone'])) {
        $errors[] = 'Телефон обязателен';
    } elseif (!preg_match('/^[\+\d\s\-\(\)]{10,20}$/', $data['phone'])) {
        $errors[] = 'Телефон неверного формата';
    }
    
    // Email
    if (empty($data['email'])) {
        $errors[] = 'Email обязателен';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email неверного формата';
    }
    
    // Дата рождения
    if (empty($data['birth_date'])) {
        $errors[] = 'Дата рождения обязательна';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
        if (!$date || $date > new DateTime()) {
            $errors[] = 'Дата рождения некорректна';
        }
    }
    
    // Пол
    if (empty($data['gender'])) {
        $errors[] = 'Пол обязателен';
    } elseif (!in_array($data['gender'], ['male', 'female'])) {
        $errors[] = 'Недопустимый пол';
    }
    
    // Языки
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
    
    // Биография
    if (empty($data['biography'])) {
        $errors[] = 'Биография обязательна';
    } elseif (strlen($data['biography']) > 5000) {
        $errors[] = 'Биография максимум 5000 символов';
    }
    
    // Чекбокс
    if (!isset($data['contract_accepted'])) {
        $errors[] = 'Примите условия';
    }
    
    return $errors;
}

// Если GET запрос - показываем форму
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['form_data'], $_SESSION['errors'], $_SESSION['success_message']);
    include 'form.php';
    exit;
}

// Сохраняем данные
$_SESSION['form_data'] = $_POST;

// Проверяем ошибки
$errors = validateForm($_POST);
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: form.php');
    exit;
}

// Сохраняем в БД
try {
    $pdo = getDB();
    $pdo->beginTransaction();
    
    // Вставляем основную информацию
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        isset($_POST['contract_accepted']) ? 1 : 0
    ]);
    
    $application_id = $pdo->lastInsertId();
    
    // Вставляем языки
    $link_stmt = $pdo->prepare("
        INSERT INTO application_languages (application_id, language_id) 
        VALUES (?, ?)
    ");
    
    foreach ($_POST['languages'] as $lang_id) {
        $link_stmt->execute([$application_id, $lang_id]);
    }
    
    $pdo->commit();
    
    $_SESSION['success_message'] = "Сохранено! ID: $application_id";
    unset($_SESSION['form_data']);
    header('Location: form.php');
    exit;
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // ПОКАЗЫВАЕМ ОШИБКУ!
    die("<h2>ОШИБКА БД:</h2><pre>" . $e->getMessage() . "</pre>");
}
?>
