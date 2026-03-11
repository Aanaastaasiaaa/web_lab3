
<?php
// ВРЕМЕННО: включаем отображение всех ошибок
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ВРЕМЕННО: смотрим, что пришло из формы
file_put_contents('debug.log', "=== " . date('Y-m-d H:i:s') . " ===\n", FILE_APPEND);
file_put_contents('debug.log', "POST: " . print_r($_POST, true), FILE_APPEND);
file_put_contents('debug.log', "REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "\n", FILE_APPEND);

session_start();
require_once 'config.php');
session_start();
require_once 'config.php';

// Функция валидации данных формы
function validateForm($data) {
    $errors = [];
    
    // 1. Валидация ФИО
    if (empty($data['full_name'])) {
        $errors[] = 'ФИО обязательно для заполнения';
    } elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s-]+$/u', $data['full_name'])) {
        $errors[] = 'ФИО должно содержать только буквы, пробелы и дефисы';
    } elseif (strlen($data['full_name']) > 150) {
    $errors[] = 'ФИО не должно превышать 150 символов';
}
    
    // 2. Валидация телефона
    if (empty($data['phone'])) {
        $errors[] = 'Телефон обязателен для заполнения';
    } elseif (!preg_match('/^[\+\d\s\-\(\)]{10,20}$/', $data['phone'])) {
        $errors[] = 'Телефон должен содержать от 10 до 20 символов: цифры, пробелы, дефисы, скобки, +';
    }
    
    // 3. Валидация email
    if (empty($data['email'])) {
        $errors[] = 'Email обязателен для заполнения';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Некорректный формат email';
    }
    
    // 4. Валидация даты рождения
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
    
    // 5. Валидация пола
    $allowed_genders = ['male', 'female', 'other'];
    if (empty($data['gender'])) {
        $errors[] = 'Пол обязателен для выбора';
    } elseif (!in_array($data['gender'], $allowed_genders)) {
        $errors[] = 'Недопустимое значение пола';
    }
    
    // 6. Валидация языков программирования (по ID)
    $allowed_languages = range(1, 12); // ID от 1 до 12
    
    if (empty($data['languages']) || !is_array($data['languages'])) {
        $errors[] = 'Выберите хотя бы один язык программирования';
    } else {
        foreach ($data['languages'] as $lang_id) {
            if (!in_array((int)$lang_id, $allowed_languages)) {
                $errors[] = 'Выбран недопустимый язык программирования';
                break;
            }
        }
    }
    
    // 7. Валидация биографии (НЕ обязательное поле)
    if (!empty($data['biography']) && strlen($data['biography']) > 5000) {
        $errors[] = 'Биография не должна превышать 5000 символов';
    }
    
    // 8. Валидация чекбокса
    if (!isset($data['contract_accepted']) || $data['contract_accepted'] != '1') {
        $errors[] = 'Необходимо подтвердить ознакомление с контрактом';
    }
    
    return $errors;
}

// Если форма не отправлена методом POST - показываем форму
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Очищаем предыдущие данные
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    unset($_SESSION['success_message']);
    
    include 'form.php';
    exit;
}

// Сохраняем отправленные данные в сессию (на случай ошибки)
$_SESSION['form_data'] = $_POST;

// Валидация данных
$errors = validateForm($_POST);

// Если есть ошибки - сохраняем их и возвращаем на форму
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    header('Location: form.php');
    exit;
}

// Если ошибок нет - сохраняем в БД
try {
    $pdo = getDB();
    
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    // 1. Вставляем основную информацию в таблицу applications
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
        VALUES 
        (:full_name, :phone, :email, :birth_date, :gender, :biography, :contract_accepted)
    ");
    
    $stmt->execute([
        ':full_name' => $_POST['full_name'],
        ':phone' => $_POST['phone'],
        ':email' => $_POST['email'],
        ':birth_date' => $_POST['birth_date'],
        ':gender' => $_POST['gender'],
        ':biography' => $_POST['biography'] ?? '',
        ':contract_accepted' => isset($_POST['contract_accepted']) ? 1 : 0
    ]);
    
    // Получаем ID новой записи
    $application_id = $pdo->lastInsertId();
    
    // 2. Вставляем связи с языками в таблицу application_languages
    $link_stmt = $pdo->prepare("
        INSERT INTO application_languages (application_id, language_id) 
        VALUES (:app_id, :lang_id)
    ");
    
    foreach ($_POST['languages'] as $lang_id) {
        $link_stmt->execute([
            ':app_id' => $application_id,
            ':lang_id' => $lang_id
        ]);
    }
    
    // Подтверждаем транзакцию
    $pdo->commit();
    
    // Сохраняем сообщение об успехе
    $_SESSION['success_message'] = "Анкета успешно сохранена! ID записи: $application_id";
    
    // Очищаем данные формы
    unset($_SESSION['form_data']);
    unset($_SESSION['errors']);
    
    // Перенаправляем на форму
    header("Location: form.php");
    exit;
    
} catch (PDOException $e) {
    // В случае ошибки отменяем транзакцию
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Логируем ошибку
    error_log("Database error in index.php: " . $e->getMessage());
    
    // Сохраняем сообщение об ошибке
    $_SESSION['errors'] = ["Произошла ошибка при сохранении. Пожалуйста, попробуйте позже."];
    
    // Перенаправляем обратно на форму
    header("Location: form.php");
    exit;
}
