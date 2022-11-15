 <main>
    <nav class="nav">
      <ul class="nav__list container">
      <?php
      /**
       * @var array $categories
       */
      foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/pages/all-lots.html"><?= $category["name_category"]; ?></a>
            </li>
            <?php endforeach; ?>
      </ul>
    </nav>
    <section class="lot-item container">
            <h2>404 Страница не найдена</h2>
            <p>Данной страницы не существует на сайте.</p>
    </section>
  </main>

</div>

