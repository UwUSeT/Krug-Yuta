document.addEventListener("DOMContentLoaded", function () {
    // Мобильное меню
    const burgerBtn = document.getElementById("burger");
    const header = document.querySelector(".header");
    if (burgerBtn && header) {
        burgerBtn.addEventListener("click", function () {
            header.classList.toggle("open");
        });
    }
    // Закрыть меню при нажатии на Esc
    window.addEventListener('keydown', (e) => {
        if (e.key === "Escape" && header) {
            header.classList.remove("open");
        }
    });
    // Закрыть меню при клике вне его
    document.body.addEventListener('click', function (event) {
        if (!header) return;
        const menu = document.querySelector(".nav-links");
        const burger = document.getElementById("burger");
        // Проверяем, кликнули ли мы вне меню и бургера
        if (menu && !menu.contains(event.target) && !burger.contains(event.target) && header.classList.contains("open")) {
            header.classList.remove("open");
        }
    });
    // Обработчик бронирования
    function handleBookingAttempt(e) {
        e.preventDefault();
        const clickedElement = e.target.closest('.book-btn, a[href="./booking.php"]');
        if (!clickedElement) return;
        if (!window.APP.isLoggedIn) {
            if (confirm('Для бронирования необходимо войти в учетную запись. Перейти на страницу входа?')) {
                window.location.href = window.APP.loginUrl;
            }
            return;
        }
        // Если это ссылка на бронирование (не кнопка номера)
        if (clickedElement.matches('a[href="./booking.php"]')) {
            window.location.href = window.APP.bookingUrl;
            return;
        }
        // Для кнопок бронирования номеров
        if (clickedElement.classList.contains('book-btn')) {
            // Получаем данные о номере из ближайшего родителя с data-атрибутами
            const roomCard = clickedElement.closest('[data-room-id]');
            if (roomCard) {
                const roomId = roomCard.dataset.roomId;
                const roomName = roomCard.dataset.roomName;
                const roomPrice = roomCard.dataset.roomPrice;
                if (roomId && roomName && roomPrice) {
                    // Формируем URL с параметрами номера
                    const url = new URL(window.APP.bookingUrl, window.location.href);
                    url.searchParams.set('room_id', roomId);
                    url.searchParams.set('room_name', encodeURIComponent(roomName));
                    url.searchParams.set('room_price', roomPrice);
                    window.location.href = url.toString();
                    return;
                }
            }
        }
        // Если не удалось получить данные о номере
        window.location.href = window.APP.bookingUrl;
    }
    // Добавляем обработчики для кнопок "Забронировать"
    document.querySelectorAll('.book-btn').forEach(btn => {
        btn.addEventListener('click', handleBookingAttempt);
    });
    // Добавляем обработчик для ссылки на бронирование
    const bookingLink = document.querySelector('a[href="./booking.php"]');
    if (bookingLink) {
        bookingLink.addEventListener('click', handleBookingAttempt);
    }
    // Модальное окно для отзыва
    const writeReviewBtn = document.querySelector('.write-btn');
    const reviewModal = document.getElementById('reviewModal');
    const closeModalBtn = document.getElementById('close');
    if (writeReviewBtn && reviewModal && closeModalBtn) {
        writeReviewBtn.addEventListener('click', function (e) {
            e.preventDefault();
            if (!window.APP.isLoggedIn) {
                if (confirm('Для отправки отзыва необходимо войти в учетную запись. Перейти на страницу входа?')) {
                    window.location.href = window.APP.loginUrl;
                }
            } else {
                openReviewModal();
            }
        });
        closeModalBtn.addEventListener('click', closeReviewModal);
        // Закрытие модалки по клику вне окна
        reviewModal.addEventListener('click', function (e) {
            if (e.target === reviewModal) {
                closeReviewModal();
            }
        });
    }
    // Обработчик отправки формы отзыва
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function (e) {
            e.preventDefault();
            alert('Спасибо за ваш отзыв! Он будет опубликован после модерации.');
            closeReviewModal();
            this.reset();
            // Сброс звезд
            document.querySelectorAll('.star').forEach(star => {
                star.classList.remove('active');
            });
        });
    }
    // Обработчик выбора рейтинга
    const stars = document.querySelectorAll('.star');
    stars.forEach(star => {
        star.addEventListener('click', function () {
            const value = this.dataset.value;
            stars.forEach(s => {
                if (s.dataset.value <= value) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
        });
    });
});
// Функции для модального окна
function openReviewModal() {
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    const originalPadding = document.body.style.paddingRight || '';
    const originalOverflow = document.body.style.overflow || '';
    document.body.dataset.originalPadding = originalPadding;
    document.body.dataset.originalOverflow = originalOverflow;
    document.body.style.paddingRight = scrollbarWidth + 'px';
    document.body.style.overflow = 'hidden';
    document.getElementById('reviewModal').style.display = 'flex';
}
function closeReviewModal() {
    const body = document.body;
    body.style.paddingRight = body.dataset.originalPadding || '';
    body.style.overflow = body.dataset.originalOverflow || '';
    document.getElementById('reviewModal').style.display = 'none';
    // Очищаем сохраненные данные после закрытия
    delete body.dataset.originalPadding;
    delete body.dataset.originalOverflow;
}
