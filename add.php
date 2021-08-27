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

/**
 * Получаем список задач пользователя с привязкой к проекту
 * Если проект не выбран - показываем все задачи
 * Если проект выбран - показываем задачи, относящиеся к этому проекту
 * Новые задачи показываем в самом начале списка
 */
$tasks_from_project = array_reverse((find_task_from_project($tasks, $projects)), true);

/* Получаем данные из формы добавления задачи
 * Если форма валидна - добавляем новую задачу
 * Если форма невалидна - выводим сообщение об ошибке
 */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_task = $_POST;
    if (!empty($new_task)) {
        $errors = is_form_valid($new_task, $projects);
        if (empty($errors)) {
            $file = $_FILES['preview'];
            if (!empty($file['name'])) {
                $dev = pathinfo($file['name'], PATHINFO_EXTENSION);
                $file_name = uniqid() . (!empty($dev) ? '.' : '') . $dev;
                move_uploaded_file($file['tmp_name'], $_SERVER['DOCUMENT_ROOT'] . '/' . $file_name);
            };
            $result = add_new_task($connection, $user_id, $new_task, $file_name);
            if ($result) {
                unset($new_task);
                header("Location: index.php");
                exit;
            }
                $error_page[] = 'Ошибка! Задача не добавлена!';
        };
    };
};


/**
 * Подключаем страницы
 */
$page_content = (empty($error_page)) ? include_template('form-task.php', [
    'tasks_from_project' => $tasks_from_project,
    'tasks' => $tasks,
    'task' => $new_task,
    'projects' => $projects,
    'errors' => $errors
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
