<?php
header('Content-Type: text/html; charset=UTF-8');

// Подключение к БД
$user = 'u82277';
$pass = '1452026';
$db = new PDO('mysql:host=localhost;dbname=u82277', $user, $pass,
    [PDO::ATTR_PERSISTENT => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// GET запрос - показываем форму
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($_GET['save'])) {
        print('Спасибо, результаты сохранены.');
    }
    include('form.php');
    exit();
}

// POST запрос - проверяем и сохраняем
$errors = FALSE;
$error_messages = [];

// 1. ФИО
if (empty($_POST['full_name'])) {
    $error_messages[] = 'Заполните ФИО.';
    $errors = TRUE;
} elseif (strlen($_POST['full_name']) > 150) {
    $error_messages[] = 'ФИО не должно превышать 150 символов.';
    $errors = TRUE;
} elseif (!preg_match('/^[а-яА-ЯёЁa-zA-Z\s-]+$/u', $_POST['full_name'])) {
    $error_messages[] = 'ФИО должно содержать только буквы, пробелы и дефисы.';
    $errors = TRUE;
}

// 2. Телефон
if (empty($_POST['phone'])) {
    $error_messages[] = 'Заполните телефон.';
    $errors = TRUE;
} elseif (!preg_match('/^[\+\d\s\-\(\)]{6,20}$/', $_POST['phone'])) {
    $error_messages[] = 'Телефон должен содержать от 6 до 20 символов.';
    $errors = TRUE;
}

// 3. Email
if (empty($_POST['email'])) {
    $error_messages[] = 'Заполните email.';
    $errors = TRUE;
} elseif (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $error_messages[] = 'Некорректный email.';
    $errors = TRUE;
}

// 4. Дата рождения
if (empty($_POST['birth_date'])) {
    $error_messages[] = 'Заполните дату рождения.';
    $errors = TRUE;
} else {
    $date = DateTime::createFromFormat('Y-m-d', $_POST['birth_date']);
    if (!$date) {
        $error_messages[] = 'Некорректная дата рождения.';
        $errors = TRUE;
    } elseif ($date > new DateTime()) {
        $error_messages[] = 'Дата рождения не может быть в будущем.';
        $errors = TRUE;
    }
}

// 5. Пол
if (empty($_POST['gender'])) {
    $error_messages[] = 'Выберите пол.';
    $errors = TRUE;
} elseif (!in_array($_POST['gender'], ['male', 'female'])) {
    $error_messages[] = 'Некорректное значение пола.';
    $errors = TRUE;
}

// 6. Языки (используем ID)
if (empty($_POST['languages']) || !is_array($_POST['languages'])) {
    $error_messages[] = 'Выберите хотя бы один язык программирования.';
    $errors = TRUE;
} else {
    $allowed_langs = range(1, 12);
    foreach ($_POST['languages'] as $lang_id) {
        if (!in_array((int)$lang_id, $allowed_langs)) {
            $error_messages[] = 'Некорректный язык программирования.';
            $errors = TRUE;
            break;
        }
    }
}

// 7. Биография
if (empty($_POST['biography'])) {
    $error_messages[] = 'Заполните биографию.';
    $errors = TRUE;
} elseif (strlen($_POST['biography']) > 5000) {
    $error_messages[] = 'Биография не должна превышать 5000 символов.';
    $errors = TRUE;
}

// 8. Чекбокс
if (!isset($_POST['contract_accepted'])) {
    $error_messages[] = 'Подтвердите ознакомление с контрактом.';
    $errors = TRUE;
}

// Если есть ошибки - показываем их
if ($errors) {
    foreach ($error_messages as $message) {
        print($message . '<br/>');
    }
    exit();
}

// Сохранение в БД
try {
    // Начинаем транзакцию
    $db->beginTransaction();
    
    // 1. Вставляем основную информацию
    $stmt = $db->prepare("
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
    
    $application_id = $db->lastInsertId();
    
    // 2. Вставляем языки
    if (!empty($_POST['languages'])) {
        $stmt = $db->prepare("
            INSERT INTO application_languages (application_id, language_id) 
            VALUES (?, ?)
        ");
        
        foreach ($_POST['languages'] as $lang_id) {
            $stmt->execute([$application_id, $lang_id]);
        }
    }
    
    // Подтверждаем транзакцию
    $db->commit();
    
    // Успех - редирект
    header('Location: ?save=1');
    exit();
    
} catch(PDOException $e) {
    $db->rollBack();
    print('Ошибка базы данных: ' . $e->getMessage());
    exit();
}
?>
