<?php
require_once 'db.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}



// Папка для завантаження фото
$upload_dir = __DIR__ . '/uploads/animals/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Функція для redirect з помилками
function redirectWithErrors($errors) {
    $_SESSION['errors'] = $errors;
    header("Location: index.php");
    exit;
}

// === Видалення тварини (тільки Адмін + Заводник) ===
if (isset($_GET['delete_animal'])) {
    if (!hasRole(['Адміністратор сайту', 'Заводник'])) {
        redirectWithErrors(['У вас немає прав для видалення тварин']);
    }

    $id = intval($_GET['delete_animal']);
    if ($id > 0) {
        // Опціонально: отримуємо фото для видалення файлу
        $check = $conn->prepare("SELECT photo FROM animals WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        $result = $check->get_result();
        if ($row = $result->fetch_assoc()) {
            if ($row['photo'] && file_exists($upload_dir . $row['photo'])) {
                unlink($upload_dir . $row['photo']); // Видаляємо файл фото
            }
        }
        $check->close();

        $stmt = $conn->prepare("DELETE FROM animals WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: index.php");
    exit;
}

// === Видалення користувача (тільки Адміністратор) ===
if (isset($_GET['delete_user'])) {
    if (!hasRole(['Адміністратор сайту'])) {
        redirectWithErrors(['У вас немає прав для видалення користувачів']);
    }

    $id = intval($_GET['delete_user']);
    if ($id > 0 && $id != $_SESSION['user_id']) { // Не дозволяємо видаляти себе
        $check = $conn->prepare("SELECT id FROM users WHERE id = ?");
        $check->bind_param("i", $id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
        }
        $check->close();
    }
    header("Location: index.php");
    exit;
}

// === Додавання нової тварини ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_animal'])) {
    if (!hasRole(['Адміністратор сайту', 'Заводник'])) {
        redirectWithErrors(['У вас немає прав для додавання тварин']);
    }

    $errors = [];

    $name        = trim($_POST['animal_name'] ?? '');
    $type        = trim($_POST['animal_type'] ?? '');
    $price       = floatval($_POST['animal_price'] ?? 0);
    $description = trim($_POST['animal_description'] ?? '');

    // Валідація
    if (empty($name) || strlen($name) > 50) {
        $errors[] = 'Назва тварини повинна бути непорожньою та не довшою за 50 символів';
    }
    $allowed_types = ['Собака', 'Кіт'];
    if (!in_array($type, $allowed_types)) {
        $errors[] = 'Недозволений тип тварини';
    }
    if ($price < 0.01 || $price > 100000) {
        $errors[] = 'Ціна повинна бути від 0.01 до 100 000 ₴';
    }

    if (!empty($errors)) {
        redirectWithErrors($errors);
    }

    // Обробка фото
    $photo = '';
    if (isset($_FILES['animal_photo']) && $_FILES['animal_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['animal_photo']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed_ext)) {
            redirectWithErrors(['Дозволені формати фото: jpg, jpeg, png, gif, webp']);
        }
        $photo = 'animal_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['animal_photo']['tmp_name'], $upload_dir . $photo);
    }

    // Вставка в БД
    $stmt = $conn->prepare("INSERT INTO animals (name, type, price, description, photo) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $name, $type, $price, $description, $photo);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// === Редагування тварини ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_animal'])) {
    if (!hasRole(['Адміністратор сайту', 'Заводник'])) {
        redirectWithErrors(['У вас немає прав для редагування тварин']);
    }

    $errors = [];

    $id          = intval($_POST['edit_animal_id'] ?? 0);
    $name        = trim($_POST['edit_animal_name'] ?? '');
    $type        = trim($_POST['edit_animal_type'] ?? '');
    $price       = floatval($_POST['edit_animal_price'] ?? 0);
    $description = trim($_POST['edit_animal_description'] ?? '');

    if ($id <= 0) {
        $errors[] = 'Невірний ID тварини';
    }
    if (empty($name) || strlen($name) > 50) {
        $errors[] = 'Назва тварини повинна бути непорожньою та не довшою за 50 символів';
    }
    $allowed_types = ['Собака', 'Кіт'];
    if (!in_array($type, $allowed_types)) {
        $errors[] = 'Недозволений тип тварини';
    }
    if ($price < 0.01 || $price > 100000) {
        $errors[] = 'Ціна повинна бути від 0.01 до 100 000 ₴';
    }

    if (!empty($errors)) {
        redirectWithErrors($errors);
    }

    // Отримання старого фото для можливого видалення
    $old_photo = '';
    $check = $conn->prepare("SELECT photo FROM animals WHERE id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $result = $check->get_result();
    if ($row = $result->fetch_assoc()) {
        $old_photo = $row['photo'];
    }
    $check->close();

    // Обробка нового фото
    $photo = $old_photo; // За замовчуванням залишаємо старе
    if (isset($_FILES['edit_animal_photo']) && $_FILES['edit_animal_photo']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['edit_animal_photo']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed_ext)) {
            redirectWithErrors(['Дозволені формати фото: jpg, jpeg, png, gif, webp']);
        }
        $photo = 'animal_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
        move_uploaded_file($_FILES['edit_animal_photo']['tmp_name'], $upload_dir . $photo);

        // Видаляємо старе фото, якщо було
        if ($old_photo && $old_photo !== $photo && file_exists($upload_dir . $old_photo)) {
            unlink($upload_dir . $old_photo);
        }
    }

    // Оновлення в БД
    $stmt = $conn->prepare("UPDATE animals SET name = ?, type = ?, price = ?, description = ?, photo = ? WHERE id = ?");
    $stmt->bind_param("ssdssi", $name, $type, $price, $description, $photo, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// === Редагування користувача (тільки Адміністратор) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    if (!hasRole(['Адміністратор сайту'])) {
        redirectWithErrors(['У вас немає прав для редагування користувачів']);
    }


    $errors = [];
    
    $id = intval($_POST['edit_user_id'] ?? 0);
    $name = trim($_POST['edit_user_name'] ?? '');
    $email = trim($_POST['edit_user_email'] ?? '');
    $role = trim($_POST['edit_user_role'] ?? '');
    
    if ($id <= 0) {
        $errors[] = 'Невірний ID користувача';
    }
    if (empty($name) || strlen($name) > 50) {
        $errors[] = 'Ім\'я повинно бути непорожнім та не довшим за 50 символів';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        $errors[] = 'Некоректний або завеликий email';
    }
    $allowed_roles = ['Клієнт', 'Кур\'єр', 'Заводник', 'Адміністратор сайту'];
    if (!in_array($role, $allowed_roles)) {
        $errors[] = 'Недозволена роль';
    }
    
    if (!empty($errors)) {
        redirectWithErrors($errors);
    }
    
    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $role, $id);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// === Додавання нового користувача (тільки Адміністратор) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    if (!hasRole(['Адміністратор сайту'])) {
        redirectWithErrors(['У вас немає прав для додавання користувачів']);
    }

    // ... (твій існуючий код додавання користувача без змін) ...
    $errors = [];
    
    $name = trim($_POST['user_name'] ?? '');
    $email = trim($_POST['user_email'] ?? '');
    $role = trim($_POST['user_role'] ?? '');
    
    if (empty($name) || strlen($name) > 50) {
        $errors[] = 'Ім\'я повинно бути непорожнім та не довшим за 50 символів';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 50) {
        $errors[] = 'Некоректний або завеликий email';
    }
    $allowed_roles = ['Клієнт', 'Кур\'єр', 'Заводник', 'Адміністратор сайту'];
    if (!in_array($role, $allowed_roles)) {
        $errors[] = 'Недозволена роль';
    }
    
    if (!empty($errors)) {
        redirectWithErrors($errors);
    }
    
    $stmt = $conn->prepare("INSERT INTO users (name, email, role) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $role);
    $stmt->execute();
    $stmt->close();
    header("Location: index.php");
    exit;
}

