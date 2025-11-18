<?php
require __DIR__ . "./vendor/helpers.php";
$user = currentUser();
$rooms = getRoomsListWithAmenities();
$reviews = getReviews();
?>
<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Круг Уюта</title>
    <link rel="shortcut icon" href="./icons/favicon.ico" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
    <link rel="stylesheet" href="./css/index.css">
    <link rel="stylesheet" href="./css/swiper.css">
</head>

<body>
    <div class="body-wraper">
        <header class="header ">
            <div class="header__container container">
                <a href="#" class="logo">
                    <img class="logo__img" src="./icons/логотип.svg" alt="Логотип">
                </a>
                <button class="header__burger-btn" id="burger">
                    <span></span><span></span><span></span>
                </button>
                <nav class="menu" id="menu">
                    <ul class="menu__list">
                        <li class="menu__item"><a class="menu__link" href="#about">О гостинице</a></li>
                        <li class="menu__item"><a class="menu__link" href="#gallery">Фотогалерея</a></li>
                        <li class="menu__item"><a class="menu__link" href="#rooms">Номера</a></li>
                        <li class="menu__item"><a class="menu__link" href="./booking.php">Бронирование</a></li>
                        <li class="menu__item"><a class="menu__link" href="#reviews">Отзывы</a></li>
                        <li class="menu__item"><a class="menu__link" href="#contacts">Контакты</a></li>
                        <li class="menu__item"><?= getAuthButton(); ?></li>
                    </ul>
                </nav>
            </div>
        </header>
    </div>
    <main>
        <section class="banner-section mb-5 d-flex justify-content-center" id="banner">
            <img src="./img/pholder-для-баннера.avif" loading="lazy" class="banner-img mb-5" alt="Изображение гостиницы">
            <div class="banner-content">
                <h1 class="slogan">КРУГ УЮТА — комфорт, который согревает душу</h1>
            </div>
        </section>
        <section id="about" class="about-section mb-5">
            <div class="container">
                <div class="col-12 mb-4">
                    <h2>О гостинице</h2>
                </div>
                <div class="row mt-5">
                    <div class="col-lg-6 col-md-12">
                        <p>Гостиница «Круг уюта», укрытая в вековом сосновом бору, дарит гостям тишину, наполненную шелестом хвои и пением птиц.
                            Её 18 номеров воплощают философию «меньше, но лучше»: льняное бельё с гречневой лузгой, глиняная посуда, светильники из ветвей и вода, очищенная сосновым углём.
                            Без телевизоров, но с книгами о природе и этюдниками, здесь утро начинается с медитации, день — с мастер-классов по эко-свечам, а вечер — с тихих бесед у костра.
                            «Круг уюта» не просто место для отдыха — это приглашение вернуться к себе и обрести гармонию с природой.</p>

                    </div>
                    <div class="col-lg-6 col-md-12">
                        <img src="./img/hotel.avif" class="img-custom" loading="lazy" alt="Гостиница">
                    </div>
                </div>
                <div class="services-section row">
                    <div class="col-lg-3 col-md-6 col-sm-12 ">
                        <div class="service-item">
                            <i class="fa-solid fa-mug-hot"></i>
                            <p>Кафе</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 ">
                        <div class="service-item">
                            <i class="fa-solid fa-spa"></i>
                            <p>Спа</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 ">
                        <div class="service-item">
                            <i class="fa-solid fa-person-swimming"></i>

                            <p>Бассейн</p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-12 ">
                        <div class="service-item">
                            <i class="fa-solid fa-place-of-worship"></i>
                            <p>Детская площадка</p>
                        </div>
                    </div>
                </div>
                <p class="text-center">Заселение — с 14:00. Выезд — до 12:00. Домашние животные не допускаются.</p>
            </div>
        </section>
        <section class="gallery-section my-5" id="gallery">
            <div class="container">
                <div class="col-12 mb-4">
                    <h2>Фотогалерея</h2>

                </div>
            </div>
            <div class="gallery-swiper-container">
                <div class="swiper gallerySwiper">
                    <div class="swiper-wrapper">
                        <div class="swiper-slide">
                            <img src="./img/hotel.avif" loading="lazy" alt="Интерьер 1">
                        </div>
                        <div class="swiper-slide">
                            <img src="./img/кафе.avif" loading="lazy" alt="Интерьер 2">
                        </div>
                        <div class="swiper-slide">
                            <img src="./img/типо-сауна.avif" loading="lazy" alt="Интерьер 3">
                        </div>
                        <div class="swiper-slide">
                            <img src="./img/pool-img.avif" loading="lazy" alt="Интерьер 4">
                        </div>
                    </div>
                    <div class="swiper-pagination"></div>
                    <div class="gallery-prev"><i class="fa-solid fa-chevron-left"></i></div>
                    <div class="gallery-next"><i class="fa-solid fa-chevron-right"></i></div>
                </div>
            </div>
        </section>
        <section id="rooms" class="rooms-section  ">
            <div class="container">
                <div class="col-12 mb-4">
                    <h2>Номера</h2>

                </div>
            </div>

            <div class="container-rooms py-5">
                <div class="rooms-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($rooms as $room): ?>
                            <div class="swiper-slide">
                                <div class="card-content">
                                    <div class="price-tag">
                                        <?= number_format($room['price'], 0, ',', ' ') ?> ₽ / ночь
                                    </div>
                                    <div class="room-number">№<?= htmlspecialchars($room['room_number']) ?></div>

                                    <div class="img-slider">
                                        <div class="img-slides">
                                            <?php foreach ($room['images'] as $img): ?>
                                                <div class="img-slide">
                                                    <img src="<?= htmlspecialchars($img) ?>" alt="Номер <?= $room['room_number'] ?>" loading="lazy" decoding="async">
                                                </div>
                                            <?php endforeach; ?>
                                            <?php if (empty($room['images'])): ?>
                                                <div class="img-slide">
                                                    <img src="https://via.placeholder.com/440x200/F2DDC6/4A1301?text=  Нет+изображения" alt="Нет фото">
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if (count($room['images']) > 1): ?>
                                            <div class="img-nav">
                                                <?php for ($i = 0; $i < count($room['images']); $i++): ?>
                                                    <div class="img-dot <?= $i === 0 ? 'active' : '' ?>"></div>
                                                <?php endfor; ?>
                                            </div>
                                            <div class="img-arrows">
                                                <div class="img-arrow img-left"><i class="fa fa-chevron-left"></i></div>
                                                <div class="img-arrow img-right"><i class="fa fa-chevron-right"></i></div>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="room-content">
                                        <h3 class="room-title"><?= htmlspecialchars($room['room_name']) ?></h3>
                                        <small class="room-description">
                                            <?= nl2br(htmlspecialchars($room['description'] ?? 'Описание отсутствует')) ?>
                                        </small>
                                        <p class="fw-bold">Номер со своей ванной комнатой</p>
                                        <p class="fs-5">В номере есть:</p>
                                        <?php if ($room['amenities']): ?>
                                            <div class="amenities">
                                                <?php foreach ($room['amenities'] as $am): ?>
                                                    <div class="amenity">
                                                        <img src="<?= htmlspecialchars($am['icon']) ?>">
                                                        <?php if (mb_strpos(mb_strtolower($am['name']), 'кровать') !== false): ?>
                                                            <span>
                                                                <?=
                                                                match ((int)$room['capacity']) {
                                                                    1 => 'Односп. кровать (160×200)',
                                                                    2 => 'Две односп. кровати (160×200)',
                                                                    3 => 'Три односп. кровати (160×200)',
                                                                    4 => '2 двуспальные кровати',
                                                                    5 => '2 двуспальные и 1 односпальная кровать',
                                                                    6 => '3 двуспальные кровати',
                                                                    default => $room['capacity'] . ' односпальных кроватей'
                                                                }
                                                                ?>
                                                            </span>
                                                        <?php else: ?>
                                                            <span><?= htmlspecialchars($am['name']) ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>

                                        <div class="room-capacity">
                                            Подойдёт для проживания <?= (int)$room['capacity'] ?>
                                            <?=
                                            match ((int)$room['capacity']) {
                                                1 => 'человека',
                                                2, 3, 4 => 'человек',
                                                default => 'человек'
                                            }
                                            ?>
                                        </div>
                                        <button class="book-btn btn-custom" data-room-name="<?= htmlspecialchars($room['room_name']) ?>" data-room-price="<?= $room['price'] ?>">Забронировать</button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Управление Swiper -->
                    <div class="swiper-controls">
                        <div class="swiper-btn swiper-prev"><i class="fa-solid fa-chevron-left"></i></div>
                        <div class="swiper-btn swiper-next"><i class="fa-solid fa-chevron-right"></i></div>
                    </div>
                </div>
            </div>
        </section>
        <section id="reviews" class="my-5">
            <div class="container ">
                <div class="col-12 mb-4">
                    <h2>Отзывы</h2>
                </div>
            </div>
            <div class="reviews-swiper-container">
                <div class="swiper reviewsSwiper">
                    <div class="swiper-wrapper">
                        <?php if (!empty($reviews)): ?>
                            <?php foreach ($reviews as $review): ?>
                                <div class="swiper-slide">
                                    <div class="review-content">
                                        <p><?= nl2br(htmlspecialchars($review['comment'])) ?></p>
                                        <div class="stars">
                                            <?php
                                            $rating = max(0, min(5, (int)$review['rating']));
                                            for ($i = 1; $i <= 5; $i++):
                                                $isFull = $rating >= $i;
                                            ?>
                                                <i class="<?= $isFull ? 'fa-solid' : 'fa-regular' ?> fa-star"></i>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="author"><?= htmlspecialchars($review['firstname']) ?> <?= htmlspecialchars($review['lastname']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="swiper-slide">
                                <div class="review-content text-center py-5">
                                    <p>Пока нет отзывов. Будьте первым, кто оставит свой отзыв!</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="swiper-fraction"></div>
                    <div class="reviews-prev"><i class="fa-solid fa-chevron-left"></i></div>
                    <div class="reviews-next"><i class="fa-solid fa-chevron-right"></i></div>
                </div>
            </div>
            <?php if ($user && $user['role'] !== 'admin'): ?>
                <div class="col-12">
                    <button class="write-btn btn-custom mb-5" id="openReviewBtn">Написать отзыв</button>
                </div>
            <?php endif; ?>
        </section>
        <section id="contacts" class="contacts-section my-5">
            <div class="container">
                <div class="col-12 mb-4">
                    <h2>Контакты</h2>

                </div>
                <div class="row">
                    <div class="col-lg-6 col-md-12 d-flex flex-column  justify-content-center align-items-start">
                        <ul>
                            <li class="mb-4">
                                <i class="fa-solid fa-phone me-2"></i>
                                <span>+8(800)353-535</span>
                            </li>
                            <li class="mb-4">
                                <i class="fa-solid fa-envelope me-2"></i>
                                <span>hannellograda-4392@gmail.com</span>
                            </li>
                            <li class="mb-4">
                                <i class="fa-solid fa-location-dot me-2"></i>
                                <span>Троллейная Ул., дом 152, кв. 74</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-6 col-md-12 d-flex flex-row  justify-content-center align-items-center">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m16!1m12!1m3!1d39055.44949955089!2d104.28393851219248!3d52.28032660698954!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!2m1!1z0LjRgNC60YPRgtGB0Log0KLRgNC-0LvQu9C10LnQvdCw0Y8g0KPQuy4sINC00L7QvCAxNTIsINC60LIuIDc0!5e0!3m2!1sru!2sru!4v1748404093533!5m2!1sru!2sru"
                            width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    </div>
                </div>
        </section>
    </main>
    <footer class="footer py-5 px-5 ">
        <div class="row justify-content-center g-5">
            <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column align-items-center g-5">
                <div class="mb-4 me-2">
                    <a href="./index.html" class="logo">
                        <img class="logo__img" src="./icons/логотип.svg" alt="Логотип">
                    </a>
                </div>
                <ul>
                    <li class="mb-4 me-5">
                        <i class="fa-solid fa-phone "></i>
                        <span>+8(800)353-535</span>
                    </li>
                    <li class="mb-4 me-5">
                        <i class="fa-solid fa-envelope"></i>
                        <span>hannellograda-4392@gmail.com</span>
                    </li>
                    <li class="mb-4 me-5">
                        <i class="fa-solid fa-location-dot"></i>
                        <span>Троллейная Ул., дом 152, кв. 74</span>
                    </li>
                </ul>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12 d-flex flex-column align-items-start mt-5 ms-0">
                <ul>
                    <li><a class="menu__link" href="#about">О гостинице</a>
                    </li>
                    <li><a class="menu__link" href="#gallery">Фотогалерея</a></li>
                    <li><a class="menu__link" href="#rooms">Номера</a></li>
                    <li><a class="menu__link" href="booking.html"> Бронирование</a></li>
                    <li><a class="menu__link" href="#reviews">Отзывы</a></li>
                    <li><a class="menu__link" href="#contacts">Контакты</a></li>
                </ul>
                <p class="mt-5 mb-0">ООО «КРУГ УЮТА». 2025 © Все права защищены.</p>
            </div>
        </div>
    </footer>
    <!-- Модальное окно отзыва -->
    <div id="reviewModal" class="modal" style="display: none;">
        <div class="modal-content">
            <button class="modal-close" id="close">
                <i class="fa-solid fa-xmark"></i>
            </button>
            <div class="form-header">
                <i class="fa-solid fa-pen"></i>
                <h3>Оставьте свой отзыв</h3>
            </div>
            <p class="form-subtitle">Поделитесь своими впечатлениями о визите</p>

            <form id="reviewForm" action="./vendor/actions/submit-review.php" method="POST">
                <?php if (isset($_SESSION['user']['id'])): ?>
                    <input type="hidden" name="user_id" value="<?= htmlspecialchars($_SESSION['user']['id']) ?>">
                <?php endif; ?>

                <div class="form-group">
                    <label for="rating">Оценка</label>
                    <div class="star-rating">
                        <!-- Stars are ordered from 5 to 1 with proper labels -->
                        <input type="radio" id="star5" name="rating" value="5" required>
                        <label for="star5"><i class="fa-solid fa-star"></i></label>

                        <input type="radio" id="star4" name="rating" value="4">
                        <label for="star4"><i class="fa-solid fa-star"></i></label>

                        <input type="radio" id="star3" name="rating" value="3">
                        <label for="star3"><i class="fa-solid fa-star"></i></label>

                        <input type="radio" id="star2" name="rating" value="2">
                        <label for="star2"><i class="fa-solid fa-star"></i></label>

                        <input type="radio" id="star1" name="rating" value="1">
                        <label for="star1"><i class="fa-solid fa-star"></i></label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="reviewText">Ваш отзыв</label>
                    <textarea id="reviewText" name="review" placeholder="Расскажите, что вам понравилось или не понравилось..." rows="6" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn btn-custom" onclick="closeReviewModal()">← Назад</button>
                    <button type="submit" class="btn btn-custom">Отправить отзыв →</button>
                </div>
            </form>
        </div>
    </div>
    <script src="./js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        window.APP = {
            isLoggedIn: <?php echo isset($_SESSION['user']['id']) ? 'true' : 'false'; ?>,
            loginUrl: './login.php',
            bookingUrl: './booking.php'
        };
    </script>
    <script defer src="./js/index.js"></script>
    <script src="./js/swiper.js"></script>
</body>

</html>