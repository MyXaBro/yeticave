<?php
require_once("helpers.php");
require_once("function.php");
require_once("data.php");
require_once("init.php");
require_once("models.php");

/**
 * @var array $connect
 * @var int $is_auth
 * @var $lot
 */
$categories = get_categories($connect);

$header = include_template("header.php", [
    "categories" => $categories
]);
if ($is_auth) {
    $bets = get_bets($connect, $_SESSION["id"]);
}
$page_content = include_template("main-my-bets.php", [
    "categories" => $categories,
    "header" => $header,
    "bets" => $bets,
    "is_auth" => $is_auth

]);

/**
 * @var string $user_name
 */
$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "categories" => $categories,
    "title" => $lot["title"],
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);
