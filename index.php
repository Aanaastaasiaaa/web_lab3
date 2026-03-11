<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'config.php';

// Функция валидации
function validateForm($data) {
    $errors = [];
    
    // 1. ФИО
    if (empty($data['full_name'])) {
        $errors[] = 'ФИО обязательно для заполнения';
    } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s-]+$/u', $data['full_name'])) {
        $errors[] = 'ФИО должно содержать только буквы, пробелы и дефисы';
    } elseif (strlen($data['full_name']) > 150) {
        $errors[] = 'ФИО не должно превышать 150 символов';
    }
    
    // 2. Телефон (исправлено: минимум 6 символов)
    if (empty($data['phone'])) {
        $errors[] = 'Телефон обязателен для заполнения';
    } elseif (!preg_match('/^[\+\d\s\-\(\)]{6,20}$/', $data['phone'])) {
        $errors[] = 'Телефон должен содержать от 6 до 20 символов: цифры, пробелы, дефисы, скобки, +';
    }
    
    // 3. Email
    if (empty($data['email'])) {
        $errors[] = 'Email обязателен для заполнения';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный формат email';
    }
    
    // 4. Дата рождения
    if (empty($data['birth_date'])) {
        $errors[] = 'Дата рождения обязательна';
    } else {
        $date = DateTime::createFromFormat('Y-m-d', $data['birth_date']);
        if (!$date || $date->format('Y-m-d') !== $data['birth_date']) {
            $errors[] = 'Некорректный формат даты';
        } elseif ($date > new DateTime()) {
            $errors[] = 'Дата рождения не может быть в будущем';
        }
    }
    
    // 5. Пол
    if (empty($data['gender'])) {
        $errors[] = 'Пол обязателен для выбора';
    } elseif (!in_array($data['gender'], ['male', 'female'])) {
        $errors[] = 'Недопустимое значение пола';
    }
    
    // 6. Языки
    if (empty($data['languages']) || !is_array($data['languages'])) {
        $errors[] = 'Выберите хотя бы один язык программирования';
    } else {
        foreach ($data['languages'] as $lang_id) {
            if (!in_array((int)$lang_id, range(1, 12))) {
                $errors[] = 'Выбран недопустимый язык программирования';
                break;
            }
        }
    }
    
    // 7. Биография
    if (empty($data['biography'])) {
        $errors[] = 'Биография обязательна для заполнения';
    } elseif (strlen($data['biography']) > 5000) {
        $errors[] = 'Биография не должна превышать 5000 символов';
    }
    
    // 8. Чекбокс
    if (!isset($data['contract_accepted'])) {
        $errors[] = 'Необходимо подтвердить ознакомление с контрактом';
    }
    
    return $errors;
}

// Если GET запрос - показываем форму
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    unset($_SESSION['success_message']);
    include 'form.php';
    exit;
}

// Сохраняем данные формы в сессию
$_SESSION['form_data'] = $_POST;

// Валидация
$errors = validateForm($_POST);

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: form.php');
    exit;
}

// Сохранение в БД
try {
    $pdo = getDB();
    
    // Вставляем основную информацию
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $contract = isset($_POST['contract_accepted']) ? 1 : 0;
    
    $stmt->execute([
        $_POST['full_name'],
        $_POST['phone'],
        $_POST['email'],
        $_POST['birth_date'],
        $_POST['gender'],
        $_POST['biography'],
        $contract
    ]);
    
    $application_id = $pdo->lastInsertId();
    
    // Вставляем языки
    if (!empty($_POST['languages'])) {
        $link_stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id) 
            VALUES (?, ?)
        ");
        
        foreach ($_POST['languages'] as $lang_id) {
            $link_stmt->execute([$application_id, $lang_id]);
        }
    }
    
    // Успех
    $_SESSION['success_message'] = "Анкета успешно сохранена! ID записи: $application_id";
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    
    header('Location: form.php');
    exit;
    
} catch (Exception $e) {
    // Ошибка
    error_log("Database error: " . $e->getMessage());
    $_SESSION['errors'] = ["Произошла ошибка при сохранении. Пожалуйста, попробуйте позже."];
    header('Location: form.php');
    exit;
}
?>
