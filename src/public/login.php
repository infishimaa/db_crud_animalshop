<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

require_once 'db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = 'Заповніть усі поля';
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                header("Location: index.php");
                exit;
            } else {
                $errors[] = 'Невірний пароль';
            }
        } else {
            $errors[] = 'Користувача з таким email не знайдено';
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід - Animal Shop</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <div class="section" style="max-width: 500px; margin: 100px auto;">
            <h2 style="text-align: center; margin-bottom: 30px;">🐾 Вхід в систему</h2>

            <?php if (!empty($errors)): ?>
            <div style="background: #ffebee; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #c62828;">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Пароль:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div style="text-align: center; margin-top: 20px;">
                    <button type="submit" class="add-btn">Увійти</button>
                </div>
            </form>
            <p style="text-align: center; margin-top: 20px;">
                Немає акаунта? <a href="register.php">Зареєструватися</a>
            </p>
        </div>
    </div>
</body>
</html>