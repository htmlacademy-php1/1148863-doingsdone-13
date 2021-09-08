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

/**
 * Проверяем наличие пользователя в базе
 * Если пользователь незалогиненный - отправляем на страницу входа/регистрации
 * Если находим пользователя - показываем его страницу
 */
if (!isset($_SESSION['id'])) {
    header("Location: new-user.php");
    exit;
};

/**
 * Приводим id к числовому типу
 */

$user_id = $_SESSION['id'];

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

/**
 * Получаем список задач пользователя с привязкой к проекту
 * Если проект не выбран - показываем все задачи
 * Если проект выбран - показываем задачи, относящиеся к этому проекту
 * Новые задачи показываем в самом начале списка
 */
$tasks_from_project = array_reverse((find_task_from_project($tasks, $projects)), true);

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
