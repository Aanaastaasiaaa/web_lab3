<?php
// Отключаем буферизацию вывода
ob_start();

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
    
    // 2. Телефон
    if (empty($data['phone'])) {
        $errors[] = 'Телефон обязателен для заполнения';
    } elseif (!preg_match('/^[\+\d\s\-\(\)]{6,20}$/', $data['phone'])) {
        $errors[] = 'Телефон должен содержать от 6 до 20 символов';
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
        if (!$date) {
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
                $errors[] = 'Выбран недопустимый язык';
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

// GET запрос
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Очищаем старые данные
    $_SESSION = array();
    session_regenerate_id(true);
    include 'form.php';
    exit;
}

// Сохраняем данные
$form_data = $_POST;

// Валидация
$errors = validateForm($form_data);

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['form_data'] = $form_data;
    session_write_close();
    header('Location: form.php');
    exit;
}

// Сохранение в БД
try {
    $pdo = getDB();
    
    // Начинаем транзакцию
    $pdo->beginTransaction();
    
    // Вставка
    $stmt = $pdo->prepare("
        INSERT INTO applications 
        (full_name, phone, email, birth_date, gender, biography, contract_accepted) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    $contract = isset($form_data['contract_accepted']) ? 1 : 0;
    
    $stmt->execute([
        $form_data['full_name'],
        $form_data['phone'],
        $form_data['email'],
        $form_data['birth_date'],
        $form_data['gender'],
        $form_data['biography'],
        $contract
    ]);
    
    $application_id = $pdo->lastInsertId();
    
    // Вставка языков
    if (!empty($form_data['languages'])) {
        $link_stmt = $pdo->prepare("
            INSERT INTO application_languages (application_id, language_id) 
            VALUES (?, ?)
        ");
        
        foreach ($form_data['languages'] as $lang_id) {
            $link_stmt->execute([$application_id, $lang_id]);
        }
    }
    
    // Подтверждаем транзакцию
    $pdo->commit();
    
    // Очищаем сессию полностью
    $_SESSION = array();
    session_regenerate_id(true);
    
    // Устанавливаем только сообщение об успехе
    $_SESSION['success_message'] = "Анкета успешно сохранена! ID записи: $application_id";
    
} catch (Exception $e) {
    // Откатываем транзакцию
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    // Логируем ошибку
    error_log("DB Error: " . $e->getMessage());
    
    // Очищаем сессию
    $_SESSION = array();
    session_regenerate_id(true);
    
    // Устанавливаем ошибку
    $_SESSION['errors'] = ["Ошибка при сохранении. Попробуйте еще раз."];
    $_SESSION['form_data'] = $form_data;
}

// Принудительно закрываем сессию
session_write_close();

// Перенаправляем
header('Location: form.php');
exit;
?>
