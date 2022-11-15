<?php

require_once("helpers.php");
require_once("data.php");
require_once("function.php");
require_once("init.php");
require_once("models.php");

/**
 * @var array $connect
 */
$categories = get_categories($connect);
$search = null;
if(isset($search)) {
    $search = htmlspecialchars($_GET["search"]);
}
$goods = null;
$pages = null;
$pages_count = null;
$cur_page = null;

if($search){
    $items_count = get_count_lots($connect, $search);
    $cur_page = $_GET["page"] ?? 1;
    $page_items = 9;
    $pages_count = ceil($items_count / $page_items);
    $offset = ($cur_page - 1) * $page_items;
    $pages = range(1, $pages_count);

    $goods = get_found_lots($connect, $search, $page_items, $offset);
}

$header = include_template("header.php",[
   "categories" => $categories
]);

$page_content = include_template("main-search.php",[
    "categories" => $categories,
    "search" => $search,
    "goods" => $goods,
    "header" => $header,
    "pages_count" => $pages_count,
    "pages" => $pages,
    "cur_page" => $cur_page
]);

/**
 * @var object $is_auth
 * @var string $user_name
 */
$layout_content = include_template("layout.php", [
    "title" => "Поиск",
    "content" => $page_content,
    "categories" => $categories,
    "search" => $search,
    "goods" => $goods,
    "is_auth" => $is_auth,
    "user_name" => $user_name
    ]);

print($layout_content);
