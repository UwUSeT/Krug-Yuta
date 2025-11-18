document.addEventListener('DOMContentLoaded', () => {
    // Данные из PHP
    const roomsData = window.roomsData || [];
    const userId = window.userId || 0;

    // Состояние приложения
    const bookingState = {
        checkIn: '',
        checkOut: '',
        guests: 1,
        selectedRoom: null,
        selectedRoomId: null,
        selectedRoomPrice: 0
    };

    let modalSlider = null;

    // Запрет на ввод в полях даты
    ['check-in', 'check-out'].forEach(id => {
        const dateInput = document.getElementById(id);
        if (dateInput) {
            dateInput.addEventListener('keydown', (e) => {
                e.preventDefault();
            });
        }
    });

    // Вспомогательные функции
    const formatPrice = (price) => price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");

    const formatDate = (dateString) => {
        if (!dateString) return '';
        const [y, m, d] = dateString.split('-');
        return `${d.padStart(2, '0')}.${m.padStart(2, '0')}.${y}`;
    };

    const calculateNights = (checkIn, checkOut) => {
        if (!checkIn || !checkOut) return 0;
        const inDate = new Date(checkIn);
        const outDate = new Date(checkOut);
        return Math.max(1, Math.ceil((outDate - inDate) / (1000 * 60 * 60 * 24)));
    };

    const getRoomById = (id) => roomsData.find(room => room.id_room == id);

    // Управление шагами
    const updateProgress = (step) => {
        const progressFill = document.getElementById('progress-fill');
        if (progressFill) {
            progressFill.style.width = `${((step - 1) / 3) * 100}%`;
        }
    };

    const showStep = (step) => {
        document.querySelectorAll('.step').forEach(el => el.classList.remove('active'));
        const stepElement = document.getElementById(`step${step}`);
        if (stepElement) {
            stepElement.classList.add('active');
            updateProgress(step);
        }
    };

    // Обработчики кнопок
    document.getElementById('go-to-step2')?.addEventListener('click', () => {
        const checkIn = document.getElementById('check-in')?.value;
        const checkOut = document.getElementById('check-out')?.value;
        const guests = parseInt(document.getElementById('guests')?.value) || 1;

        if (!checkIn || !checkOut || guests < 1 || new Date(checkOut) <= new Date(checkIn)) {
            alert('Пожалуйста, заполните все поля корректно.');
            return;
        }

        bookingState.checkIn = checkIn;
        bookingState.checkOut = checkOut;
        bookingState.guests = guests;
        showStep(2);
    });

    document.getElementById('go-to-step3')?.addEventListener('click', () => {
        if (!bookingState.selectedRoom) {
            alert('Пожалуйста, выберите номер.');
            return;
        }
        showStep(3);
    });

    ['back-to-home', 'back-to-step1', 'back-to-step2'].forEach(id => {
        const btn = document.getElementById(id);
        if (btn) {
            btn.addEventListener('click', () => {
                if (id === 'back-to-home') window.location.href = './index.php';
                else showStep(parseInt(id.slice(-1)));
            });
        }
    });

    // Выбор номера
    document.querySelectorAll('.btn-select').forEach(btn => {
        btn.addEventListener('click', () => {
            const roomId = btn.dataset.id;
            const room = getRoomById(roomId);

            if (room) {
                // Убираем выделение с предыдущего номера
                document.querySelectorAll('.room-card').forEach(card => {
                    card.classList.remove('selected');
                });

                // Выделяем текущий номер
                const roomCard = btn.closest('.room-card');
                if (roomCard) roomCard.classList.add('selected');

                // Обновляем состояние
                bookingState.selectedRoom = room.room_name;
                bookingState.selectedRoomId = room.id_room;
                bookingState.selectedRoomPrice = room.price;

                // Обновляем данные на шаге 3
                const selectedRoomName = document.getElementById('selected-room-name');
                const bookingDates = document.getElementById('booking-dates');
                const nightsCount = document.getElementById('nights-count');
                const guestsCount = document.getElementById('guests-count');
                const totalAmount = document.getElementById('total-amount');

                if (selectedRoomName) selectedRoomName.textContent = room.room_name;
                if (bookingDates) {
                    bookingDates.textContent =
                        `${formatDate(bookingState.checkIn)} – ${formatDate(bookingState.checkOut)}`;
                }
                const nights = calculateNights(bookingState.checkIn, bookingState.checkOut);
                if (nightsCount) nightsCount.textContent = nights;
                if (guestsCount) guestsCount.textContent = bookingState.guests;
                if (totalAmount) totalAmount.textContent = `${nights * room.price} ₽`;
            }
        });
    });

    // Рабочая кнопка "Подробнее" со слайдером
    document.querySelectorAll('.btn-details').forEach(btn => {
        btn.addEventListener('click', () => {
            const roomId = btn.dataset.id;
            const room = getRoomById(roomId);
            if (!room) return;

            // Заполняем данные
            document.getElementById('modal-room-name').textContent = room.room_name || '—';
            document.getElementById('modal-room-number').textContent = room.room_number || '—';
            document.getElementById('modal-guests').textContent = room.capacity || '1';
            document.getElementById('modal-price').textContent = formatPrice(room.price || 0);
            document.getElementById('modal-description').textContent = room.description || 'Описание отсутствует';

            // Загружаем изображения
            const images = room.images || [];
            const placeholder = 'https://via.placeholder.com/350x150/F2DDC6/4A1301?text=Нет+фото';

            const swiperWrapper = document.querySelector('#modal-swiper .swiper-wrapper');
            if (swiperWrapper) {
                swiperWrapper.innerHTML = (images.length > 0 ? images : [placeholder]).map(img =>
                    `<div class="swiper-slide"><img src="${img}" alt="Фото номера ${room.room_name || ''}" loading="lazy" decoding="async"></div>`
                ).join('');
            }

            // Инициализируем слайдер
            const modalSwiper = document.getElementById('modal-swiper');
            if (modalSwiper) {
                // Удаляем предыдущий слайдер если есть
                if (modalSlider) {
                    modalSlider.destroy(true, true);
                    modalSlider = null;
                }

                // Показываем/скрываем навигацию в зависимости от количества изображений
                const hasMultipleImages = images.length > 1;
                modalSwiper.classList.toggle('single-image', !hasMultipleImages);

                // Инициализируем новый слайдер только если есть несколько изображений
                if (hasMultipleImages && typeof Swiper !== 'undefined') {
                    const paginationEl = modalSwiper.querySelector('.swiper-pagination');
                    const nextEl = modalSwiper.querySelector('.custom-next');
                    const prevEl = modalSwiper.querySelector('.custom-prev');

                    modalSlider = new Swiper(modalSwiper, {
                        loop: true,
                        pagination: paginationEl ? { el: paginationEl, clickable: true } : false,
                        navigation: nextEl && prevEl ? { nextEl, prevEl } : false,
                        slidesPerView: 1,
                        spaceBetween: 0,
                        speed: 600
                    });
                }
            }

            // Удобства
            const amenitiesGrid = document.getElementById('modal-amenities-grid');
            if (amenitiesGrid) {
                amenitiesGrid.innerHTML = room.amenities && room.amenities.length > 0 ?
                    room.amenities.map(am =>
                        `<div class="amenity-item">
                            <img src="${am.icon || ''}" alt="${am.name || ''}" width="20">
                            <span>${am.name || ''}</span>
                        </div>`
                    ).join('') :
                    '<p>Удобства не указаны</p>';
            }

            // Показываем модальное окно
            document.getElementById('room-detail-modal').classList.add('active');
        });
    });

    // Закрытие модальных окон
    document.querySelectorAll('.close-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const modal = btn.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
                // Удаляем слайдер при закрытии модального окна
                if (modalSlider) {
                    modalSlider.destroy(true, true);
                    modalSlider = null;
                }
            }
        });
    });

    // Закрытие по клику вне содержимого
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.classList.remove('active');
                // Удаляем слайдер при закрытии модального окна
                if (modalSlider) {
                    modalSlider.destroy(true, true);
                    modalSlider = null;
                }
            }
        });
    });

    // Отправка формы
    document.getElementById('booking-form')?.addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!bookingState.selectedRoomId) {
            alert('Пожалуйста, выберите номер.');
            return;
        }

        const nights = calculateNights(bookingState.checkIn, bookingState.checkOut);
        const total = nights * bookingState.selectedRoomPrice;

        try {
            const response = await fetch('./vendor/actions/process.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    check_in: bookingState.checkIn,
                    check_out: bookingState.checkOut,
                    guests: bookingState.guests,
                    room_id: bookingState.selectedRoomId,
                    nights: nights,
                    total: total,
                    id_user: userId
                })
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('modal-booking-id').textContent = data.booking_id || '—';
                document.getElementById('modal-amount').textContent = `${data.total || 0} ₽`;
                document.getElementById('success-modal').classList.add('active');

                setTimeout(() => {
                    window.location.href = './index.php';
                }, 2500);
            } else {
                throw new Error(data.error || 'Неизвестная ошибка');
            }
        } catch (error) {
            alert(`Ошибка при бронировании: ${error.message}`);
            console.error(error);
        }
    });

    // Инициализация слайдеров на странице
    if (typeof Swiper !== 'undefined') {
        document.querySelectorAll('.room-slider:not(.single-image)').forEach(container => {
            const slidesCount = container.querySelectorAll('.swiper-slide').length;
            if (slidesCount > 1) {
                const paginationEl = container.querySelector('.swiper-pagination');
                const nextEl = container.querySelector('.custom-next');
                const prevEl = container.querySelector('.custom-prev');

                new Swiper(container, {
                    loop: true,
                    pagination: paginationEl ? { el: paginationEl, clickable: true } : false,
                    navigation: nextEl && prevEl ? { nextEl, prevEl } : false,
                    slidesPerView: 1,
                    spaceBetween: 0,
                    speed: 400
                });
            }
        });
    };

    autoSelectPreselectedRoom();
});
