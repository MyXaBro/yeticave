INSERT INTO yeticave.category(character_code, name_category)
VALUES
       ('boards', 'Доски и лыжи'),
       ('attachment', 'Крепления'),
       ('boots', 'Ботинки'),
       ('clotnes', 'Одежда'),
       ('various', 'Разное');

INSERT INTO users
    (email, user_name, user_password, contacts)
    VALUES
           ('rulez699@gmail.com', 'Vladislav', 'd569h', '+7(800)555-35-35'),
           ('shvk1995@mail.ru', 'Viktoria', '573gh', '+7(900)456-44-44'),
           ('damskiy_ugodnik2007@gmail.com', 'Tratatata', 'qwerty1234', 'LA');

INSERT INTO lots
        (names_lot, category_id, description, start_price, image, time_finished)
    VALUES
            ('2014 Rossignol District Snowboard', '1','Нежный снежный сноуборд','10999','img/lot-1.jpg','2022-07-09'),
            ('DC Ply Mens 2016/2017 Snowboard','1', 'Это как комикс, только сноуборд','159999','img/lot-2.jpg','2022-07-09'),
            ('Крепления Union Contact Pro 2015 года размер L/XL','2', 'Крепления как у твоей мамки','8000','img/lot-3.jpg','2022-07-09'),
            ('Ботинки для сноуборда DC Mutiny Charocal','3','Встань и лети','10999','img/lot-4.jpg','2022-06-02'),
            ('Куртка для сноуборда DC Mutiny Charocal', '4', 'Не голым же тебе кататься','7500', 'img/lot-5.jpg','2022-07-11'),
            ('Маска Oakley Canopy','5','Прекрасно в зимнюю погоду, можно даже банки грабить в этом, но это не точно','5400', 'img/lot-6.jpg','2022-07-10')
;

INSERT INTO bets
    (price_bet, user_id, lot_id)
VALUES
    ('8500','1','4');

INSERT INTO bets
(price_bet, user_id, lot_id)
VALUES
    ('9500','1','4');

/*Получаем список категорий*/
SELECT name_category FROM category;

/*Получить самые новые, открытые лоты.
  Каждый лот должен включать название, стартовую цену,
  ссылку на изображение, цену, название категории*/
SELECT names_lot, start_price, image, category.name_category FROM lots JOIN category
ON lots.category_id = category.id
WHERE lots.id = 4;

/*Обновить название лота по его идентификатору*/
UPDATE lots
SET names_lot='Ботинки для сноуборда обычные'
WHERE lots.id =4;

/*получить список ставок для лота по его идентификатору с сортировкой по дате*/
SELECT bets.data_bet,bets.price_bet, lots.names_lot, users.user_name FROM bets
    JOIN lots ON bets.lot_id = lots.id
    JOIN users ON  bets.user_id = users.id
    WHERE bets.id = 2
    ORDER BY bets.data_bet DESC;