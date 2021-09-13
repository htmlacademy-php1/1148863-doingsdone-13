<?php

date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');

/**
 *  Начало сессии
 */
session_start();

/**
 * Подключение к БД
 */
$connection = do_connection();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $auth = $_POST;
        if (!empty($auth)) {
// Получаем результат валидации формы
            $result = is_auth_valid($connection, $auth);

// Если ошибок нет, то записываем ID и переходим на главную
            if (empty($result['errors'])) {

                if (isset($result)) {
                    $_SESSION['id'] = $result;
                    unset($auth);
                    header("Location: /index.php");
                    exit;
                } else {
                    $error_page[] = 'Не удалось перейти на страницу!';
                };
            }
        };
    }


    // Начало HTML кода
    $page_content = (empty($error_page)) ? include_template('auth.php',[
        'auth' => $auth,
        'errors' => $result['errors']
        ]) : include_template('error.php',[
        'error_page' => $error_page
        ]);
        $layout_content = include_template('layout.php', [
            'content' => $page_content,
            'title' => 'Дела в порядке',
             'user' => $user['user_name']
          ]);

    print($layout_content);
?>
