<?php
require __DIR__ . "./vendor/helpers.php";
checkAuth();
$user = currentUser();
$bookings = getBookings();
$rooms = getRoomsListWithAmenities();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Личный кабинет</title>
  <link rel="shortcut icon" href="./icons/favicon.ico" type="image/x-icon">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300..800&family=Jost:wght@500..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
  <link rel="stylesheet" href="./css/private.css">
</head>

<body>
  <main>
    <div class="sidebar">
      <a href="./index.php"><i class="fa-solid fa-circle-left"></i></a>
      <div class="tab active" onclick="openTab('profile')"><i class="fa-solid fa-user"></i></div>
      <div class="tab" onclick="openTab('messages')"><i class="fa-solid fa-bell"></i></div>
      <div class="tab" onclick="openTab('bookings')"><i class="fa-solid fa-calendar"></i></div>
    </div>
    <!-- Основной контент -->
    <div class="main-content">
      <!-- Вкладка: Профиль -->
      <div id="profile" class="tab-content active">
        <h2>Мой профиль</h2>
        <!-- Информация о пользователе (выше формы) -->
        <div class="profile-info-container">
          <div class="profile-info-card">
            <h3>Ваши данные</h3>

            <?php if ($user): ?>
              <div class="info-grid">
                <div class="info-item">
                  <span class="info-label">Имя:</span>
                  <span class="info-value"><?= htmlspecialchars($user['firstname'] ?? '') ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Фамилия:</span>
                  <span class="info-value"><?= htmlspecialchars($user['lastname'] ?? '') ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Отчество:</span>
                  <span class="info-value"><?= htmlspecialchars($user['middlename'] ?? '') ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Дата рождения:</span>
                  <span class="info-value"><?= !empty($user['birth']) ? date('d.m.Y', strtotime($user['birth'])) : '' ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Email:</span>
                  <span class="info-value"><?= htmlspecialchars($user['email'] ?? '') ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Телефон:</span>
                  <span class="info-value"><?= htmlspecialchars($user['phone'] ?? '') ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Гражданство:</span>
                  <span class="info-value"><?= htmlspecialchars($user['country'] ?? '') ?></span>
                </div>
                <div class="info-item">
                  <span class="info-label">Пол:</span>
                  <span class="info-value">
                    <?= $user['gender'] === 'male' ? 'Мужской' : ($user['gender'] === 'female' ? 'Женский' : '') ?>
                  </span>
                </div>
                <div class="info-item">
                  <span class="info-label">Login:</span>
                  <span class="info-value"><?= htmlspecialchars($user['login'] ?? '') ?></span>
                </div>
              </div>
            <?php else: ?>
              <p class="info-empty">Данные пользователя не загружены</p>
            <?php endif; ?>
          </div>
        </div>

        <!-- Форма редактирования -->
        <div class="profile-form-container">
          <form action="./vendor/actions/updateuser.php" method="post">
            <div class="form-row">
              <div class="form-group">
                <label for="firstname">Имя</label>
                <input
                  type="text"
                  id="firstname"
                  name="firstname"
                  placeholder="Иван"
                  value="<?= htmlspecialchars($user['firstname'] ?? '') ?>"
                  required
                  <?= validationErrorAttr('firstname'); ?> />
                <?php if (hasValidationError('firstname')): ?>
                  <small><?= validationErrorMessage('firstname') ?></small>
                <?php endif; ?>
              </div>
              <div class="form-group">
                <label for="lastname">Фамилия</label>
                <input
                  type="text"
                  id="lastname"
                  name="lastname"
                  placeholder="Петров"
                  value="<?= htmlspecialchars($user['lastname'] ?? '') ?>"
                  required
                  <?= validationErrorAttr('lastname'); ?> />
                <?php if (hasValidationError('lastname')): ?>
                  <small><?= validationErrorMessage('lastname') ?></small>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="middlename">Отчество</label>
                <input
                  type="text"
                  id="middlename"
                  name="middlename"
                  placeholder="Наташкович"
                  value="<?= htmlspecialchars($user['middlename'] ?? '') ?>"
                  required
                  <?= validationErrorAttr('middlename'); ?> />
                <?php if (hasValidationError('middlename')): ?>
                  <small><?= validationErrorMessage('middlename') ?></small>
                <?php endif; ?>
              </div>
              <div class="form-group">
                <label for="birthdate">День рождения</label>
                <input
                  type="date"
                  id="birthdate"
                  name="birth"
                  value="<?= htmlspecialchars($user['birth'] ?? '') ?>"
                  required
                  <?= validationErrorAttr('birth'); ?> />
                <?php if (hasValidationError('birth')): ?>
                  <small><?= validationErrorMessage('birth') ?></small>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="email">Email</label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  placeholder="example@mail.com"
                  value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                  <?= validationErrorAttr('email'); ?>
                  required />
                <?php if (hasValidationError('email')): ?>
                  <small><?= validationErrorMessage('email') ?></small>
                <?php endif; ?>
              </div>
              <div class="form-group">
                <label for="phone">Мобильный телефон</label>
                <input
                  type="tel"
                  id="phone"
                  name="phone"
                  placeholder="+7 900 123-45-67" maxlength="18"
                  value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                  <?= validationErrorAttr('phone'); ?>
                  required />
                <?php if (hasValidationError('phone')): ?>
                  <small><?= validationErrorMessage('phone') ?></small>
                <?php endif; ?>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="citizenship">Гражданство</label>
                <select id="citizenship" name="country" required>
                  <option value="Россия" <?= ($user['country'] ?? '') === 'Россия' ? 'selected' : '' ?>>Россия</option>
                  <option value="Казахстан" <?= ($user['country'] ?? '') === 'Казахстан' ? 'selected' : '' ?>>Казахстан</option>
                  <option value="Беларусь" <?= ($user['country'] ?? '') === 'Беларусь' ? 'selected' : '' ?>>Беларусь</option>
                </select>
              </div>
              <div class="form-group">
                <label>Пол</label>
                <div class="gender-input-container">
                  <label class="gender-label">
                    <input type="radio" name="gender" class="gender-input" value="male" <?= ($user['gender'] ?? '') === 'male' ? 'checked' : '' ?> />
                    Мужской
                  </label>
                  <label class="gender-label">
                    <input type="radio" name="gender" class="gender-input" value="female" <?= ($user['gender'] ?? '') === 'female' ? 'checked' : '' ?> />
                    Женский
                  </label>
                </div>
              </div>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="login">Login</label>
                <input
                  type="text"
                  id="login"
                  name="login"
                  placeholder="example"
                  value="<?= htmlspecialchars($user['login'] ?? '') ?>"
                  <?= validationErrorAttr('login'); ?>
                  required />
                <?php if (hasValidationError('login')): ?>
                  <small><?= validationErrorMessage('login') ?></small>
                <?php endif; ?>
              </div>
              <div class="form-group">
                <label for="password">Пароль</label>
                <input
                  type="password"
                  id="password"
                  name="password"
                  placeholder="*******"
                  <?= validationErrorAttr('password'); ?>
                  required />
                <?php if (hasValidationError('password')): ?>
                  <small><?= validationErrorMessage('password') ?></small>
                <?php endif; ?>
              </div>
            </div>
            <button type="button" class="btn btn-custom">Изменить данные</button>
          </form>
          <?php clearValidation(); ?>
          <div class="actions">
            <form action="./vendor/actions/logout.php" method="post">
              <button type="submit" class="btn btn-custom">Выйти из учетной записи</button>
            </form>
            <form action="./vendor/actions/deleteuser.php" method="post" onsubmit="return confirm('Вы уверены? Все данные будут безвозвратно удалены!');">
              <input type="hidden" name="confirm" value="1">
              <button type="button" class="btn btn-custom">Удалить учетную запись</button>
            </form>
          </div>
        </div>
      </div>
      <!-- Вкладка: Сообщения -->
      <div id="messages" class="tab-content">
        <h2>Сообщения</h2>
        <!-- Отображение flash-сообщения об отзыве -->
        <?php if (isset($_SESSION['flash_success']) && stripos($_SESSION['flash_success'], 'отзыв') !== false): ?>
          <div class="message-item">
            <div class="message-icon">
              <i class="fa-solid fa-star"></i>
            </div>
            <div>
              <div class="message-header">
                <span class="message-title">Отзывы</span>
                <span class="message-date"><?= date('d.m.Y') ?></span>
              </div>
              <div class="message-body">
                <?= htmlspecialchars($_SESSION['flash_success']) ?><br>
                <small>Мы ценим ваше мнение. До новых встреч!</small>
              </div>
            </div>
          </div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <!-- Отображение flash-сообщения о бронировании -->
        <?php if (isset($_SESSION['flash_success']) && (stripos($_SESSION['flash_success'], 'бронирование') !== false || stripos($_SESSION['flash_success'], 'Бронирование') !== false)): ?>
          <div class="message-item">
            <div class="message-icon">
              <i class="fa-solid fa-calendar-check"></i>
            </div>
            <div>
              <div class="message-header">
                <span class="message-title">Бронирования</span>
                <span class="message-date"><?= date('d.m.Y') ?></span>
              </div>
              <div class="message-body">
                <?= htmlspecialchars($_SESSION['flash_success']) ?><br>
                <small>Детали бронирования можно посмотреть в разделе "Бронирования"</small>
              </div>
            </div>
          </div>
          <?php unset($_SESSION['flash_success']); ?>
        <?php endif; ?>

        <!-- Статичное сообщение, если нет flash-сообщений -->
        <?php if (!isset($_SESSION['flash_success'])): ?>
          <div class="message-item">
            <div class="message-icon">
              <i class="fa-solid fa-inbox"></i>
            </div>
            <div>
              <div class="message-header">
                <span class="message-title">Почтовый ящик</span>
                <span class="message-date"><?= date('d.m.Y') ?></span>
              </div>
              <div class="message-body">
                У вас нет новых сообщений
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>


      <div id="bookings" class="tab-content">
        <h2>Бронирования</h2>
        <?php if (empty($bookings)): ?>
          <div class="empty-state">У вас нет активных бронирований</div>
        <?php else: ?>
          <div class="bookings-list">
            <?php foreach ($bookings as $booking):
              $images = $booking['images'] ?? [];
              $dateRange = date('d', strtotime($booking['check_in'])) . '-' . date('d', strtotime($booking['check_out'])) . ' ' .
                str_replace(
                  ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                  ['января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
                  date('F Y', strtotime($booking['check_out']))
                );

              $statusMap = [
                'ожидает' => ['status-pending', 'Ожидает'],
                'подтверждено' => ['status-confirmed', 'Подтверждено'],
                'отменено' => ['status-cancelled', 'Отменено'],
                'завершено' => ['status-completed', 'Завершено']
              ];
              [$statusClass, $statusText] = $statusMap[$booking['status']] ?? ['status-other', ucfirst($booking['status'])];
            ?>
              <div class="booking-card">
                <div class="booking-card-image">
                  <img src="<?= !empty($images[0]) ? htmlspecialchars($images[0]) : 'https://via.placeholder.com/150x150/F2DDC6/4A1301?text=  Нет+фото' ?>"
                    alt="Номер <?= htmlspecialchars($booking['room_number'] ?? '') ?>" loading="lazy" decoding="async">
                </div>
                <div class="booking-card-details">
                  <div class="booking-card-header">
                    <span class="booking-id">#<?= htmlspecialchars($booking['booking_id'] ?? '') ?></span>
                    <span class="booking-status <?= $statusClass ?>"><?= htmlspecialchars($statusText) ?></span>
                  </div>
                  <div class="booking-card-info">
                    <p><i class="fa-solid fa-calendar-days"></i> <?= htmlspecialchars($dateRange) ?></p>
                    <p><i class="fa-solid fa-bed"></i> <?= htmlspecialchars($booking['room_name'] ?? '') ?></p>
                    <p><i class="fa-solid fa-credit-card"></i> <?= number_format($booking['total'] ?? 0, 0, ',', ' ') ?> ₽</p>
                  </div>
                  <div class="booking-card-actions">
                    <button class="btn-custom" onclick="viewRoomDetails(<?= $booking['room_id'] ?? 0 ?>)">Подробнее о номере</button>
                    <?php if (($booking['status'] ?? '') === 'ожидает'): ?>
                      <button class="btn-custom" onclick="cancelBooking('<?= htmlspecialchars($booking['booking_id']) ?>')">Отменить</button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </main>

  <!-- Модальное окно -->
  <div id="room-detail-modal" class="modal">
    <div class="modal-content">
      <div class="close-btn" onclick="closeRoomDetailModal()">&times;</div>
      <div class="modal-images">
        <div class="swiper modal-slider" id="modal-swiper">
          <div class="swiper-wrapper"></div>
          <div class="swiper-pagination"></div>
          <button class="custom-prev"><i class="fas fa-chevron-left"></i></button>
          <button class="custom-next"><i class="fas fa-chevron-right"></i></button>
        </div>
      </div>
      <div class="modal-specs">
        <h3><span id="modal-room-name">—</span></h3>
        <p><strong>Номер:</strong> <span id="modal-room-number">—</span></p>
        <p><strong>Вместимость:</strong> <span id="modal-guests">1 гость</span></p>
        <div class="room-price-modal">от <span id="modal-price">0</span> ₽ / ночь</div>
      </div>
      <div class="modal-description">
        <p><strong>Описание:</strong> <span id="modal-description">Описание отсутствует</span></p>
      </div>
      <div class="modal-amenities">
        <h3><strong>Удобства:</strong></h3>
        <div id="modal-amenities-grid" class="amenities-grid"></div>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
  <script>
    // Глобальные переменные для JavaScript
    window.roomsData = <?= json_encode(array_values($rooms ?? [])) ?>;
    window.config = {
      cancelBookingUrl: './vendor/actions/cancel-booking.php'
    };
  </script>

  <script src="./js/private.js"></script>
</body>

</html>