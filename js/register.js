
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('birthdate').addEventListener('keydown', (e) => {
    e.preventDefault(); // Блокирует всё, включая Backspace, стрелки и т.д.
});
    const phoneInput = document.getElementById('phone');

    if (!phoneInput) return;

    phoneInput.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, ''); // Оставляем только цифры

        // Убираем первую цифру, если это не 7 или 8 (для +7)
        if (value.length > 0 && !['7', '8'].includes(value[0])) {
            value = '7' + value;
        } else if (value.length > 0 && value[0] === '8') {
            value = '7' + value.slice(1); // заменяем 8 на 7
        }

        // Форматируем под +7 (XXX) XXX-XX-XX
        let formatted = '';
        if (value.length > 0) formatted = '+7';
        if (value.length > 1) formatted += ' (' + value.slice(1, 4);
        if (value.length >= 4) formatted += ') ' + value.slice(4, 7);
        if (value.length >= 7) formatted += '-' + value.slice(7, 9);
        if (value.length >= 9) formatted += '-' + value.slice(9, 11);

        e.target.value = formatted;
    });
});