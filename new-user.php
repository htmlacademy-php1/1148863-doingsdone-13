<?php
date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');

/**
 * Подключение к БД
 */
$connection = do_connection();

/**
 * Проверяем корректность заполнения формы
 * Если форма валидна - переходим на главную
 * Если нет - просим исправить ошибки
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_data = $_POST;
    if (!empty($user_data)) {
        $errors = is_register_valid($connection, $user_data);
        if (empty($errors)) {
            $result = add_new_user($connection, $user_data);
            if ($result) {
                unset($user_data);
                header("Location: index.php");
                exit;
            } else {
                $error_page[] = 'Ошибка! Пользователь не зарегистрирован!';
            };
        };
    };
}

/**
 * Подключаем страницы
 */
$page_content = (empty($error_page)) ? include_template('register.php', [
    'user_data' => $user_data,
    'errors' => $errors
    ]) : include_template('error.php',[
    'error_page' => $error_page
    ]);

$layout_content = include_template('layout.php', [
  'content' => $page_content,
  'title' => 'Дела в порядке'
]);


print($layout_content);

?>
