<?php
date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('data.php');
require_once('functions.php');

/** Подключение к БД */

$connection = do_connection();

/** Получаем данные о пользователе */

$user = find_users($connection);

/** Получаем список проектов пользователя */

$projects = find_projects($connection);

/** Получаем список задач пользователя */

$tasks = find_tasks($connection);

/**  Подключаем страницы */

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
