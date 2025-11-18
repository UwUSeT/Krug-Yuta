<?php
require __DIR__ . "./vendor/helpers.php";
checkGuest();
?>

<!DOCTYPE html>
<html lang="ru">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Авторизация</title>
  <link rel="shortcut icon" href="./icons/favicon.ico" type="image/x-icon">
  <link
    href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&family=Jost:ital,wght@0,100..900;1,100..900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="./css/auth.css" />
</head>

<body>
  <main>
    <div class="main-content">
      <h2>Авторизация</h2>

      <?php if (hasMessage('error')): ?>
        <div class="notice error" style="color: red; margin-bottom: 15px;"><?= getMessage('error') ?></div>
      <?php endif; ?>

      <form action="./vendor/actions/login.php" method="post">
        <div class="form-row">
          <div class="form-group">
            <label for="login">Логин</label>
            <input
              type="text"
              id="login"
              name="login"
              placeholder="papka12G"
              required
              <?= validationErrorAttr('login'); ?> />
            <?php if (hasValidationError('login')): ?>
              <small><?= validationErrorMessage('login') ?></small>
            <?php endif; ?>
          </div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label for="password">Пароль</label>
            <input
              type="password"
              id="password"
              name="password"
              placeholder="*******"
              minlength="6"
              required
              <?= validationErrorAttr('password'); ?> />
            <?php if (hasValidationError('password')): ?>
              <small><?= validationErrorMessage('password') ?></small>
            <?php endif; ?>
          </div>
        </div>

        <div class="actions">
          <button class="btn-submit" type="submit">Войти</button>
          <a href="register.php" class="link-registr">Нет аккаунта? Создать аккаунт</a>
        </div>
      </form>
    </div>
    <?php clearValidation(); ?>
  </main>
</body>

</html>