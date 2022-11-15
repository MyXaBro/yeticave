<?php
session_start();
$user_name = null;
$is_auth = !empty($_SESSION["name"]);
if ($is_auth) {
    $user_name = $_SESSION["name"];
}
