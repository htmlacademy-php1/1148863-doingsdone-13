<?php
date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');

// Подключение к БД

$connection = do_connection();
mysqli_set_charset($connection, 'utf8');

// Проверяем нет ли ошибки соединения

if (!$connection) {
    $error = mysqli_connect_error();
    $content = find_mistake($error);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'user' => $user
      ]);

    print($layout_content);
    exit;
}

// Получаем данные о пользователе

// Приводим id к числовому типу
    $rand_user = 1;
    $user_id = intval($rand_user);

    $result = find_users($user_id);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
    }
     else {
        $error = mysqli_connect_error();
        $content = find_mistake($error);
        $layout_content = include_template('layout.php', [
            'content' => $content,
            'title' => 'Дела в порядке',
            'user' => $user
          ]);
        print($layout_content);
        exit;
     }

// Получаем список проектов пользователя

$result_project = find_projects($user_id);

if (!$result_project) {
    $error = mysqli_error();
    $content = find_mistake($error);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'user' => $user
      ]);
    print($layout_content);
    exit;
}

$projects = mysqli_fetch_all($result_project, MYSQLI_ASSOC);

//Получаем список задач пользователя

$result_task = find_tasks($user_id);

if (!$result_task) {
    $error = mysqli_error();
    $content = find_mistake($error);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'user' => $user
      ]);
    print($layout_content);
    exit;
}

$tasks = mysqli_fetch_all($result_task, MYSQLI_ASSOC);

// Подключаем страницы

$page_content = include_template('main.php', [
    'tasks' => $tasks,
    'projects' => $projects
]);
$layout_content = include_template('layout.php', [
  'content' => $page_content,
  'title' => 'Дела в порядке',
   'user' => $user['user_name']
]);


print($layout_content);

?>
