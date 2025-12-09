<?php
require_once 'db.php';
require_once 'handlers.php';

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

// Отримання даних
$animals_result = $conn->query("SELECT id, name, type, price FROM animals ORDER BY id ASC");
$users_result = $conn->query("SELECT id, name, email, role FROM users ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Animal Shop - Управління</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- Секція Тварини -->
        <div class="section">
            <div class="section-header">
                <h2>🐾 Список тварин</h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Назва</th>
                        <th>Тип</th>
                        <th>Ціна (₴)</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($animals_result && $animals_result->num_rows > 0): ?>
                        <?php while($row = $animals_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><?= number_format($row['price'], 2) ?></td>
                                <td>
                                    <button class="edit-btn" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>" data-type="<?= htmlspecialchars($row['type'], ENT_QUOTES) ?>" data-price="<?= $row['price'] ?>" onclick="openEditAnimalModalFromButton(this)">
                                        ✎ Редагувати
                                    </button>
                                    <a href="?delete_animal=<?= $row['id'] ?>" 
                                       class="delete-btn"
                                       onclick="return confirm('Ви впевнені, що хочете видалити <?= htmlspecialchars($row['name']) ?>?')">
                                        🗑 Видалити
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">Немає даних</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="add-form">
                <h3>➕ Додати нову тварину</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="animal_name">Назва:</label>
                            <input type="text" id="animal_name" name="animal_name" required placeholder="Наприклад: Бігль">
                        </div>
                        
                        <div class="form-group">
                            <label for="animal_type">Тип:</label>
                            <select id="animal_type" name="animal_type" required>
                                <option value="">Оберіть тип</option>
                                <option value="Собака">Собака</option>
                                <option value="Кіт">Кіт</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="animal_price">Ціна (₴):</label>
                            <input type="number" id="animal_price" name="animal_price" step="0.01" min="0.01" required placeholder="0.00">
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="add_animal" class="add-btn">Додати</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Секція Користувачі -->
        <div class="section user-section">
            <div class="section-header">
                <h2>👥 Список користувачів</h2>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ім'я</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                        <?php while($row = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td>
                                    <button class="edit-btn" data-id="<?= $row['id'] ?>" data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>" data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>" data-role="<?= htmlspecialchars($row['role'], ENT_QUOTES) ?>" onclick="openEditUserModalFromButton(this)">
                                        ✎ Редагувати
                                    </button>
                                    <a href="?delete_user=<?= $row['id'] ?>" 
                                       class="delete-btn"
                                       onclick="return confirm('Ви впевнені, що хочете видалити <?= htmlspecialchars($row['name']) ?>?')">
                                        🗑 Видалити
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="no-data">Немає даних</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <div class="add-form">
                <h3>➕ Додати нового користувача</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user_name">Ім'я:</label>
                            <input type="text" id="user_name" name="user_name" required placeholder="Прізвище Ім'я По батькові">
                        </div>
                        
                        <div class="form-group">
                            <label for="user_email">Email:</label>
                            <input type="email" id="user_email" name="user_email" required placeholder="email@example.com">
                        </div>
                        
                        <div class="form-group">
                            <label for="user_role">Роль:</label>
                            <select id="user_role" name="user_role" required>
                                <option value="">Оберіть роль</option>
                                <option value="Клієнт">Клієнт</option>
                                <option value="Кур'єр">Кур'єр</option>
                                <option value="Розвідник">Розвідник</option>
                                <option value="Адміністратор сайту">Адміністратор сайту</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <button type="submit" name="add_user" class="add-btn">Додати</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Модальне вікно для редагування тварини -->
    <div id="editAnimalModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редагування тварини</h2>
                <span class="close-btn" onclick="closeEditAnimalModal()">&times;</span>
            </div>
            <form method="POST" action="" onsubmit="return validateEditAnimal()">
                <input type="hidden" id="edit_animal_id" name="edit_animal_id">
                
                <div class="form-group">
                    <label for="edit_animal_name">Назва:</label>
                    <input type="text" id="edit_animal_name" name="edit_animal_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_animal_type">Тип:</label>
                    <select id="edit_animal_type" name="edit_animal_type" required>
                        <option value="Собака">Собака</option>
                        <option value="Кіт">Кіт</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_animal_price">Ціна (₴):</label>
                    <input type="number" id="edit_animal_price" name="edit_animal_price" step="0.01" min="0.01" required>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditAnimalModal()">Скасувати</button>
                    <button type="submit" class="btn-save">Зберегти</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Модальне вікно для редагування користувача -->
    <div id="editUserModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Редагування користувача</h2>
                <span class="close-btn" onclick="closeEditUserModal()">&times;</span>
            </div>
            <form method="POST" action="" onsubmit="return validateEditUser()">
                <input type="hidden" id="edit_user_id" name="edit_user_id">
                
                <div class="form-group">
                    <label for="edit_user_name">Ім'я:</label>
                    <input type="text" id="edit_user_name" name="edit_user_name" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_user_email">Email:</label>
                    <input type="email" id="edit_user_email" name="edit_user_email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_user_role">Роль:</label>
                    <select id="edit_user_role" name="edit_user_role" required>
                        <option value="Клієнт">Клієнт</option>
                        <option value="Кур'єр">Кур'єр</option>
                        <option value="Розвідник">Розвідник</option>
                        <option value="Адміністратор сайту">Адміністратор сайту</option>
                    </select>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditUserModal()">Скасувати</button>
                    <button type="submit" class="btn-save">Зберегти</button>
                </div>
            </form>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
<?php
$conn->close();
?>