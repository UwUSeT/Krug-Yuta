document.addEventListener('DOMContentLoaded', function() {
    // ======= 1. Функции для работы с вкладками =======
    window.openTab = function(tabId, event = null) {
        // Скрыть все контенты и вкладки
        document.querySelectorAll('.tab-content, .tab').forEach(el => {
            el.classList.remove('active');
        });
        
        // Активировать нужную вкладку контента
        const tabContent = document.getElementById(tabId);
        if (tabContent) tabContent.classList.add('active');
        
        // Активировать соответствующую кнопку навигации
        let activeTab = null;
        if (event?.currentTarget) {
            activeTab = event.currentTarget;
        } else {
            // Поиск кнопки по атрибуту onclick
            document.querySelectorAll('.tab').forEach(tab => {
                const onclickAttr = tab.getAttribute('onclick');
                if (onclickAttr && onclickAttr.includes(`'${tabId}'`)) {
                    activeTab = tab;
                }
            });
        }
        
        if (activeTab) activeTab.classList.add('active');
        
        // Сохраняем активную вкладку в localStorage
        localStorage.setItem('activeTab', tabId);
    };
    
    // Автовосстановление активной вкладки при перезагрузке
    const savedTab = localStorage.getItem('activeTab');
    if (savedTab && document.getElementById(savedTab)) {
        window.openTab(savedTab);
    }
    
    // Автооткрытие вкладки с уведомлениями при наличии новых сообщений
    if (document.querySelector('.has-unread')) {
        window.openTab('messages');
    }
    
    // ======= 2. Формы и валидация =======
    // Блокировка ввода в поле даты рождения
    const birthdateInput = document.getElementById('birthdate');
    if (birthdateInput) {
        birthdateInput.addEventListener('keydown', function(e) {
            e.preventDefault();
        });
    }
    
    // Маска для телефона
    const phoneInput = document.getElementById('phone');
    if (phoneInput) {
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            
            // Нормализация первой цифры
            if (value && !['7', '8'].includes(value[0])) value = '7' + value;
            if (value && value[0] === '8') value = '7' + value.slice(1);
            
            // Форматирование номера
            let formatted = '';
            if (value.length > 0) formatted = '+7';
            if (value.length > 1) formatted += ' (' + value.slice(1, 4);
            if (value.length >= 4) formatted += ') ' + value.slice(4, 7);
            if (value.length >= 7) formatted += '-' + value.slice(7, 9);
            if (value.length >= 9) formatted += '-' + value.slice(9, 11);
            
            e.target.value = formatted;
        });
    }
    
    // ======= 3. Модальное окно деталей номера =======
    // Функция открытия деталей номера
    window.viewRoomDetails = function(roomId) {
        const roomsData = window.roomsData || [];
        const room = roomsData.find(r => r.id_room == roomId);
        if (!room) return;
        
        // Заполнение данных о номере
        document.getElementById('modal-room-name').textContent = room.room_name || '—';
        document.getElementById('modal-room-number').textContent = room.room_number || '—';
        document.getElementById('modal-guests').textContent = `${room.capacity || 1} гость${room.capacity == 1 ? '' : (room.capacity < 5 ? 'я' : 'ей')}`;
        document.getElementById('modal-price').textContent = room.price ? room.price.toLocaleString('ru-RU') : '0';
        document.getElementById('modal-description').textContent = room.description || 'Описание отсутствует';
        
        // Загрузка изображений
        const images = room.images?.length ? room.images : ['https://via.placeholder.com/350x150/F2DDC6/4A1301?text=        Нет+фото'];
        const swiperWrapper = document.querySelector('#modal-swiper .swiper-wrapper');
        if (swiperWrapper) {
            swiperWrapper.innerHTML = images.map(img => 
                `<div class="swiper-slide">
                    <img src="${img}" alt="Фото номера ${room.room_number || ''}" loading="lazy" decoding="async">
                </div>`
            ).join('');
        }
        
        // Настройка слайдера
        const container = document.getElementById('modal-swiper');
        if (container) {
            container.classList.toggle('single-image', images.length <= 1);
            
            if (window.modalSlider) window.modalSlider.destroy(true, true);
            
            if (images.length > 1 && typeof Swiper !== 'undefined') {
                window.modalSlider = new Swiper(container, {
                    loop: true,
                    pagination: { el: container.querySelector('.swiper-pagination'), clickable: true },
                    navigation: { 
                        nextEl: container.querySelector('.custom-next'),
                        prevEl: container.querySelector('.custom-prev')
                    },
                    slidesPerView: 1,
                    speed: 400
                });
            }
        }
        
        // Удобства номера
        const amenitiesGrid = document.getElementById('modal-amenities-grid');
        if (amenitiesGrid) {
            amenitiesGrid.innerHTML = room.amenities?.length ? 
                room.amenities.map(am => 
                    `<div class="amenity-item">
                        <img src="${am.icon || ''}" alt="${am.name || ''}" width="20">
                        ${am.name || ''}
                    </div>`
                ).join('') : 
                '<div class="amenity-item"><p>Удобства не указаны</p></div>';
        }
        
        // Показ модального окна
        const modal = document.getElementById('room-detail-modal');
        if (modal) modal.classList.add('active');
    };
    
    // Функция закрытия модального окна
    window.closeRoomDetailModal = function() {
        const modal = document.getElementById('room-detail-modal');
        if (modal) modal.classList.remove('active');
        
        if (window.modalSlider) {
            window.modalSlider.destroy(true, true);
            window.modalSlider = null;
        }
    };
    
    // Закрытие модального окна при клике вне его содержимого
    const modal = document.getElementById('room-detail-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                window.closeRoomDetailModal?.();
            }
        });
    }
    
    // Закрытие модального окна по нажатию Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal?.classList.contains('active')) {
            window.closeRoomDetailModal?.();
        }
    });
    
    // ======= 4. Бронирования =======
    // Функция отмены бронирования (ИСПРАВЛЕНА для работы со строковыми ID)
    window.cancelBooking = function(bookingId) {
        // Используем оригинальный ID без преобразования в число
        const cleanId = bookingId.toString().trim();
        
        if (!cleanId) {
            alert('Ошибка: пустой ID бронирования');
            return;
        }
        
        if (!confirm('Вы уверены, что хотите отменить бронирование #' + cleanId + '?')) return;
        
        // Ищем кнопку, которая была нажата
        const clickedButton = document.querySelector(
            `button[onclick*="cancelBooking('${cleanId}')"], ` +
            `button[onclick*='cancelBooking("${cleanId}")'], ` +
            `button[data-booking-id="${cleanId}"]`
        );
        
        // Отображаем индикатор загрузки на найденной кнопке
        if (clickedButton) {
            clickedButton.disabled = true;
            clickedButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Отмена...';
        }
        
        // Формируем корректный URL для запроса
        const baseUrl = window.location.origin + window.location.pathname;
        const cancelUrl = new URL('./vendor/actions/cancel-booking.php', baseUrl).href;
        
        fetch(cancelUrl, {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `booking_id=${encodeURIComponent(cleanId)}`,
            credentials: 'same-origin'
        })
        .then(response => {
            // Проверяем, является ли ответ JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                return response.text().then(text => {
                    throw new Error('Сервер вернул не JSON: ' + text.substring(0, 200));
                });
            }
        })
        .then(data => {
            if (data.success) {
                alert('Успех: ' + data.message);
                location.reload();
            } else {
                throw new Error(data.error || 'Неизвестная ошибка при отмене бронирования');
            }
        })
        .catch(error => {
            console.error('Ошибка при отмене бронирования:', error);
            
            // Восстанавливаем кнопку
            if (clickedButton) {
                clickedButton.disabled = false;
                clickedButton.innerHTML = 'Отменить';
            }
            
            alert('Ошибка: ' + (error.message || 'Произошла ошибка при отмене бронирования'));
        });
    };
    
    // ======= 5. Инициализация Swiper =======
    if (typeof Swiper !== 'undefined') {
        // Инициализация всех слайдеров на странице
        document.querySelectorAll('.room-slider').forEach(container => {
            const slidesCount = container.querySelectorAll('.swiper-slide').length;
            
            new Swiper(container, {
                loop: slidesCount > 1,
                pagination: {
                    el: container.querySelector('.swiper-pagination'),
                    clickable: true,
                },
                navigation: {
                    nextEl: container.querySelector('.custom-next'),
                    prevEl: container.querySelector('.custom-prev'),
                },
                slidesPerView: 1,
                spaceBetween: 0,
                speed: 400,
            });
        });
    }
    
    // Инициализация обработчиков для кнопок отмены
    document.querySelectorAll('button[onclick*="cancelBooking"], button[data-booking-id]').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            let bookingId = null;
            
            // Получаем ID из data-атрибута или из onclick
            if (this.hasAttribute('data-booking-id')) {
                bookingId = this.getAttribute('data-booking-id');
            } else {
                const onclickAttr = this.getAttribute('onclick');
                const match = onclickAttr.match(/cancelBooking\(['"]([^'"]+)['"]\)/i);
                if (match && match[1]) {
                    bookingId = match[1];
                }
            }
            
            if (bookingId) {
                window.cancelBooking(bookingId);
            } else {
                alert('Не удалось определить ID бронирования');
            }
        });
    });
});
