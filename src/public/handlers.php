<?php
require_once 'db.php';
global $conn;

// Обробка видалення тварини
if (isset($_GET['delete_animal'])) {
    $id = intval($_GET['delete_animal']);
    $stmt = $conn->prepare("DELETE FROM animals WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Обробка видалення користувача
if (isset($_GET['delete_user'])) {
    $id = intval($_GET['delete_user']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// Обробка редагування тварини
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_animal'])) {
    $id = intval($_POST['edit_animal_id']);
    $name = trim($_POST['edit_animal_name']);
    $type = trim($_POST['edit_animal_type']);
    $price = floatval($_POST['edit_animal_price']);
    
    if (!empty($name) && !empty($type) && $price > 0) {
        $stmt = $conn->prepare("UPDATE animals SET name = ?, type = ?, price = ? WHERE id = ?");
        $stmt->bind_param("ssdi", $name, $type, $price, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}

// Обробка редагування користувача
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['edit_user_id']);
    $name = trim($_POST['edit_user_name']);
    $email = trim($_POST['edit_user_email']);
    $role = trim($_POST['edit_user_role']);
    
    if (!empty($name) && !empty($email) && !empty($role)) {
        $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
        $stmt->bind_param("sssi", $name, $email, $role, $id);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}

// Обробка додавання нової тварини
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_animal'])) {
    $name = trim($_POST['animal_name']);
    $type = trim($_POST['animal_type']);
    $price = floatval($_POST['animal_price']);
    
    if (!empty($name) && !empty($type) && $price > 0) {
        $stmt = $conn->prepare("INSERT INTO animals (name, type, price) VALUES (?, ?, ?)");
        $stmt->bind_param("ssd", $name, $type, $price);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}

// Обробка додавання нового користувача
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $name = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);
    $role = trim($_POST['user_role']);
    
    if (!empty($name) && !empty($email) && !empty($role)) {
        $stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $role);
        $stmt->execute();
        $stmt->close();
        header("Location: index.php");
        exit;
    }
}
?>