// === Додавання замовлення (Адмін + Заводник) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    if (!hasRole(['Адміністратор сайту', 'Заводник'])) {
        redirectWithErrors(['Немає прав']);
    }

    $user_id = intval($_POST['user_id'] ?? 0);
    $animal_id = intval($_POST['animal_id'] ?? 0);
    $status = hasRole(['Адміністратор сайту']) ? ($_POST['status'] ?? 'Нове') : 'Нове';

    // Отримання суми з тварини
    $stmt = $conn->prepare("SELECT price FROM animals WHERE id = ?");
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $amount = $result->fetch_assoc()['price'] ?? 0;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO orders (user_id, animal_id, order_date, status, transfer_date, payment_date, amount) VALUES (?, ?, CURDATE(), ?, CURDATE(), CURDATE(), ?)");
    $stmt->bind_param("iisd", $user_id, $animal_id, $status, $amount);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// === Редагування замовлення (тільки Адмін) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_order'])) {
    if (!hasRole(['Адміністратор сайту'])) {
        redirectWithErrors(['Немає прав']);
    }
    // повна обробка всіх полів
}

// === Зміна статусу (тільки Кур'єр) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (!hasRole(['Кур\'єр'])) {
        redirectWithErrors(['Немає прав']);
    }
    $order_id = intval($_POST['order_id'] ?? 0);
    $status = trim($_POST['status'] ?? '');

    $allowed_status = ['Нове', 'В обробці', 'Доставлено', 'Скасовано'];
    if (!in_array($status, $allowed_status)) {
        $status = 'Нове';
    }

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();

    header("Location: index.php");
    exit;
}

