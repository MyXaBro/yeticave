<?php

function format_price($price)
{
    $price = ceil($price);
    if($price >= 1000){
        $price = number_format($price,0, '', ' ');

    }
    return $price . ' ' . '₽';
};



function time_counter($date)
{

    date_default_timezone_set('Europe/Moscow');
    $one_date = date_create($date);
    $two_date = date_create('now');
    $diff = date_diff($one_date,$two_date);
    $format_diff = date_interval_format($diff, "%d %H %I");
    $arr = explode(" ", $format_diff);


    $hours = $arr[0] * 24 + $arr[1];
    $minutes = floor($arr[2]);
    $hours = str_pad($hours, 2, "0", STR_PAD_LEFT);
    $minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT);
    $result[] = $hours;
    $result[] = $minutes;

    return $result;
};

/**
 * Создает подготовленное выражение на основе готового SQL запроса и
 * переданных данных
 *
 * @param $link mysqli Ресурс соединения
 * @param $sql string SQL запрос с плейсхолдерами вместо значений
 * @param array $data Данные для вставки на место плейсхолдеров
 *
 * @return mysqli_stmt|void $stmt Подготовленное выражение
 */

function db_get_prepare_stmt_version(mysqli $link, string $sql, array $data = []){
    $stmt = mysqli_prepare($link, $sql);

    if($stmt === false){
        $errorMsg = 'Не удалось инициализировать подготовленное выражение ' . mysqli_error($link);
        die($errorMsg);
    }

    if($data){
        $types = '';
        $stmt_data = [];

        foreach($data as $key => $value){
            $type = 's';

            if(is_int($value)){
                $type = 'i';
            }else if (is_double($value)){
                $type = 'd';
            }

            if($type){
                $types .= $type;
                $stmt_data[] = $value;
             }
        }

        $values = array_merge([$stmt, $types], $stmt_data);
        mysqli_stmt_bind_param(...$values);

        if(mysqli_errno($link) > 0 ){
            $errorMsg = 'Не удалось связать подготовленное выражение с параметрами: ' . mysqli_error($link);
            die($errorMsg);
        }
    }
    return $stmt;
};

/**
 * Возвращает массив из объекта результата запроса
 * @param object $result_query mysqli Результат запроса к базе данных
 * @return array
 */

function get_arrow ($result_query) {
    $arrow = null;
    $row = mysqli_num_rows($result_query);
    if ($row === 1) {
        $arrow = mysqli_fetch_assoc($result_query);
    } else if ($row > 1) {
        $arrow = mysqli_fetch_all($result_query, MYSQLI_ASSOC);
    }

    return $arrow;
}

/**
 * Валидирует поле категории, если такой категории нет в списке
 * возвращает сообщение об этом
 * @param int $id категория, которую ввел пользователь в форму
 * @param array $allowed_list Список существующих категорий
 * @return string Текст сообщения об ошибке
 */

function validate_category(int $id, array $allowed_list)
{
    if(!in_array($id, $allowed_list)){
        return "Указана несуществующая категория";
    }
}

/**
 * Проверяет что содержимое поля является числом больше нуля
 * @param string $num число которое ввел пользователь в форму
 * @return string|null Текст сообщения об ошибке
 */

function validate_number(string $num)
{
    if(!empty($num)){
        $num *= 1;
        if(is_int($num) && $num > 0){
            return NULL;
        }
        return 'Содержимое поля должно быть целым числом и быть больше нуля';
    }
};

/**
 * Проверяет что дата окончания торгов не меньше одного дня
 * @param string $date дата которую ввел пользователь в форму
 * @return string Текст сообщения об ошибке
 */

function validate_date(string $date)
{
    if(is_date_valid($date)){
        $now = date_create("now");
        $d = date_create($date);
        $diff = date_diff($d, $now);
        $interval = date_interval_format($diff, "%d");

        if($interval < 1){
            return "Дата должна быть больше текущей не менее чем на один день";
        };
    }else{
        return "Содержимое поля «дата завершения» должно быть датой в формате «ГГГГ-ММ-ДД»";
    }
};

/**
 *  Проверяет что содержимое поля является корректным адресом электронной почты
 * @param string $email
 */

function validate_email($email){
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        return "Email должен быть корректным";
    }
}

/**
 * Проверяет, что содержимое поля укладывается в допустимый формат
 * @param string $value содержимое поля
 * @param int $min минимальное количество символов
 * @param int $max максимальное количество символов
 */

function validate_length($value, $min, $max){
    if($value){
        $len = strlen($value);
        if($len < $min or $len > $max){
            return "Значение должно быть от $min до $max символов";
        }
    }
};