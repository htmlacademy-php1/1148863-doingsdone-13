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
 * Приводим id к числовому типу
 */

$rand_user = 1;
$user_id = intval($rand_user);

/**
 * Получаем данные о пользователе
 */

$user = find_users($connection, $user_id);

/**
 * Получаем список проектов пользователя
 */

$projects = find_projects($connection, $user_id);


/**
 * Получаем список задач пользователя
 */

$tasks = find_tasks($connection, $user_id);

$tasks_from_project = find_task_from_project($tasks, $projects);

/**
 * Подключаем страницы
 */

$page_content = include_template('main.php', [
    'tasks_from_project' => $tasks_from_project,
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
