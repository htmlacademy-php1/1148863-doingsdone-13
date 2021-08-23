<?php

/**
 * Дедлайн
 */
function get_time($final_data):int {
    $HOURS = 3600;
    $now_date = time();
    $diff_time = strtotime($final_data) - $now_date;
    $hours_until_end = floor($diff_time / $HOURS);
    return $hours_until_end;
};

/**
 * Подсчет проектов
 */
function do_counting(string $name, array $items):int {
   $number = 0;
   foreach($items as $task) {
       if($name == $task['project']) {
           $number ++;
       }
   };
   return $number;
};

/**
 * Фильтрация
 */
function esc($str) {
   $text = htmlspecialchars($str);
   return $text;
};

/**
 * Рендерит контент для страницы с ошибкой
 */
function find_mistake(string $error):string {
    $content = include_template('error.php',['error' => $error]);
    return $content;
};

/**
 * Выводит страницу с ошибкой и завершает работу
 */
function data_mistake_text(string $error) {
    $content = find_mistake($error);
    $layout_content = include_template('layout.php', [
        'content' => $content,
        'title' => 'Дела в порядке'
      ]);

    print($layout_content);
    exit;
};


/**
 * Подключение к БД
 */
function do_connection():object {
    $connection = mysqli_connect('localhost', 'root', '', 'doings');
    mysqli_set_charset($connection, 'utf8');
    if (!$connection) {
        $error = mysqli_connect_error();
        data_mistake_text($error);
    }
    return $connection;
};

/**
 * Получает данные о пользователе
 */
function find_users(object $connection, int $user_id):array {
    $users = 'SELECT id, user_name FROM users WHERE id = ' . $user_id;
    $result = mysqli_query($connection, $users);
    if (!$result) {
        $error = mysqli_error();
        data_mistake_text($error);
    }

     $user = mysqli_fetch_assoc($result);
     return $user;
};

/**
 * Получаем список проектов
 */
function find_projects(object $connection, int $user_id):array {
    $projects = 'SELECT id, category FROM projects WHERE user_id = ' . $user_id;
    $result_project = mysqli_query($connection, $projects);
    if (!$result_project) {
        $error = mysqli_error();
        data_mistake_text($error);
    }

     $projects = mysqli_fetch_all($result_project, MYSQLI_ASSOC);
     return $projects;
};



/**
 * Получаем общий список задач
 */
function find_tasks(object $connection, int $user_id):array {
    $tasks = 'SELECT tasks.id, tasks.user_id, projects.category AS project, tasks.project_id, tasks.task, tasks.final_date, tasks.ready_or_not FROM tasks '
    . 'LEFT JOIN projects ON tasks.project_id = projects.id '
    . 'LEFT JOIN users ON tasks.user_id = users.id '
    . 'WHERE tasks.user_id = ' . $user_id;
    $result_task = mysqli_query($connection, $tasks);
    if (!$result_task) {
        $error = mysqli_error();
        data_mistake_text($error);
    }

     $tasks = mysqli_fetch_all($result_task, MYSQLI_ASSOC);
     return $tasks;
};



/**
 * Проверяем существование id проекта
 * Выбираем задачи только для выбранного проекта
 * Если в проекте задач нет, то получаем список задач по всем проектам
 */
function find_task_from_project(array $tasks, array $projects):array {

    if (!isset($_GET['id'])) {
        return $tasks;
    }
        $result = [];
        $project_id = intval($_GET['id']);
        $get_project = false;
        foreach($projects as $project => $value){
            if ($project_id === intval($value['id'])){
                $get_project = true;
                break;
            }
        }
        if ($get_project === false){
            http_response_code(404);
            $error = 'Проект с данным ID не найден!';
            $content = find_mistake($error);
            $layout_content = include_template($path_to_template . "layout.php", [
                "content" => $content,
                "user" => $user,
            ]);
            print($layout_content);
            exit;
        }
        foreach ($tasks as $task) {
            if (intval($task['project_id']) === $project_id) {
                $result[] = $task;
            };
        }
        return $result;
    };

?>
