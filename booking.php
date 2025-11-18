<?php
require __DIR__ . "/vendor/helpers.php";
checkAuth();
$user = currentUser();
$rooms = getRoomsListWithAmenities();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Бронирование - КРУГ УЮТА</title>
    <link rel="shortcut icon" href="./icons/favicon.ico" type="image/x-icon">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="./css/booking.css">
    <script>
        // Передаем данные в глобальные переменные
        window.roomsData = <?= json_encode(array_values($rooms)) ?>;
        window.userId = <?= (int)($user['id_user'] ?? 0) ?>;
    </script>
</head>

<body>
    <form id="booking-form">
        <main class="booking-container">
            <div class="booking-header">
                <h1>Бронирование номера</h1>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progress-fill"></div>
            </div>

            <!-- Шаг 1 -->
            <div id="step1" class="step active">
                <h2>Выберите даты и гостей</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Заезд:<input type="date" id="check-in" required></label>
                    </div>
                    <div class="form-group">
                        <label>Выезд:<input type="date" id="check-out" required></label>
                    </div>
                </div>
                <div class="form-group">
                    <label>Гостей:<input type="number" id="guests" value="1" min="1" max="3" required></label>
                </div>
                <div class="btn-group">
                    <button type="button" id="back-to-home">← Назад</button>
                    <button type="button" id="go-to-step2">Далее →</button>
                </div>
            </div>

            <!-- Шаг 2 -->
            <div id="step2" class="step">
                <h2>Выберите номер</h2>
                <div class="room-grid">
                    <?php foreach ($rooms as $room):
                        $images = $room['images'] ?: ['https://via.placeholder.com/350x150/F2DDC6/4A1301?text=Нет+фото'];
                        $hasSlider = count($images) > 1;
                    ?>
                        <div class="room-card" data-id="<?= $room['id_room'] ?>">
                            <div class="swiper room-slider <?= $hasSlider ? '' : 'single-image' ?>">
                                <div class="swiper-wrapper">
                                    <?php foreach ($images as $img): ?>
                                        <div class="swiper-slide">
                                            <img src="<?= htmlspecialchars($img) ?>"
                                                alt="Номер <?= htmlspecialchars($room['room_number']) ?>"
                                                loading="lazy" decoding="async">
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php if ($hasSlider): ?>
                                    <div class="swiper-pagination"></div>
                                    <div class="custom-next"><i class="fa-solid fa-chevron-right"></i></div>
                                    <div class="custom-prev"><i class="fa-solid fa-chevron-left"></i></div>
                                <?php endif; ?>
                            </div>
                            <div class="room-info">
                                <h3><?= htmlspecialchars($room['room_name']) ?></h3>
                                <p>Номер: <?= htmlspecialchars($room['room_number']) ?></p>
                                <p>Вместимость: <?= (int)$room['capacity'] ?>
                                    <?= match ((int)$room['capacity']) {
                                        1 => 'гость',
                                        2, 3, 4 => 'гостя',
                                        default => 'гостей'
                                    } ?>
                                </p>
                                <p>от <?= number_format($room['price'], 0, ',', ' ') ?> ₽/ночь</p>
                                <div class="room-actions">
                                    <button type="button" class="btn-details"
                                        data-id="<?= $room['id_room'] ?>">Подробнее</button>
                                    <button type="button" class="btn-select"
                                        data-id="<?= $room['id_room'] ?>"
                                        data-name="<?= htmlspecialchars($room['room_name']) ?>"
                                        data-price="<?= $room['price'] ?>">Выбрать</button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="btn-group">
                    <button type="button" id="back-to-step1">← Назад</button>
                    <button type="button" id="go-to-step3">Далее →</button>
                </div>
            </div>

            <!-- Шаг 3 -->
            <div id="step3" class="step">
                <h2>Подтверждение</h2>
                <div class="payment-summary">
                    <p><strong>Номер:</strong> <span id="selected-room-name">Не выбран</span></p>
                    <p><strong>Даты:</strong> <span id="booking-dates">—</span></p>
                    <p><strong>Ночей:</strong> <span id="nights-count">0</span></p>
                    <p><strong>Гостей:</strong> <span id="guests-count">0</span></p>
                    <hr>
                    <p><strong>Итого:</strong> <span id="total-amount">0 ₽</span></p>
                </div>
                <h3>Данные карты</h3>
                <div class="payment-fields">
                    <input type="text" id="card-number" placeholder="Номер карты" maxlength="19">
                    <input type="text" id="card-expiry" placeholder="ММ/ГГ" maxlength="5">
                    <input type="text" id="card-cvv" placeholder="CVV" maxlength="3">
                </div>
                <div class="btn-group">
                    <button type="button" id="back-to-step2">← Назад</button>
                    <button type="submit">Подтвердить</button>
                </div>
            </div>
        </main>
    </form>

    <!-- Модальное окно: Детали номера -->
    <div id="room-detail-modal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <div class="modal-images">
                <div class="swiper modal-slider" id="modal-swiper">
                    <div class="swiper-wrapper">
                        <!-- Изображения будут добавлены динамически -->
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="custom-prev"><i class="fas fa-chevron-left"></i></div>
                    <div class="custom-next"><i class="fas fa-chevron-right"></i></div>
                </div>
            </div>
            <div class="modal-specs">
                <h3><span id="modal-room-name">—</span></h3>
                <p><strong>Номер:</strong> <span id="modal-room-number">—</span></p>
                <p><strong>Гостей:</strong> <span id="modal-guests">1</span></p>
                <p class="room-price-modal">от <span id="modal-price">0</span> ₽/ночь</p>
            </div>
            <div class="modal-description">
                <p><strong>Описание:</strong> <span id="modal-description">—</span></p>
            </div>
            <div class="modal-amenities">
                <h3>Удобства:</h3>
                <div id="modal-amenities-grid" class="amenities-grid"></div>
            </div>
        </div>
    </div>

    <!-- Модальное окно успеха -->
    <div id="success-modal" class="modal success-modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <i class="fas fa-check-circle"></i>
            <h2>Успешно!</h2>
            <p><strong>Бронь:</strong> <span id="modal-booking-id">—</span></p>
            <p><strong>Сумма:</strong> <span id="modal-amount">0 ₽</span></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script src="./js/booking.js"></script>
</body>

</html>