 <main>
     <nav class="nav">
         <ul class="nav__list container">
             <?php
             /**
             * @var array $categories
             **/?>
             <?php foreach ($categories as $category): ?>
                 <li class="nav__item">
                     <a href="/pages/all-lots.html"><?= $category["name_category"]; ?></a>
                 </li>
             <?php endforeach; ?>
         </ul>
     </nav>
     <?php $classname = isset($errors) ? "form--invalid" : ""; ?>
    <form class="form container <?=$classname;?>" action="main-sign-up.php" method="post" autocomplete="off"> <!-- form
    --invalid -->
      <h2>Регистрация нового аккаунта</h2>
        <?php $classname = isset($errors["email"]) ? "form__item--invalid" : ""; ?>
      <div class="form__item <?=$classname?>">
        <label for="email">E-mail <sup>*</sup></label>
        <input id="email" type="text" name="email" placeholder="Введите e-mail">
        <span class="form__error"><?= $errors["email"]; ?>></span>
      </div>
        <?php $classname = isset($errors["password"]) ? "form__item--invalid" : ""; ?>
        <div class="form__item <?=$classname?>">
      <div class="form__item">
        <label for="password">Пароль <sup>*</sup></label>
        <input id="password" type="password" name="password" placeholder="Введите пароль">
        <span class="form__error"><?=$errors["password"];?></span>
      </div>
            <?php $classname = isset($errors["name"]) ? "form__item--invalid" : ""; ?>
            <div class="form__item <?=$classname?>">
        <label for="name">Имя <sup>*</sup></label>
        <input id="name" type="text" name="name" placeholder="Введите имя">
        <span class="form__error"><?=$errors["name"];?></span>
      </div>

            <?php $classname = isset($errors["message"]) ? "form__item--invalid" : ""; ?>
            <div class="form__item <?=$classname?>">
        <label for="message">Контактные данные <sup>*</sup></label>
        <textarea id="message" name="message" placeholder="Напишите как с вами связаться"></textarea>
        <span class="form__error"><?=$errors["message"];?></span>
      </div>
      <span class="form__error form__error--bottom">Пожалуйста, исправьте ошибки в форме.</span>
      <button type="submit" class="button">Зарегистрироваться</button>
      <a class="text-link" href="#">Уже есть аккаунт</a>
    </form>
  </main>