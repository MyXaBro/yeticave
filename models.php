<?php

/**
 * Формирует запрос для получения новых лотов
 * @param string $date
 * @return string
 */

function get_query_list_lots(string $date): string
{
    return /** @lang MySQL */
        "SELECT data_creation, names_lot, start_price, image, time_finished, name_category FROM lots 
            JOIN category ON lots.category_id = category.id
            WHERE data_creation > $date ORDER BY data_creation DESC";
}

function get_query_lots($id_lot): string
{
    return "SELECT names_lot, description, start_price, image, time_finished, category.name_category FROM lots
        JOIN category ON lots.category_id = category.id
        WHERE lots.id = $id_lot;";

}

/**
 * Формирует SQL-запрос для создания нового лота
 * @param integer $user_id id пользователя
 * @return string SQL-запрос
 */

function get_query_create_lot(int $user_id): string
{
    return "INSERT INTO lots(names_lot, category_id, description, start_price, step, time_finished, image, user_id)
        VALUES (?,?,?,?,?,?,?,$user_id);";
}

/**
 * Возвращает массив категорий
 * @param object $connect
 */

function get_categories($connect)
{
    if(!$connect){
        $error = mysqli_connect_error();
        return $error;
    }else{
        $sql = "SELECT id, character_code, name_category FROM category;";
        $result = mysqli_query($connect, $sql);
        if($result){
            $categories = get_arrow($result);
            return $categories;
        }else{
            $error = mysqli_error($connect);
            return $error;
        }
    }
}

/**
* Возвращает массив данных пользователей: адресс электронной почты и имя
* @param array $connect подключение к MySQL
* @return [Array | String] $users_data Двумерный массив с именами и емейлами пользователей
* или описание последней ошибки подключения
*/

