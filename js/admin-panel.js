document.addEventListener('DOMContentLoaded', function() {
    initStatusUpdates();
    initFileUploads();
    initDeleteButtons();
});

function initStatusUpdates() {
    const selects = document.querySelectorAll('.status-select');
    if (selects.length === 0) return;
    
    selects.forEach(select => {
        select.addEventListener('change', handleStatusChange);
    });
}

async function handleStatusChange() {
    const id = this.dataset.id;
    const status = this.value;
    
    try {
        const response = await fetch('', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `action=update_booking_status&id=${encodeURIComponent(id)}&status=${encodeURIComponent(status)}`
        });
        
        const data = await response.json();
        if (!data.success) throw new Error(data.error || 'Ошибка сервера');
        
        // Визуальная обратная связь
        showSuccessFeedback(this.parentElement);
    } catch (error) {
        showError(error.message);
        console.error('Ошибка обновления статуса:', error);
    }
}

function showSuccessFeedback(element) {
    element.style.backgroundColor = '#d4edda';
    element.style.transition = 'background-color 0.5s';
    
    setTimeout(() => {
        element.style.backgroundColor = '';
    }, 1500);
}

function initFileUploads() {
    document.querySelectorAll('.custom-file-upload').forEach(container => {
        const elements = {
            input: container.querySelector('.file-input'),
            button: container.querySelector('.custom-file-button'),
            fileName: container.querySelector('.file-name')
        };
        
        if (!elements.input || !elements.button || !elements.fileName) return;
        
        setupFileUpload(container, elements);
    });
}

function setupFileUpload(container, { input, button, fileName }) {
    button.addEventListener('click', () => input.click());
    
    input.addEventListener('change', () => {
        fileName.textContent = input.files.length ? input.files[0].name : 'Не выбран ни один файл';
        
        // Добавляем визуальную обратную связь
        if (input.files.length) {
            container.style.borderColor = '#4A1301';
        } else {
            container.style.borderColor = '';
        }
    });
    
    // Сброс стилей при фокусе
    input.addEventListener('focus', () => {
        container.style.borderColor = '#4A1301';
    });
    
    input.addEventListener('blur', () => {
        container.style.borderColor = '';
    });
}

function showError(message) {
    alert(`Ошибка: ${message}`);
    location.reload();
}
function initDeleteButtons() {
    // Добавляем обработчик для всех кнопок удаления
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function(e) {
            // Подтверждение уже есть в onclick, поэтому здесь ничего не делаем
        });
    });
}
