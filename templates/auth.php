<section class="content__side">
        <p class="content__side-info">Если у вас уже есть аккаунт, авторизуйтесь на сайте</p>

        <a class="button button--transparent content__side-button" href="form-authorization.html">Войти</a>
      </section>

      <main class="content__main">
      <h2 class="content__main-heading">Вход на сайт</h2>

<form class="form" action="auth.php" method="post" enctype="multipart/form-data" autocomplete="off">
    <div class="form__row">
    <label class="form__label" for="email">E-mail <sup>*</sup></label>

    <input class="form__input<?=(isset($errors['email'])) ? ' form__input--error' : NULL;?>" type="text" name="email" id="email" value="<?=(!isset($errors['email']) && !isset($errors['invalid'])) ? htmlspecialchars($auth['email']) : NULL?>" placeholder="Введите e-mail">
    <?=(isset($errors['email'])) ? '<p class="form__message">' . $errors['email'] . '</p>' : NULL;?>
    </div>

    <div class="form__row">
    <label class="form__label" for="password">Пароль <sup>*</sup></label>

    <input class="form__input<?=(isset($errors['password'])) ? ' form__input--error' : NULL;?>" type="password" name="password" id="password" value="<?=(!isset($errors['password']) && !isset($errors['invalid'])) ? htmlspecialchars($auth['password']) : NULL?>" placeholder="Введите пароль">
    <?=(isset($errors['password'])) ? '<p class="form__message">' . $errors['password'] . '</p>' : NULL;?>
    </div>

    <div class="form__row form__row--controls">
    <?php
        if(!empty($errors)) {
             (empty($errors['invalid'])) ? print('<p class="error-message">Пожалуйста, исправьте ошибки в форме</p>') : print('<p class="error-message">Вы ввели неверный email/пароль</p>') ;
        };
    ?>
    <input class="button" type="submit" name="" value="Войти">
    </div>
</form>
      </main>
