<?php

$password = 'password123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "<h3>Хеш пароля для вставки в базу:</h3>";
echo "<textarea style='width:100%; height:100px;'>$hash</textarea>";
echo "<p>Пароль, який ти ввів: <strong>$password</strong></p>";
?>