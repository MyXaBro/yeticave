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

$page_content = include_template("main-sign-up.php", [
    "categories" => $categories
]);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $required = ["email", "password", "name", "message"];
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

    $user = filter_input_array(INPUT_POST,
        [
            "email" => FILTER_DEFAULT,
            "password" => FILTER_DEFAULT,
            "name" => FILTER_DEFAULT,
            "message" => FILTER_DEFAULT
        ], true);

    foreach ($user as $field => $value) {
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
            "user" => $user,
            "errors" => $errors
        ]);
    } else {
        $users_data = get_users_data($connect);
        $emails = array_column($users_data, "email");
        $names = array_column($users_data, "user_name");
        if (in_array($user["email"], $emails)) {
            $errors["email"] = "Пользователь с таким е-mail уже зарегистрирован";
        }
        if (in_array($user["name"], $names)) {
            $errors["name"] = "Пользователь с таким именем уже зарегистрирован";
        }

        if (count($errors)) {
            $page_content = include_template("main-sign-up.php", [
                "categories" => $categories,
                "user" => $user,
                "errors" => $errors
            ]);
        } else {
            $sql = get_query_create_user();
            $user["password"] = password_hash($user["password"], PASSWORD_DEFAULT);
            $stmt = db_get_prepare_stmt_version($connect, $sql, $user);
            $res = mysqli_stmt_execute($stmt);

            if ($res) {
                header("Location: /login.php");
            } else {
                $error = mysqli_error($connect);
            }
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



