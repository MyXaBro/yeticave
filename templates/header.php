 <nav class="nav">
      <ul class="nav__list container">
          <?php
          /**
           * @var array $categories
           */
 ?>
      <?php foreach ($categories as $category): ?>
            <li class="nav__item">
                <a href="/pages/all-lots.html"><?= $category["name_category"]; ?></a>
            </li>
            <?php endforeach; ?>
      </ul>
    </nav>