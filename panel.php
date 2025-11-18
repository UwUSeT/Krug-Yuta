<?php
require_once __DIR__ . '/vendor/helpers.php';
requireAdmin();

// Обработка AJAX-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_booking_status') {
    header('Content-Type: application/json');
    $pdo = getPDO();
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if ($id && in_array($status, ['ожидает', 'подтверждено', 'отменено'])) {
        $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$status, $id]);
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Неверные данные']);
    }
    exit;
}

// Обработка удаления отзыва
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_review'])) {
    $id = (int)$_GET['delete_review'];
    $pdo = getPDO();
    $pdo->prepare("DELETE FROM reviews WHERE id_review = ?")->execute([$id]);
    header("Location: ?page=reviews&success=Отзыв успешно удален");
    exit;
}

// Подготовка данных
$page = $_GET['page'] ?? null;
$message = null;
$edit_data = $all_amenities = $selected_amenity_ids = null;
$rooms = $clients = $bookings = $reviews = [];
$count_clients = $count_rooms = $count_bookings = $count_reviews = 0;

try {
    $pdo = getPDO();

    // Главная страница - статистика
    if (!$page) {
        $count_clients = (int)$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $count_rooms = (int)$pdo->query("SELECT COUNT(*) FROM rooms")->fetchColumn();
        $count_bookings = (int)$pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
        $count_reviews = (int)$pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
    }
    // Страница клиентов
    elseif ($page === 'clients') {
        $clients = $pdo->query("
            SELECT u.firstname, u.lastname, u.phone, u.email, r.name AS role
            FROM users u
            JOIN role r ON u.id_role = r.id_role
            ORDER BY u.firstname
        ")->fetchAll();
    }
    // Страница номеров
    elseif ($page === 'rooms') {
        // Удаление номера
        if (isset($_GET['delete'])) {
            $id = (int)$_GET['delete'];
            $stmt = $pdo->prepare("SELECT img1, img2, img3 FROM rooms WHERE id_room = ?");
            $stmt->execute([$id]);
            $images = $stmt->fetch();

            if ($images) {
                foreach (['img1', 'img2', 'img3'] as $field) {
                    $path = __DIR__ . '/' . ($images[$field] ?? '');
                    if (!empty($images[$field]) && file_exists($path)) unlink($path);
                }
            }

            $pdo->prepare("DELETE FROM rooms WHERE id_room = ?")->execute([$id]);
            $message = ['type' => 'success', 'text' => 'Номер и его изображения удалены.'];
        }

        // Редактирование
        if (isset($_GET['edit'])) {
            $id = (int)$_GET['edit'];
            $stmt = $pdo->prepare("SELECT * FROM rooms WHERE id_room = ?");
            $stmt->execute([$id]);
            $edit_data = $stmt->fetch();

            if ($edit_data) {
                $stmt2 = $pdo->prepare("SELECT amenity_id FROM room_and_amenity WHERE room_id = ?");
                $stmt2->execute([$id]);
                $selected_amenity_ids = array_column($stmt2->fetchAll(), 'amenity_id');
            }
        }

        // Все удобства
        $all_amenities = $pdo->query("SELECT amenity_id, name FROM amenity ORDER BY name")->fetchAll();

        // Обработка формы
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_room = $_POST['id_room'] ?? null;
            $room_number = trim($_POST['room_number'] ?? '');
            $room_name = trim($_POST['room_name'] ?? '');
            $capacity = trim($_POST['capacity'] ?? '2');
            $description = trim($_POST['description'] ?? '');
            $price = trim($_POST['price'] ?? '');
            $selected_amenities = $_POST['amenities'] ?? [];

            // Валидация
            $valid = true;
            if (!$room_number || !preg_match('/^\d{3}$/', $room_number)) {
                $message = ['type' => 'error', 'text' => 'Номер должен быть трёхзначным.'];
                $valid = false;
            } elseif (empty($room_name)) {
                $message = ['type' => 'error', 'text' => 'Укажите название типа номера.'];
                $valid = false;
            } elseif (!is_numeric($capacity) || $capacity < 1 || $capacity > 10) {
                $message = ['type' => 'error', 'text' => 'Вместимость должна быть от 1 до 10 гостей.'];
                $valid = false;
            } elseif (!is_numeric($price) || $price < 0) {
                $message = ['type' => 'error', 'text' => 'Цена должна быть положительным числом.'];
                $valid = false;
            } else {
                $checkSql = "SELECT COUNT(*) FROM rooms WHERE room_number = ?";
                $params = [$room_number];
                if ($id_room) {
                    $checkSql .= " AND id_room != ?";
                    $params[] = (int)$id_room;
                }
                $check = $pdo->prepare($checkSql);
                $check->execute($params);
                if ((int)$check->fetchColumn() > 0) {
                    $message = ['type' => 'error', 'text' => "Номер $room_number уже существует."];
                    $valid = false;
                }
            }

            // Валидация удобств
            if ($valid && !empty($selected_amenities)) {
                $int_amenities = array_map('intval', $selected_amenities);
                $placeholders = str_repeat('?,', count($int_amenities) - 1) . '?';
                $stmt_val = $pdo->prepare("SELECT COUNT(*) FROM amenity WHERE amenity_id IN ($placeholders)");
                $stmt_val->execute($int_amenities);
                if ((int)$stmt_val->fetchColumn() !== count($int_amenities)) {
                    $message = ['type' => 'error', 'text' => 'Выбраны недопустимые удобства.'];
                    $valid = false;
                }
            }

            // Обработка изображений
            $imgPaths = [null, null, null];
            if ($valid) {
                try {
                    // Функция загрузки изображения
                    $handleImageUpload = function ($fileKey, $roomNumber, $index, $oldPath = null) {
                        $uploadDir = __DIR__ . '/img/';
                        if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

                        if (empty($_FILES[$fileKey]['name'])) return $oldPath;

                        $file = $_FILES[$fileKey];
                        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/jpg', 'image/avif'];
                        $maxSize = 5 * 1024 * 1024;

                        if ($file['error'] !== UPLOAD_ERR_OK) throw new Exception('Ошибка загрузки файла');
                        if ($file['size'] > $maxSize) throw new Exception('Файл слишком большой (макс. 5 МБ)');
                        if (!in_array($file['type'], $allowedTypes)) throw new Exception('Разрешены только JPG, PNG, WebP, AVIF');

                        if ($oldPath && file_exists(__DIR__ . '/' . $oldPath)) unlink(__DIR__ . '/' . $oldPath);

                        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                        $safeExt = strtolower($ext) === 'jpeg' ? 'jpg' : strtolower($ext);
                        $filename = $roomNumber . '_' . $index . '_' . substr(sha1(uniqid()), 0, 10) . '.' . $safeExt;
                        $filePath = $uploadDir . $filename;

                        if (!move_uploaded_file($file['tmp_name'], $filePath)) throw new Exception('Не удалось сохранить изображение');
                        return 'img/' . $filename;
                    };

                    if ($id_room) {
                        $oldStmt = $pdo->prepare("SELECT img1, img2, img3 FROM rooms WHERE id_room = ?");
                        $oldStmt->execute([(int)$id_room]);
                        $oldImages = $oldStmt->fetch();
                        if (!$oldImages) throw new Exception('Номер не найден');

                        $imgPaths[0] = $handleImageUpload('img1', $room_number, 1, $oldImages['img1']);
                        $imgPaths[1] = $handleImageUpload('img2', $room_number, 2, $oldImages['img2']);
                        $imgPaths[2] = $handleImageUpload('img3', $room_number, 3, $oldImages['img3']);
                    } else {
                        $imgPaths[0] = !empty($_FILES['img1']['name']) ? $handleImageUpload('img1', $room_number, 1) : null;
                        $imgPaths[1] = !empty($_FILES['img2']['name']) ? $handleImageUpload('img2', $room_number, 2) : null;
                        $imgPaths[2] = !empty($_FILES['img3']['name']) ? $handleImageUpload('img3', $room_number, 3) : null;
                    }
                } catch (Exception $e) {
                    $message = ['type' => 'error', 'text' => 'Ошибка загрузки изображения: ' . $e->getMessage()];
                    $valid = false;
                }
            }

            if ($valid) {
                $pdo->beginTransaction();
                try {
                    if ($id_room) {
                        $pdo->prepare("
                            UPDATE rooms 
                            SET room_number = ?, room_name = ?, capacity = ?, description = ?, price = ?, img1 = ?, img2 = ?, img3 = ?
                            WHERE id_room = ?
                        ")->execute([
                            $room_number,
                            $room_name,
                            (int)$capacity,
                            $description,
                            (float)$price,
                            $imgPaths[0],
                            $imgPaths[1],
                            $imgPaths[2],
                            (int)$id_room
                        ]);
                        $pdo->prepare("DELETE FROM room_and_amenity WHERE room_id = ?")->execute([(int)$id_room]);
                    } else {
                        $pdo->prepare("
                            INSERT INTO rooms (room_number, room_name, capacity, description, price, img1, img2, img3)
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ")->execute([
                            $room_number,
                            $room_name,
                            (int)$capacity,
                            $description,
                            (float)$price,
                            $imgPaths[0],
                            $imgPaths[1],
                            $imgPaths[2]
                        ]);
                        $id_room = $pdo->lastInsertId();
                    }

                    if (!empty($selected_amenities)) {
                        $stmt_ins = $pdo->prepare("INSERT INTO room_and_amenity (room_id, amenity_id) VALUES (?, ?)");
                        foreach ($selected_amenities as $aid) {
                            $stmt_ins->execute([(int)$id_room, (int)$aid]);
                        }
                    }

                    $pdo->commit();
                    $message = ['type' => 'success', 'text' => "Номер $room_number " . ($id_room ? 'обновлён' : 'добавлен') . "!"];
                    $edit_data = null;
                } catch (Exception $e) {
                    $pdo->rollback();
                    $message = ['type' => 'error', 'text' => 'Ошибка при сохранении данных.'];
                }
            }
        }

        // Загрузка списка номеров
        $rooms = $pdo->query("SELECT * FROM rooms ORDER BY room_number")->fetchAll();
    }
    // Страница бронирований
    elseif ($page === 'bookings') {
        $bookings = $pdo->query("
            SELECT b.*, r.room_name, r.room_number, u.firstname AS client_firstname,
                   u.lastname AS client_lastname, u.phone AS client_phone, u.email AS client_email
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id_room
            JOIN users u ON b.id_user = u.id_user
            ORDER BY b.created_at DESC
        ")->fetchAll();
    }
    // Страница отзывов
    elseif ($page === 'reviews') {
        $reviews = $pdo->query("
            SELECT r.id_review, r.id_user, r.rating, r.comment, r.created_at, 
                   u.firstname, u.lastname, u.email
            FROM reviews r
            JOIN users u ON r.id_user = u.id_user
            ORDER BY r.created_at DESC
        ")->fetchAll();

        if (isset($_GET['success'])) {
            $message = ['type' => 'success', 'text' => htmlspecialchars($_GET['success'])];
        }
    }
} catch (PDOException $e) {
    $message = ['type' => 'error', 'text' => 'Ошибка базы данных: ' . htmlspecialchars($e->getMessage())];
}
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Админ-панель отеля</title>
    <link rel="shortcut icon" href="./icons/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&family=Jost:wght@500..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./css/panel.css">
</head>

<body>
    <h2>Админ-панель отеля</h2>

    <?php if (!$page): ?>
        <div class="main-menu-wrapper">
            <div class="main-menu">
                <a href="?page=clients" class="menu-card-link">
                    <div class="menu-card">
                        <h2>Клиенты</h2>
                        <div class="count"><?= $count_clients ?></div>
                    </div>
                </a>
                <a href="?page=rooms" class="menu-card-link">
                    <div class="menu-card">
                        <h2>Номера</h2>
                        <div class="count"><?= $count_rooms ?></div>
                    </div>
                </a>
                <a href="?page=bookings" class="menu-card-link">
                    <div class="menu-card">
                        <h2>Бронирования</h2>
                        <div class="count"><?= $count_bookings ?></div>
                    </div>
                </a>
                <a href="?page=reviews" class="menu-card-link">
                    <div class="menu-card">
                        <h2>Отзывы</h2>
                        <div class="count"><?= $count_reviews ?></div>
                    </div>
                </a>

                <div class="actions">
                    <form action="./vendor/actions/logout.php" method="post">
                        <button type="submit" class="btn-custom">Выйти</button>
                    </form>
                    <a href="./index.php">
                        <i class="fa-solid fa-circle-left"></i>
                    </a>
                </div>
            </div>
        </div>

    <?php elseif ($page === 'clients'): ?>
        <a href="?" class="back-btn">← Назад</a>
        <h2>Список клиентов</h2>
        <table>
            <tr>
                <th>Имя</th>
                <th>Фамилия</th>
                <th>Телефон</th>
                <th>Email</th>
                <th>Роль</th>
            </tr>
            <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= htmlspecialchars($client['firstname']) ?></td>
                    <td><?= htmlspecialchars($client['lastname']) ?></td>
                    <td><?= htmlspecialchars($client['phone']) ?></td>
                    <td><?= htmlspecialchars($client['email']) ?></td>
                    <td><?= htmlspecialchars($client['role']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

    <?php elseif ($page === 'rooms'): ?>
        <a href="?" class="back-btn">← Назад</a>
        <div class="admin-rooms-layout">
            <div class="form-section">
                <h2><?= $edit_data ? 'Редактировать номер' : 'Добавить новый номер' ?></h2>
                <form method="POST" enctype="multipart/form-data">
                    <?php if ($edit_data): ?>
                        <input type="hidden" name="id_room" value="<?= htmlspecialchars($edit_data['id_room']) ?>">
                    <?php endif; ?>

                    <label>Номер (трёхзначный):</label>
                    <input type="text" name="room_number" value="<?= htmlspecialchars($edit_data['room_number'] ?? '') ?>" maxlength="3" required>

                    <label>Название типа номера:</label>
                    <input type="text" name="room_name" value="<?= htmlspecialchars($edit_data['room_name'] ?? '') ?>" required>

                    <label>Вместимость (гостей):</label>
                    <input type="number" name="capacity" value="<?= htmlspecialchars($edit_data['capacity'] ?? '2') ?>" min="1" max="10" required>

                    <label>Цена (руб./ночь):</label>
                    <input type="number" name="price" value="<?= htmlspecialchars($edit_data['price'] ?? '') ?>" min="0" step="1" required>

                    <label>Описание:</label>
                    <textarea name="description" placeholder="Описание номера..."><?= htmlspecialchars($edit_data['description'] ?? '') ?></textarea>

                    <?php foreach (['img1', 'img2', 'img3'] as $i => $img): ?>
                        <label>Изображение <?= $i + 1 ?>:</label>
                        <div class="custom-file-upload">
                            <input type="file" name="<?= $img ?>" accept="image/jpeg,image/png,image/jpg,image/avif" class="file-input">
                            <button type="button" class="custom-file-button">Выбрать файл</button>
                            <span class="file-name">Не выбран ни один файл</span>
                        </div>
                        <?php if ($edit_data && !empty($edit_data[$img])): ?>
                            <div class="current-image-preview">
                                <img src="<?= htmlspecialchars($edit_data[$img]) ?>" alt="Изображение <?= $i + 1 ?>" loading="lazy">
                                <br><small>Текущее изображение</small>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <label>Удобства:</label>
                    <div class="amenities-checkboxes">
                        <?php foreach ($all_amenities as $am): ?>
                            <label>
                                <input type="checkbox" name="amenities[]" value="<?= (int)$am['amenity_id'] ?>"
                                    <?= $selected_amenity_ids && in_array($am['amenity_id'], $selected_amenity_ids) ? 'checked' : '' ?>>
                                <?= htmlspecialchars($am['name']) ?>
                            </label>
                        <?php endforeach; ?>
                    </div>

                    <button type="submit" class="<?= $edit_data ? 'update-btn' : 'add-btn' ?>">
                        <?= $edit_data ? 'Сохранить изменения' : 'Добавить номер' ?>
                    </button>
                    <?php if ($edit_data): ?>
                        <a href="?page=rooms" class="btn-custom">Отмена</a>
                    <?php endif; ?>
                </form>

                <?php if ($message): ?>
                    <div class="message <?= $message['type'] ?>"><?= htmlspecialchars($message['text']) ?></div>
                <?php endif; ?>
            </div>

            <?php if ($rooms): ?>
                <div class="rooms-list-section">
                    <h2>Список номеров</h2>
                    <ul class="rooms">
                        <?php foreach ($rooms as $room):
                            $room_amenities = $pdo->prepare("
                                SELECT a.name 
                                FROM amenity a
                                JOIN room_and_amenity ra ON a.amenity_id = ra.amenity_id
                                WHERE ra.room_id = ?
                            ");
                            $room_amenities->execute([$room['id_room']]);
                            $amenities_list = $room_amenities->fetchAll(PDO::FETCH_COLUMN);
                        ?>
                            <li>
                                <div class="room-info">
                                    <strong><?= htmlspecialchars($room['room_name']) ?> (№<?= htmlspecialchars($room['room_number']) ?>)</strong>
                                    <span class="capacity">Подходит для гостей: <?= (int)$room['capacity'] ?></span>
                                    <span class="price"><?= number_format($room['price'], 0, ',', ' ') ?> ₽/ночь</span>
                                    <?php if (!empty($room['description'])): ?>
                                        <br><small><?= htmlspecialchars(substr($room['description'], 0, 80)) . (strlen($room['description']) > 80 ? '...' : '') ?></small>
                                    <?php endif; ?>

                                    <?php $images = array_filter([$room['img1'], $room['img2'], $room['img3']]);
                                    if ($images): ?>
                                        <div class="images-preview">
                                            <?php foreach ($images as $img): ?>
                                                <img src="<?= htmlspecialchars($img) ?>" alt="Изображение номера" loading="lazy">
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>

                                    <div class="amenity-tag">
                                        <?= $amenities_list ? htmlspecialchars(implode(', ', $amenities_list)) : 'Нет удобств' ?>
                                    </div>
                                </div>
                                <div class="room-actions">
                                    <a href="?page=rooms&edit=<?= $room['id_room'] ?>" class="edit-btn" title="Изменить">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?page=rooms&delete=<?= $room['id_room'] ?>" class="delete-btn"
                                        onclick="return confirm('Вы уверены, что хотите удалить номер и все его изображения?')" title="Удалить">
                                        <i class="fas fa-trash-alt"></i>
                                    </a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($page === 'bookings'): ?>
        <a href="?" class="back-btn">← Назад</a>
        <h2>Управление бронированиями</h2>

        <?php if (empty($bookings)): ?>
            <p>Нет бронирований.</p>
        <?php else: ?>
            <table>
                <tr>
                    <th>№</th>
                    <th>Код брони</th>
                    <th>Клиент</th>
                    <th>Номер</th>
                    <th>Даты</th>
                    <th>Гостей</th>
                    <th>Сумма</th>
                    <th>Статус</th>
                    <th>Контакты</th>
                </tr>
                <?php foreach ($bookings as $index => $b): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($b['booking_id']) ?></td>
                        <td><?= htmlspecialchars($b['client_firstname']) ?> <?= htmlspecialchars($b['client_lastname']) ?></td>
                        <td><?= htmlspecialchars($b['room_name']) ?> (<?= htmlspecialchars($b['room_number']) ?>)</td>
                        <td><?= date('d.m.Y', strtotime($b['check_in'])) ?> – <?= date('d.m.Y', strtotime($b['check_out'])) ?></td>
                        <td><?= (int)$b['guests'] ?></td>
                        <td><?= number_format($b['total'], 0, ',', ' ') ?> ₽</td>
                        <td>
                            <select class="status-select" data-id="<?= $b['id'] ?>">
                                <option value="ожидает" <?= $b['status'] === 'ожидает' ? 'selected' : '' ?>>ожидает</option>
                                <option value="подтверждено" <?= $b['status'] === 'подтверждено' ? 'selected' : '' ?>>подтверждено</option>
                                <option value="отменено" <?= $b['status'] === 'отменено' ? 'selected' : '' ?>>отменено</option>
                            </select>
                        </td>
                        <td>
                            <div class="contact-info">
                                <i class="fas fa-phone"></i> <?= htmlspecialchars($b['client_phone']) ?><br>
                                <i class="fas fa-envelope"></i> <?= htmlspecialchars($b['client_email']) ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>

    <?php elseif ($page === 'reviews'): ?>
        <a href="?" class="back-btn">← Назад</a>
        <h2>Управление отзывами</h2>

        <?php if ($message): ?>
            <div class="message <?= $message['type'] ?>"><?= htmlspecialchars($message['text']) ?></div>
        <?php endif; ?>

        <?php if (empty($reviews)): ?>
            <p>Нет отзывов.</p>
        <?php else: ?>
            <div class="reviews-list">
                <?php foreach ($reviews as $review): ?>
                    <div class="review-card">
                        <div class="review-header">
                            <div class="review-author">
                                <h3><?= htmlspecialchars($review['firstname']) ?> <?= htmlspecialchars($review['lastname']) ?></h3>
                                <span class="review-email"><?= htmlspecialchars($review['email']) ?></span>
                            </div>
                            <div class="review-rating">
                                <?php
                                $rating = max(0, min(5, (int)$review['rating']));
                                for ($i = 1; $i <= 5; $i++):
                                    $isFull = $rating >= $i;
                                ?>
                                    <i class="<?= $isFull ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                        <div class="review-content">
                            <?= nl2br(htmlspecialchars($review['comment'])) ?>
                        </div>
                        <div class="review-footer">
                            <span class="review-date"><?= date('d.m.Y H:i', strtotime($review['created_at'])) ?></span>
                            <form method="GET" class="delete-form">
                                <input type="hidden" name="page" value="reviews">
                                <input type="hidden" name="delete_review" value="<?= $review['id_review'] ?>">
                                <button type="submit" class="delete-btn" title="Удалить" style="border: none; background: none;"
                                    onclick="return confirm('Вы уверены, что хотите удалить этот отзыв?')">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <script src="./js/admin-panel.js"></script>
</body>

</html>