<?php
require_once("helpers.php");
require_once("function.php");
require_once("data.php");
require_once("init.php");
require_once("models.php");

/**
 * @var array $connect
 */
$categories = get_categories($connect);

$header = include_template("header.php", [
    "categories" => $categories
]);

$page_content = include_template("main-login.php", [
    "categories" => $categories
]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ["email", "password"];
    $errors = [];

    $rules = [
        "email" => function ($value) {
            return validate_email($value);
        },
        "password" => function ($value) {
            return validate_length($value, 6, 8);
        },
        "message" => function ($value) {
            return validate_length($value, 12, 1000);
        }
    ];

    $user_info = filter_input_array(INPUT_POST, [
        "email" => FILTER_DEFAULT,
        "password" => FILTER_DEFAULT,
        "name" => FILTER_DEFAULT,
        "message" => FILTER_DEFAULT
    ], true);

    foreach ($user_info as $field => $value) {
        if (isset($rules[$field])) {
            $rule = $rules[$field];
            $errors[$field] = $rule($value);
        }
        if (in_array($field, $required) && empty($value)) {
            $errors[$field] = "Поле $field нужно заполнить";
        }
    }

    $errors = array_filter($errors);

    if (count($errors)) {
        $page_content = include_template("main-sign-up.php", [
            "categories" => $categories,
            "user_info" => $user_info,
            "errors" => $errors
        ]);
    } else {
        $users_data = get_login($connect, $user_info["email"]);
        if ($users_data) {
            if (password_verify($user_info["password"], $users_data["user_password"])) {
                $issession = session_start();
                $_SESSION['name'] = $users_data["user_name"];
                $_SESSION['id'] = $users_data["id"];

                header("Location: /index.php");
            } else {
                $errors["password"] = "Вы ввели неверный пароль";
            }
        } else {
            $errors["email"] = "Пользователь с таким е-mail не зарегестрирован";
        }
        if (count($errors)) {
            $page_content = include_template("main-login.php", [
                "categories" => $categories,
                "user_info" => $user_info,
                "errors" => $errors
            ]);
        }
    }
}

/**
 * @var int $is_auth
 * @var string $user_name
 */
$layout_content = include_template("layout.php", [
    "content" => $page_content,
    "categories" => $categories,
    "title" => "Регистрация",
    "is_auth" => $is_auth,
    "user_name" => $user_name
]);

print($layout_content);