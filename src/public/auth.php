<?php

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

function hasRole($allowed_roles) {
    $user_role = $_SESSION['user_role'] ?? '';
    return in_array($user_role, (array)$allowed_roles);
}

// Список ролей англійською для зручності (якщо захочеш змінити)
$role_map = [
    'Адміністратор сайту' => 'admin',
    'Заводник' => 'breader',
    'Кур\'єр' => 'courier',
    'Клієнт' => 'client'
];

$_SESSION['user_role_en'] = $role_map[$_SESSION['user_role']] ?? 'client';
?>