function get_users_data(array $connect){
    if(!$connect){
        $error = mysqli_connect_error();
        return $error;
    }else{
        $sql = "SELECT email, user_name FROM users;";
        $result = mysqli_error($connect, $sql);
        if($result){
            $users_data = get_arrow($result);
            return $users_data;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * Формирует SQL-запрос для регистрации нового пользователя
 * @param integer $user_id id пользователя
 * @return string SQL-запрос
 */
function get_query_create_user() {
    return "INSERT INTO users (data_registration, email, user_password, user_name, contacts) VALUES (NOW(), ?, ?, ?, ?);";
}

/**
 * Возвращает массив данных пользователя: id адресс электронной почты имя и хеш пароля
 * @param array $connect Подключение к MySQL
 * @param string $email введенный адрес электронной почты
 * @return [Array | String] $users_data Массив с данными пользователя: id адресс электронной почты имя и хеш пароля
 * или описание последней ошибки подключения
 */

function get_login($connect, $email){
    if(!$connect){
        $error = mysqli_connect_error();
        return $error;
    }else{
        $sql = "SELECT id, email, user_name, user_password FROM users WHERE email = '$email'";
        $result = mysqli_query($connect, $sql);
        if($result){
            $users_data = get_arrow($result);
            return $users_data;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * @param $link mysqli ресурс соединения
 * @param string $words ключевые слова введеные пользователем
 * @param $limit
 * @param $offset
 * @return array|string $goods Двумерный массив лотов, в названии или описании которых есть такие слова или описание последней ошибки подключения
 */

function get_found_lots(mysqli $link, string $words, $limit, $offset){
    $sql = "SELECT lots.id, lots.names_lot, lots.start_price, lots.image, lots.time_finished, category.name_category FROM lots
    JOIN category ON lots.category_id = category.id
    WHERE MATCH(lots.names_lot, lots.description) AGAINST(?) ORDER BY data_creation DESC LIMIT $limit OFFSET $offset;";

    $stmt = mysqli_prepare($link, $sql);
    if ($stmt === false) {
        die(mysqli_error($link));
    }else{
        mysqli_stmt_bind_param($stmt, 's', $words);
    }

        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);


        if ($res) {
            return get_arrow($res);
    }
    /**
     * @var array $connect
     */
    $error = mysqli_error($connect);
    return $error;
}

/**
 * @param $link
 * @param $words
 * @return mixed
 */

function get_count_lots($link, $words){
    $sql = "SELECT COUNT(*) AS cnt FROM lots WHERE MATCH(names_lot, description) AGAINST(?);";
    $stmt = mysqli_prepare($link, $sql);
    if ($stmt === false) {
        die(mysqli_error($link));
    }else {
        mysqli_stmt_bind_param($stmt, 's', $words);
    }
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);

    if($res){
        $count = mysqli_fetch_assoc($res)["cnt"];
        return $count;
    }
    /**
     * @var array $connect
     */
    $error = mysqli_error($connect);
    return $error;
}

/**
 * @param $link
 * @param $sum
 * @param $user_id
 * @param $lot_id
 * @return bool|string
 */
function add_bet_database($link, $sum, $user_id, $lot_id) {
    $sql = "INSERT INTO bets (data_bet, price_bet, user_id, lot_id) VALUE (NOW(), ?, $user_id, $lot_id);";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, 'i', $sum);
    $res = mysqli_stmt_execute($stmt);
    if ($res) {
        return $res;
    }
    /**
     * @var array $connect
     */
    $error = mysqli_error($connect);
    return $error;
}

/**
 * Возвращает массив из десяти последних ставок на этот лот
 * @param array $connect
 * @param int $id_lot
 * @return [Array | String] $list_bets Ассоциативный массив со списком ставок на этот лот из базы данных
 * или описание последней ошибки подключения
 */
function get_bets_history ($connect, $id_lot) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT users.user_name, bets.price_bet, DATE_FORMAT(data_bet, '%d.%m.%y %H:%i') AS data_bet
        FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        WHERE lots.id=$id_lot
        ORDER BY bets.data_bet DESC LIMIT 10;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $list_bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $list_bets;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}
/**
 * Возвращает массив ставок пользователя
 * @param array $connect
 * @param int $id
 * @return [Array | String] $list_bets Ассоциативный массив ставок
 *  пользователя из базы данных или описание последней ошибки подключения
 */
function get_bets ($connect, $id) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT DATE_FORMAT(bets.data_bet, '%d.%m.%y %H:%i') AS data_bet, bets.price_bet, lots.names_lot, lots.description, lots.image, lots.time_finished, lots.id, category.name_category
        FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        JOIN category ON lots.category_id=category.id
        WHERE bets.user_id=$id
        ORDER BY bets.data_bet DESC;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $list_bets = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $list_bets;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * Возвращает массив лотов у которых истек срок окончания торгов и нет победетеля
 * @param array $connect Подключение к MySQL
 * @return [Array | String] $lots массив лотов
 * или описание последней ошибки подключения
 */
function get_lot_date_finish ($connect) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT * FROM lots
        where winner_id IS NULL && time_finished <= now();";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $lots;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * Возвращает последнюю ставку на лот
 * @param array $connect Подключение к MySQL
 * @param int $id ID лота
 * @return [Array | String] $bet массив с описанием ставки
 * или описание последней ошибки подключения
 */
function get_last_bet ($connect, $id) {
    if (!$connect) {
        $error = mysqli_connect_error();
    } else {
        $sql = "SELECT * FROM bets
        where lot_id = $id
        ORDER BY data_bet DESC LIMIT 1;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $bet = get_arrow($result);
            return $bet;
        }
        $error = mysqli_error($connect);
    }
    return $error;
}

/**
 * Записывает в таблицу лотов в базе данных ID победителя торгов по конкретному лоту
 * @param array $connect Подключение к MySQL
 * @param int $winer_id ID победителя торгов
 * @param int $lot_id ID лота
 * @return [Bool | String] $res Возвращает true в случае успешной записи
 * или описание последней ошибки подключения
 */
function add_winner ($connect, $winer_id, $lot_id) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "UPDATE lots SET winner_id=$winer_id WHERE id = $lot_id";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            return $result;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

function viewing_lots ($connect) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT id, title, winner_id FROM lots WHERE winner_id !=0;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $lots = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $lots;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * Возвращает email, телефон и имя пользователя по id
 * @param array $connect Подключение к MySQL
 * @param int $id ID пользователя
 * @return [Array | String] $user_date массив
 * или описание последней ошибки подключения
 */
function get_user_contacts ($connect, $id) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT users.user_name, users.email, users.contacts FROM users
        WHERE id=$id;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $user_date = get_arrow($result);
            return $user_date;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * Возвращает имя пользователя и название лота для письма
 * @param array $connect Подключение к MySQL
 * @param int $id ID лота
 * @return [Array | String] $data массив
 * или описание последней ошибки подключения
 */
function get_user_win ($connect, $id) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT lots.id, lots.names_lot, users.user_name, users.contacts
        FROM bets
        JOIN lots ON bets.lot_id=lots.id
        JOIN users ON bets.user_id=users.id
        WHERE lots.id = $id;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $data = get_arrow($result);
            return $data;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}

/**
 * Возвращает контакты владельца лота
 * @param array $connect Подключение к MySQL
 * @param int $id ID лота
 * @return [Array | String] $contacts массив
 * или описание последней ошибки подключения
 */
function get_user_tell ($connect, $id) {
    if (!$connect) {
        $error = mysqli_connect_error();
        return $error;
    } else {
        $sql = "SELECT  users.contacts AS tell FROM lots
        JOIN users ON users.id=lots.user_id
        WHERE lots.id = $id;";
        $result = mysqli_query($connect, $sql);
        if ($result) {
            $contacts = get_arrow($result);
            return $contacts;
        }
        $error = mysqli_error($connect);
        return $error;
    }
}
