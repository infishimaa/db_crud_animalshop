<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    $role = 'Клієнт'; // нові користувачі — тільки клієнти

    if (empty($name) || empty($email) || empty($password)) {
        $errors[] = 'Заповніть усі обов’язкові поля';
    } elseif ($password !== $password_confirm) {
        $errors[] = 'Паролі не співпадають';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Пароль має бути не коротше 6 символів';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Невірний формат email';
    } else {
        // Перевіряємо, чи email вже існує
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $errors[] = 'Користувач з таким email вже зареєстрований';
        }
        $check->close();

        if (empty($errors)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, role, password) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $role, $hashed_password);
            $stmt->execute();
            $stmt->close();

            // Автоматичний вхід після реєстрації
            $user_id = $conn->insert_id;
            $_SESSION['user_id'] = $user_id;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_role'] = $role;
            header("Location: index.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація - Animal Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="section" style="max-width: 500px; margin: 100px auto;">
            <h2 style="text-align: center; margin-bottom: 30px;">🐾 Реєстрація</h2>

            <?php if (!empty($errors)): ?>
            <div style="background: #ffebee; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #c62828;">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="name">ПІБ:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="password_confirm">Підтвердження пароля:</label>
                    <input type="password" id="password_confirm" name="password_confirm" required>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="add-btn">Зареєструватися</button>
                </div>
            </form>
            <p style="text-align: center; margin-top: 20px;">
                Вже є акаунт? <a href="login.php">Увійти</a>
            </p>
        </div>
    </div>
</body>
</html>