// === Видалення замовлення (тільки Адмін) ===
if (isset($_GET['delete_order'])) {
    if (!hasRole(['Адміністратор сайту'])) {
        redirectWithErrors(['Немає прав']);
    }
    $id = intval($_GET['delete_order']);
    if ($id > 0) {
        $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: index.php");
    exit;
}

// Додавання замовлення (Адмін + Заводник, статус "Нове" для Заводника)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_order'])) {
    if (!hasRole(['Адміністратор сайту', 'Заводник'])) {
        redirectWithErrors(['Немає прав']);
    }
    $user_id = intval($_POST['user_id']);
    $animal_id = intval($_POST['animal_id']);
    $status = 'Нове'; // Заводник не може вибрати статус

    // Сума з ціни тварини
    $stmt = $conn->prepare("SELECT price FROM animals WHERE id = ?");
    $stmt->bind_param("i", $animal_id);
    $stmt->execute();
    $amount = $stmt->get_result()->fetch_assoc()['price'] ?? 0;

    $stmt = $conn->prepare("INSERT INTO orders (user_id, animal_id, order_date, status, amount, transfer_date, payment_date) VALUES (?, ?, CURDATE(), ?, ?, CURDATE(), CURDATE())");
    $stmt->bind_param("iisd", $user_id, $animal_id, $status, $amount);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Зміна статусу (тільки Кур'єр)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_status'])) {
    if (!hasRole(['Кур\'єр'])) {
        redirectWithErrors(['Немає прав']);
    }
    $order_id = intval($_POST['order_id']);
    $status = trim($_POST['status']);

    $allowed = ['Нове', 'В обробці', 'Товар відправлено', 'Замовлення завершено', 'Кошти повернуті'];
    if (!in_array($status, $allowed)) $status = 'Нове';

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

// Видалення (тільки Адмін)
if (isset($_GET['delete_order'])) {
    if (!hasRole(['Адміністратор сайту'])) {
        redirectWithErrors(['Немає прав']);
    }
    $id = intval($_GET['delete_order']);
    $stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: index.php");
    exit;
}

?>
