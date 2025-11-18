<?php

require __DIR__ . "./vendor/helpers.php";

checkGuest();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Регистрация пользователя</title>
  <link rel="shortcut icon" href="./icons/favicon.ico" type="image/x-icon">
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="./css/auth.css">
</head>

<body>
  <main>
    <div class="main-content">
      <h2>Регистрация</h2>
      <form action="vendor/actions/register.php" method="post">
        <div class="form-row">
          <div class="form-group">
            <label for="firstname">Имя</label>
            <input
              type="text"
              id="firstname"
              name="firstname"
              placeholder="Иван"
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
              <option value="Россия" selected>Россия</option>
              <option value="Казахстан">Казахстан</option>
              <option value="Беларусь">Беларусь</option>
            </select>
          </div>
          <div class="form-group">
            <label>Пол</label>
            <div class="gender-input-container">
              <label class="gender-label">
                <input type="radio" name="gender" class="gender-input" value="male" checked />
                Мужской
              </label>
              <label class="gender-label">
                <input type="radio" name="gender" class="gender-input" value="female" />
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

        <div class="actions">
          <button class="btn-submit" type="submit">Зарегистрироваться</button>
          <a href="login.php" class="link-registr">Вернуться</a>
        </div>
      </form>
    </div>
    <?php clearValidation(); ?>
  </main>
  <script src="./js/register.js"></script>
</body>

</html>