<?php

// Дедлайн
function get_time($final_data) {
    $HOURS = 3600;
    $now_date = time();
    $diff_time = strtotime($final_data) - $now_date;
    $hours_until_end = floor($diff_time / $HOURS);
    return $hours_until_end;
};

// Подсчет проектов
function do_counting($name, $items) {
   $number = 0;
   foreach($items as $task) {
       if($name == $task['project']) {
           $number ++;
       }
   };
   return $number;
}

// Фильтрация
function esc($str) {
   $text = htmlspecialchars($str);
   return $text;
}

// Подключение к БД

function do_connection() {
    $connection = mysqli_connect('localhost', 'root', '', 'doings');
    return $connection;
};


// Проверяем наличие ошибки соединения

function find_mistake(string $error) {
    $content = include_template('error.php',['error' => $error]);
};

// Получаем список пользователей

function find_users(int $user_id) {
    $connection = do_connection();
    $users = 'SELECT id, user_name FROM users WHERE id = ' . $user_id;
    $result = mysqli_query($connection, $users);
    return $result;
};

// Получаем список проектов

function find_projects(int $user_id) {
    $connection = do_connection();
    $projects = 'SELECT id, category FROM projects WHERE user_id = ' . $user_id;
    $result_project = mysqli_query($connection, $projects);
    return $result_project;
};

// Получаем список задач

function find_tasks(int $user_id) {
    $connection = do_connection();
    $tasks = 'SELECT tasks.id, tasks.user_id, projects.category AS project,  tasks.task, tasks.final_date, tasks.ready_or_not FROM tasks '
    . 'LEFT JOIN projects ON tasks.project_id = projects.id '
    . 'LEFT JOIN users ON tasks.user_id = users.id '
    . 'WHERE tasks.user_id = ' . $user_id;
    $result_task = mysqli_query($connection, $tasks);
    return $result_task;
};


?>
