<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Анкета программиста</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(145deg, #fbbf24 0%, #f59e0b 100%);
            min-height: 100vh;
            padding: 30px 20px;
        }
        
        .container {
            max-width: 820px;
            margin: 0 auto;
            background: #fff9e6;
            border-radius: 20px;
            border: 1px solid #fde68a;
            overflow: hidden;
        }
        
        .header {
            background: #fffbeb;
            padding: 35px 30px;
            text-align: center;
            border-bottom: 2px solid #fcd34d;
        }
        
        .header h1 {
            color: #92400e;
            font-size: 2.2em;
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        
        .header p {
            color: #b45309;
            font-size: 1.1em;
        }
        
        .form-content {
            padding: 35px;
            background: white;
        }
        
        .form-group {
            margin-bottom: 28px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #92400e;
            font-weight: 600;
            font-size: 0.95em;
            letter-spacing: 0.3px;
        }
        
        .required::after {
            content: " *";
            color: #dc2626;
            font-weight: 700;
        }
        
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #fde68a;
            border-radius: 12px;
            font-size: 1em;
            transition: border-color 0.2s ease;
            background: #fefce8;
            color: #1e293b;
        }
        
        input[type="text"]:hover,
        input[type="tel"]:hover,
        input[type="email"]:hover,
        input[type="date"]:hover,
        textarea:hover,
        select:hover {
            border-color: #fbbf24;
            background: #fffbeb;
        }
        
        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #f59e0b;
            background: white;
        }
        
        .radio-group {
            display: flex;
            gap: 25px;
            flex-wrap: wrap;
            background: #fefce8;
            padding: 15px 20px;
            border-radius: 12px;
            border: 2px solid #fde68a;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
        }
        
        .radio-option input[type="radio"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            accent-color: #f59e0b;
        }
        
        .radio-option label {
            margin-bottom: 0;
            font-weight: 500;
            color: #92400e;
        }
        
        select[multiple] {
            height: 200px;
            padding: 10px;
        }
        
        select[multiple] option {
            padding: 8px 12px;
            border-radius: 6px;
            margin: 2px 0;
        }
        
        select[multiple] option:checked {
            background: #fbbf24;
            color: #92400e;
        }
        
        select[multiple] option:hover {
            background: #fde68a;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            background: #fefce8;
            padding: 15px 20px;
            border-radius: 12px;
            border: 2px solid #fde68a;
        }
        
        .checkbox-group:hover {
            background: #fffbeb;
            border-color: #fbbf24;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 12px;
            width: 20px;
            height: 20px;
            accent-color: #f59e0b;
        }
        
        .checkbox-group label {
            margin-bottom: 0;
            font-weight: 500;
            color: #92400e;
            flex: 1;
        }
        
        .hint {
            font-size: 0.85em;
            color: #b45309;
            margin-top: 6px;
        }
        
        .error-message {
            background: #fef2f2;
            color: #991b1b;
            padding: 18px 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 2px solid #fca5a5;
        }
        
        .error-message ul {
            margin-left: 25px;
            margin-top: 10px;
        }
        
        .error-message li {
            margin: 5px 0;
        }
        
        .success-message {
            background: #f0fdf4;
            color: #166534;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            border: 2px solid #86efac;
            text-align: center;
            font-weight: 500;
        }
        
        .success-message a {
            color: #166534;
            font-weight: 600;
            text-decoration: none;
            border-bottom: 2px solid #86efac;
            padding-bottom: 2px;
        }
        
        .success-message a:hover {
            color: #052e16;
            border-bottom-color: #166534;
        }
        
        button {
            background: #f59e0b;
            color: white;
            border: none;
            padding: 16px 32px;
            font-size: 1.2em;
            font-weight: 600;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            transition: background-color 0.2s ease;
            letter-spacing: 0.5px;
        }
        
        button:hover {
            background: #d97706;
        }
        
        button:active {
            background: #b45309;
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            
            .form-content {
                padding: 20px;
            }
            
            .header h1 {
                font-size: 1.8em;
            }
            
            .radio-group {
                flex-direction: column;
                gap: 10px;
            }
            
            button {
                padding: 14px 24px;
                font-size: 1.1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Анкета программиста</h1>
            <p>Заполните форму для участия в сообществе</p>
        </div>
        
        <div class="form-content">
            <?php
            session_start();
            if (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <?= $_SESSION['success_message'] ?>
                    <br><br>
                    <a href="form.php">Заполнить новую анкету</a>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                <div class="error-message">
                    <strong>Пожалуйста, исправьте следующие ошибки:</strong>
                    <ul>
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php unset($_SESSION['errors']); ?>
            <?php endif; ?>
            
            <form method="POST" action="index.php">
                <!-- 1. ФИО -->
                <div class="form-group">
                    <label for="full_name" class="required">ФИО</label>
                    <input type="text" id="full_name" name="full_name" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['full_name'] ?? '') ?>" 
                           placeholder="Иванов Иван Иванович" required>
                    <div class="hint">Только буквы и пробелы, не более 150 символов</div>
                </div>
                
                <!-- 2. Телефон -->
                <div class="form-group">
                    <label for="phone" class="required">Телефон</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['phone'] ?? '') ?>" 
                           placeholder="+7 (999) 123-45-67" required>
                </div>
                
                <!-- 3. Email -->
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['email'] ?? '') ?>" 
                           placeholder="ivan@example.com" required>
                </div>
                
                <!-- 4. Дата рождения -->
                <div class="form-group">
                    <label for="birth_date" class="required">Дата рождения</label>
                    <input type="date" id="birth_date" name="birth_date" 
                           value="<?= htmlspecialchars($_SESSION['form_data']['birth_date'] ?? '') ?>" required>
                </div>
                
                <!-- 5. Пол -->
                <div class="form-group">
                    <label class="required">Пол</label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="male" name="gender" value="male" required
                                <?= (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'male') ? 'checked' : '' ?>>
                            <label for="male">Мужской</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="female" name="gender" value="female"
                                <?= (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'female') ? 'checked' : '' ?>>
                            <label for="female">Женский</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="other" name="gender" value="other"
                                <?= (isset($_SESSION['form_data']['gender']) && $_SESSION['form_data']['gender'] == 'other') ? 'checked' : '' ?>>
                            <label for="other">Другой</label>
                        </div>
                    </div>
                </div>
                
                <!-- 6. Любимые языки программирования -->
                <div class="form-group">
                    <label for="languages" class="required">Любимые языки программирования</label>
                    <select name="languages[]" id="languages" multiple required size="6">
                        <option value="1" <?= (isset($_SESSION['form_data']['languages']) && in_array('1', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Pascal</option>
                        <option value="2" <?= (isset($_SESSION['form_data']['languages']) && in_array('2', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>C</option>
                        <option value="3" <?= (isset($_SESSION['form_data']['languages']) && in_array('3', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>C++</option>
                        <option value="4" <?= (isset($_SESSION['form_data']['languages']) && in_array('4', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>JavaScript</option>
                        <option value="5" <?= (isset($_SESSION['form_data']['languages']) && in_array('5', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>PHP</option>
                        <option value="6" <?= (isset($_SESSION['form_data']['languages']) && in_array('6', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Python</option>
                        <option value="7" <?= (isset($_SESSION['form_data']['languages']) && in_array('7', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Java</option>
                        <option value="8" <?= (isset($_SESSION['form_data']['languages']) && in_array('8', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Haskell</option>
                        <option value="9" <?= (isset($_SESSION['form_data']['languages']) && in_array('9', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Clojure</option>
                        <option value="10" <?= (isset($_SESSION['form_data']['languages']) && in_array('10', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Prolog</option>
                        <option value="11" <?= (isset($_SESSION['form_data']['languages']) && in_array('11', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Scala</option>
                        <option value="12" <?= (isset($_SESSION['form_data']['languages']) && in_array('12', $_SESSION['form_data']['languages'])) ? 'selected' : '' ?>>Go</option>
                    </select>
                    <div class="hint">Держите Ctrl (Cmd на Mac) для выбора нескольких</div>
                </div>
                
                <!-- 7. Биография -->
                <div class="form-group">
                    <label for="biography">Биография</label>
                    <textarea id="biography" name="biography" rows="6" 
                              placeholder="Расскажите о своем опыте и интересах..."><?= htmlspecialchars($_SESSION['form_data']['biography'] ?? '') ?></textarea>
                    <div class="hint">Не обязательно, максимум 5000 символов</div>
                </div>
                
                <!-- 8. Чекбокс с контрактом -->
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="contract" name="contract_accepted" value="1" required
                            <?= isset($_SESSION['form_data']['contract_accepted']) ? 'checked' : '' ?>>
                        <label for="contract" class="required">Я ознакомлен(а) с контрактом и принимаю условия</label>
                    </div>
                </div>
                
                <!-- 9. Кнопка сохранения -->
                <button type="submit">Сохранить анкету</button>
            </form>
        </div>
    </div>
    
    <?php
    unset($_SESSION['form_data']);
    ?>
</body>
</html>
