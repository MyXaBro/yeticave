<?php

require_once("helpers.php");
require_once("data.php");
require_once("function.php");
require_once("init.php");
require_once("models.php");
require_once("getwinner.php");

/**
 * @var string $connect ;
 **/

$categories = get_categories($connect);

$sql = get_query_list_lots('2022-07-12');

$res = mysqli_query($connect, $sql);
if ($res) {
    $goods = get_arrow($res);
} else {
    $error = mysqli_error($connect);
}

/**
 * @var object $lots
 * @var object $bet
 */
$get_winner = include_template("getwinner.php",[
    "lots" => $lots,
    "bet" => $bet
]);

/**
 * @var array $categories
 * @var array $goods
 * @var int $is_auth
 * @var string $user_name
 * @var string $search
 **/

$page_content = include_template("main.php", [
    "categories" => $categories,
    "goods" => $goods
]);

$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "categories" => $categories,
    "title" => "Главная",
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);
