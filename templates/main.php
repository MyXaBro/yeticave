<main class="container">
 <section class="promo">
        <h2 class="promo__title">Нужен стафф для катки?</h2>
        <p class="promo__text">На нашем интернет-аукционе ты найдёшь самое эксклюзивное сноубордическое и горнолыжное снаряжение.</p>

     <ul class="promo__list">
            <?php
            /**
            * @var array $categories
             **/
            foreach ($categories as $key => $category):?>
            <li class="promo__item promo__item--boards">
                <a class="promo__link" href="/pages/all-lots.html"><?=$category['name_category'];?></a>
            </li>
            <?php endforeach;?>
        </ul>
    </section>
    <section class="lots">
        <div class="lots__header">
            <h2>Открытые лоты</h2>
        </div>
        <ul class="lots__list">
            <?php
            /**
                * @var array $goods
            **/
            foreach ($goods as $good):?>
            <li class="lots__item lot">
                <div class="lot__image">
                    <img src="<?=$good['image'];?>" width="350" height="260" alt="">
                </div>
                <div class="lot__info">
                    <span class="lot__category"><?=$good['name_category'];?></span>
                    <h3 class="lot__title"><a class="text-link" href="/pages/lot.html"><?=htmlspecialchars($good['names_lot']);?></a></h3>
                    <div class="lot__state">
                        <div class="lot__rate">
                            <span class="lot__amount">Стартовая цена</span>
                            <span class="lot__cost"><?=format_price(htmlspecialchars($good['start_price']));?></span>
                        </div>
                        <?php $result = time_counter(htmlspecialchars($good['time_finished']));?>
                        <div class="lot__timer timer" <?php if($result[0] < 1): ?> class="timer--finishing"<?php endif; ?>>
                        <?= "$result[0]: $result[1]"; ?>
                        </div>
                    </div>
                </div>
            </li>
            <?php endforeach;?>
        </ul>
    </section>
</main>