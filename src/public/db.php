<?php
// Налаштування підключення до БД
$host = 'db';
$db   = 'animal_shop';
$user = 'shop_user';
$pass = 'shop_pass_123';

// Підключення до MySQL
$conn = new mysqli($host, $user, $pass, $db);

// Перевірка з'єднання
if ($conn->connect_error) {
    die("Помилка з'єднання: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");
?>