<?php
date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('data.php');

// Подключение к БД

$connection = mysqli_connect('localhost', 'root', '', 'doings');
mysqli_set_charset($connection, 'utf8');

// Проверяем нет ли ошибки соединения

if (!$connection) {
    $error = mysqli_connect_error();
    $content = include_template('error.php',['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'user' => $user
      ]);

    print($layout_content);
    exit;
}
 else {

// Получаем данные о пользователе

    $user_id = 1;
    $sql = 'SELECT id, user_name FROM users WHERE id = ' . $user_id;
    $result = mysqli_query($connection, $sql);

    if ($result) {
        $user = mysqli_fetch_assoc($result);
    }
     else {
        $error = mysqli_connect_error();
        $content = include_template('error.php',['error' => $error]);
        $layout_content = include_template('layout.php', [
            'content' => $content,
            'title' => 'Дела в порядке',
            'user' => $user
          ]);
        print($layout_content);
        exit;
     }

 // Получаем список проектов пользователя

$sql_project = 'SELECT id, category FROM projects WHERE user_id = ' . $user_id;
$result_project = mysqli_query($connection, $sql_project);

if (!$result_project) {
    $error = mysqli_connect_error();
    $content = include_template('error.php',['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'user' => $user
      ]);
    print($layout_content);
    exit;
}
  else {
    $projects = mysqli_fetch_all($result_project, MYSQLI_ASSOC);
  }

//Получаем список задач пользователя

$sql_task = 'SELECT tasks.id, tasks.user_id, projects.category AS project,  tasks.task, tasks.final_date, tasks.ready_or_not FROM tasks '
. 'LEFT JOIN projects ON tasks.project_id = projects.id '
. 'LEFT JOIN users ON tasks.user_id = users.id '
. 'WHERE tasks.user_id = ' . $user_id;
$result_task = mysqli_query($connection, $sql_task);

if (!$result_task) {
    $error = mysqli_connect_error();
    $content = include_template('error.php',['error' => $error]);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке',
        'user' => $user
      ]);
    print($layout_content);
    exit;
}
  else {
   $tasks = mysqli_fetch_all($result_task, MYSQLI_ASSOC);
  }

 };

// Функции

 function get_time($final_data) {
     $HOURS = 3600;
     $now_date = time();
     $diff_time = strtotime($final_data) - $now_date;
     $hours_until_end = floor($diff_time / $HOURS);
     return $hours_until_end;
 };

function do_counting($name, $items) {
    $number = 0;
    foreach($items as $task) {
        if($name == $task['project']) {
            $number ++;
        }
    };
    return $number;
}

function esc($str) {
	$text = htmlspecialchars($str);
	return $text;
}

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
