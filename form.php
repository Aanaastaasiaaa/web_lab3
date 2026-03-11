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
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            overflow: hidden;
        }
        
        .header {
            background: #f8f9fa;
            padding: 30px;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        
        .header h1 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
        }
        
        .header p {
            color: #666;
        }
        
        .form-content {
            padding: 30px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 0.95em;
        }
        
        .required::after {
            content: " *";
            color: #dc3545;
        }
        
        input[type="text"],
        input[type="tel"],
        input[type="email"],
        input[type="date"],
        textarea,
        select {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-size: 1em;
            transition: border-color 0.3s;
        }
        
        input[type="text"]:focus,
        input[type="tel"]:focus,
        input[type="email"]:focus,
        input[type="date"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .radio-group {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        
        .radio-option {
            display: flex;
            align-items: center;
        }
        
        .radio-option input[type="radio"] {
            margin-right: 8px;
            width: 18px;
            height: 18px;
        }
        
        .radio-option label {
            margin-bottom: 0;
            font-weight: normal;
        }
        
        select[multiple] {
            height: 200px;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
        }
        
        .checkbox-group input[type="checkbox"] {
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }
        
        .checkbox-group label {
            margin-bottom: 0;
            font-weight: normal;
        }
        
        .hint {
            font-size: 0.85em;
            color: #666;
            margin-top: 5px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .error-message ul {
            margin-left: 20px;
            margin-top: 10px;
        }
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
            text-align: center;
        }
        
        .success-message a {
            color: #155724;
            font-weight: 600;
        }
        
        button {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 1.1em;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            transition: transform 0.2s;
        }
        
        button:hover {
            transform: translateY(-2px);
        }
        
        @media (max-width: 768px) {
            .container {
                margin: 10px;
            }
            
            .form-content {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📝 Анкета программиста</h1>
            <p>Заполните форму, чтобы стать частью нашего сообщества</p>
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
                
                <!-- 6. Любимые языки программирования (с ID) -->
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
                
                <!-- 7. Биография (НЕ обязательная) -->
                <div class="form-group">
                    <label for="biography">Биография</label>
                    <textarea id="biography" name="biography" rows="6" 
                              placeholder="Расскажите о себе..."><?= htmlspecialchars($_SESSION['form_data']['biography'] ?? '') ?></textarea>
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
    // Очищаем сохраненные данные формы после отображения
    unset($_SESSION['form_data']);
    ?>
</body>
</html>
