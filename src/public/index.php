<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'auth.php';
require_once 'db.php';
require_once 'handlers.php';

$user_role = $_SESSION['user_role'] ?? '';

// Запити до БД
$animals_result = $conn->query("SELECT id, name, type, price, description, photo FROM animals ORDER BY id ASC");
$users_result = $conn->query("SELECT id, name, email, role FROM users ORDER BY id ASC");
$orders_result = $conn->query("SELECT o.id, o.order_date, o.status, o.amount, 
           u.name AS client_name, a.name AS animal_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    JOIN animals a ON o.animal_id = a.id
    ORDER BY o.order_date DESC
");
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
        <?php if (!empty($_SESSION['errors'])): ?>
        <div style="background: #ffebee; padding: 20px; border-radius: 8px; margin-bottom: 30px; color: #c62828; border-left: 5px solid #c62828;">
            <strong>Помилки валідації:</strong>
            <ul style="margin-top: 10px; margin-left: 20px;">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errors']); ?>
        <?php endif; ?>

        <header>
            <h1>Animal Shop - Панель управління</h1>
            <p style="margin-top: 20px; font-size: 1.1em;">
                Вітаємо, <strong><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></strong>
                (<?= htmlspecialchars($user_role) ?>)
                | <a href="logout.php" style="color: #f44336; text-decoration: none;">Вихід</a>
            </p>
        </header>

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
                        <?php while ($row = $animals_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['type']) ?></td>
                                <td><?= number_format($row['price'], 2) ?></td>
                                <td>
                                    <?php if (hasRole(['Адміністратор сайту', 'Заводник'])): ?>
                                        <button class="edit-btn"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                                data-type="<?= htmlspecialchars($row['type'], ENT_QUOTES) ?>"
                                                data-price="<?= $row['price'] ?>"
                                                data-description="<?= htmlspecialchars($row['description'] ?? '', ENT_QUOTES) ?>"
                                                data-photo="<?= htmlspecialchars($row['photo'] ?? '', ENT_QUOTES) ?>"
                                                onclick="openEditAnimalModalFromButton(this)">
                                            ✎ Редагувати
                                        </button>
                                        <a href="?delete_animal=<?= $row['id'] ?>"
                                           class="delete-btn"
                                           onclick="return confirm('Ви впевнені, що хочете видалити <?= htmlspecialchars($row['name']) ?>?')">
                                            🗑 Видалити
                                        </a>
                                    <?php endif; ?>

                                    <button class="edit-btn" style="background-color: #17a2b8; margin-top: 5px;"
                                            data-id="<?= $row['id'] ?>"
                                            data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                            data-type="<?= htmlspecialchars($row['type'], ENT_QUOTES) ?>"
                                            data-price="<?= $row['price'] ?>"
                                            data-description="<?= htmlspecialchars($row['description'] ?? '', ENT_QUOTES) ?>"
                                            data-photo="<?= htmlspecialchars($row['photo'] ?? '', ENT_QUOTES) ?>"
                                            onclick="openAnimalDetailsFromButton(this)">
                                            👁 Переглянути деталі
                                    </button>
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

            <?php if (hasRole(['Адміністратор сайту', 'Заводник'])): ?>
            <div class="add-form">
                <h3>➕ Додати нову тварину</h3>
                <form method="POST" action="" enctype="multipart/form-data">
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
                    </div>

                    <div class="form-row" style="margin-top: 20px;">
                        <div class="form-group">
                            <label for="animal_description">Опис тварини:</label>
                            <textarea id="animal_description" name="animal_description" rows="5" placeholder="Детальний опис породи, характеру тощо..." style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-family: Arial, sans-serif;"></textarea>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="animal_photo">Фото тварини:</label>
                            <input type="file" id="animal_photo" name="animal_photo" accept="image/*">
                            <small style="color: #666; display: block; margin-top: 5px;">Дозволені формати: jpg, jpeg, png, gif, webp</small>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="add_animal" class="add-btn">Додати тварину</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>



                <!-- Секція Замовлень — доступна Адмін, Заводник, Кур'єр -->
        <?php if (hasRole(['Адміністратор сайту', 'Заводник', 'Кур\'єр'])): ?>
        <div class="section">
            <div class="section-header">
                <h2>📦 Список замовлень</h2>
                <?php if (hasRole(['Адміністратор сайту', 'Заводник'])): ?>
                    <button class="add-btn" onclick="openAddOrderModal()">+ Додати замовлення</button>
                <?php endif; ?>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Дата замовлення</th>
                        <th>Клієнт</th>
                        <th>Тварина</th>
                        <th>Сума</th>
                        <th>Статус</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
                        <?php while ($row = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['order_date']) ?></td>
                                <td><?= htmlspecialchars($row['client_name']) ?></td>
                                <td><?= htmlspecialchars($row['animal_name']) ?></td>
                                <td><?= number_format($row['amount'], 2) ?> ₴</td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td>
                                    <?php if (hasRole(['Адміністратор сайту'])): ?>
                                        <button class="edit-btn" onclick="openEditOrderModal(
    <?= $row['id'] ?>,
    '<?= htmlspecialchars($row['order_date']) ?>',
    '<?= htmlspecialchars($row['status']) ?>',
    '<?= htmlspecialchars($row['transfer_date'] ?? '') ?>',
    '<?= htmlspecialchars($row['payment_date'] ?? '') ?>',
    <?= $row['amount'] ?>
)">
    ✎ Повне редагування
</button>

                                        <a href="?delete_order=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('Видалити?')">🗑</a>
                                    <?php elseif (hasRole(['Кур\'єр'])): ?>
                                        <button class="edit-btn" style="background:#ff9800;" onclick="openStatusModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['status']) ?>')">Змінити статус</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7">Немає замовлень</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <!-- Модалка редагування замовлення (тільки Адмін) -->
<div id="editOrderModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Редагування замовлення</h2>
            <span class="close-btn" onclick="closeEditOrderModal()">&times;</span>
        </div>
        <form method="POST" action="index.php">
            <input type="hidden" name="edit_order" value="1">
            <input type="hidden" id="edit_order_id" name="order_id">

            <div class="form-group">
                <label for="edit_order_date">Дата замовлення:</label>
                <input type="date" id="edit_order_date" name="order_date" required>
            </div>

            <div class="form-group">
                <label for="edit_order_status">Статус:</label>
                <select id="edit_order_status" name="status" required>
                    <option value="Нове">Нове</option>
                    <option value="В обробці">В обробці</option>
                    <option value="Товар відправлено">Товар відправлено</option>
                    <option value="Замовлення завершено">Замовлення завершено</option>
                    <option value="Кошти повернуті">Кошти повернуті</option>
                </select>
            </div>

            <div class="form-group">
                <label for="edit_amount">Сума (₴):</label>
                <input type="number" step="0.01" id="edit_amount" name="amount" required>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeEditOrderModal()">Скасувати</button>
                <button type="submit" class="btn-save">Зберегти</button>
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
                        <th>ПІБ</th>
                        <th>Email</th>
                        <th>Роль</th>
                        <th>Дії</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($users_result && $users_result->num_rows > 0): ?>
                        <?php while ($row = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['id']) ?></td>
                                <td><?= htmlspecialchars($row['name']) ?></td>
                                <td><?= htmlspecialchars($row['email']) ?></td>
                                <td><?= htmlspecialchars($row['role']) ?></td>
                                <td>
                                    <?php if (hasRole(['Адміністратор сайту'])): ?>
                                        <button class="edit-btn"
                                                data-id="<?= $row['id'] ?>"
                                                data-name="<?= htmlspecialchars($row['name'], ENT_QUOTES) ?>"
                                                data-email="<?= htmlspecialchars($row['email'], ENT_QUOTES) ?>"
                                                data-role="<?= htmlspecialchars($row['role'], ENT_QUOTES) ?>"
                                                onclick="openEditUserModalFromButton(this)">
                                            ✎ Редагувати
                                        </button>
                                        <a href="?delete_user=<?= $row['id'] ?>"
                                           class="delete-btn"
                                           onclick="return confirm('Ви впевнені, що хочете видалити <?= htmlspecialchars($row['name']) ?>?')">
                                            🗑 Видалити
                                        </a>
                                    <?php endif; ?>
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

            <?php if (hasRole(['Адміністратор сайту'])): ?>
            <div class="add-form">
                <h3>➕ Додати нового користувача</h3>
                <form method="POST" action="">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="user_name">ПІБ:</label>
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
                                <option value="Заводник">Заводник</option>
                                <option value="Адміністратор сайту">Адміністратор сайту</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" name="add_user" class="add-btn">Додати</button>
                        </div>
                    </div>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Модальне вікно детальної інформації про тварину -->
    <div id="animalDetailsModal" class="modal">
        <div class="modal-content" style="max-width: 700px;">
            <div class="modal-header">
                <h2>Детальна інформація про тварину</h2>
                <span class="close-btn" onclick="closeAnimalDetails()">&times;</span>
            </div>
            <div style="text-align: center; padding: 20px;">
                <img id="detail_photo" src="" alt="Фото тварини" style="max-width: 100%; max-height: 400px; border-radius: 10px; margin-bottom: 20px;">
                <h3 id="detail_name" style="margin: 15px 0;"></h3>
                <p><strong>Тип:</strong> <span id="detail_type"></span></p>
                <p><strong>Ціна:</strong> <span id="detail_price"></span> ₴</p>
                <p><strong>Опис:</strong></p>
                <p id="detail_description" style="white-space: pre-wrap; text-align: left; background: #f9f9f9; padding: 15px; border-radius: 8px;"></p>
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
            <form method="POST" action="index.php" enctype="multipart/form-data">
                <input type="hidden" name="edit_animal" value="1">
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

                <div class="form-group">
                    <label for="edit_animal_description">Опис тварини:</label>
                    <textarea id="edit_animal_description" name="edit_animal_description" rows="1" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:4px; font-family: Arial, sans-serif;"></textarea>
                </div>

                <div class="form-group">
                    <label>Поточне фото:</label>
                    <div style="text-align: center; margin: 10px 0;">
                        <img id="edit_current_photo" src="" alt="Поточне фото" style="max-width: 100%; max-height: 300px; border-radius: 8px; display: none;">
                        <p id="no_photo_text" style="color: #999;">Фото відсутнє</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="edit_animal_photo">Замінити фото:</label>
                    <input type="file" id="edit_animal_photo" name="edit_animal_photo" accept="image/*">
                    <small style="color: #666; display: block; margin-top: 5px;">Залиште порожнім, щоб залишити поточне фото</small>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeEditAnimalModal()">Скасувати</button>
                    <button type="submit" class="btn-save">Зберегти зміни</button>
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
            <form method="POST" action="index.php">
                <input type="hidden" name="edit_user" value="1">
                <input type="hidden" id="edit_user_id" name="edit_user_id">
                
                <div class="form-group">ПІБ:</label>
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
                        <option value="Заводник">Заводник</option>
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

                    <!-- Модалка додавання замовлення (Адмін + Заводник) -->
    <div id="addOrderModal" class="modal">
    <div class="modal-content" style="max-height: 85vh; overflow-y: auto; padding-bottom: 100px;">
        <div class="modal-header">
            <h2>Додати замовлення</h2>
            <span class="close-btn" onclick="closeAddOrderModal()">&times;</span>
        </div>
        <form method="POST" action="index.php">
            <input type="hidden" name="add_order" value="1">
            
            <div class="form-group">
                <label for="add_client">Клієнт:</label>
                <select id="add_client" name="user_id" required>
                    <?php
                    $clients = $conn->query("SELECT id, name FROM users WHERE role = 'Клієнт' ORDER BY name");
                    while ($c = $clients->fetch_assoc()): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="add_animal">Тварина:</label>
                <select id="add_animal" name="animal_id" required style="height: 75px; overflow-y: auto;">
                    <?php
                    $animals_list = $conn->query("SELECT id, name FROM animals ORDER BY name");
                    while ($a = $animals_list->fetch_assoc()): ?>
                        <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-cancel" onclick="closeAddOrderModal()">Скасувати</button>
                <button type="submit" class="btn-save">Додати</button>
            </div>
        </form>
    </div>
</div>
                

    <!-- Модалка зміни статусу (тільки Кур'єр) -->
    <div id="statusModal" class="modal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2>Змінити статус замовлення</h2>
                <span class="close-btn" onclick="closeStatusModal()">&times;</span>
            </div>
            <form method="POST">
                <input type="hidden" name="change_status" value="1">
                <input type="hidden" id="status_id" name="order_id">
                <div class="form-group">
                    <label for="new_status">Новий статус:</label>
                    <select id="new_status" name="status" required>
                        <option value="Нове">Нове</option>
                        <option value="В обробці">В обробці</option>
                        <option value="Товар відправлено">Товар відправлено</option>
                        <option value="Замовлення завершено">Замовлення завершено</option>
                        <option value="Кошти повернуті">Кошти повернуті</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" onclick="closeStatusModal()">Скасувати</button>
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