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
 * Получаем ID зарегистрированного пользовалеля
 */


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

/**
 * Валидация формы с последующей отправкой
 * Если форма заполнена некорректно - пишем сообщение об ошибке и не отправляем форму
 * Если форма заполнена корректно - отправляем ее
 * Отправляем пользователя на главную страницу
 */
    function is_form_valid(array $task, array $categories):array {
        $errors = [];
        if (empty($task['name'])) {
            $errors['name'] = 'Необходимо указать название задачи';
        };

        if (!empty($task['project'])) {
            if (!in_array((int)$task['project'], array_column($categories, 'id'))) {
                $errors['project'] = 'Выберите проект из списка';
            };
        } else {
            $errors['project'] = 'Выберите проект из списка';
        };

        if (empty($task['date'])) {
            $errors['date'] = 'Укажите дату выполнения';
        } else {
            if (!is_date_valid($task['date'])) {
                $errors['date'] = 'Неверный формат даты';
            };
            $now_date = time();

            if (strtotime($task['date']) < $now_date) {
                $errors['date'] = 'Дата должна быть больше или равна текущей';
            };
        };

        if (!empty($_FILES['preview']['name'])) {
            if (!$_FILES['preview']['error']) {
                if (!$_FILES['preview']['size']) {
                    $errors['preview'] = 'Файл не должен быть пустым';
                };
            } else {
                $errors['preview'] = 'Произошла ошибка при загрузке файла : ' . $_FILES['preview']['error'];
            };
        };
        return $errors;
    };


/**
 * Проверяем наличие параметра новой задачи, чтобы добавить его в БД
 */
function is_param($name, $connection, $param) {
    if (!isset($name)) {
        $result = NULL;
    } else {
        $result = mysqli_real_escape_string($connection, $param);
    }
        return $result;
};


/**
 * Добавляем новую задачу в список текущего пользователя
 * Пользователь увидит задачу в списке на главной странице профиля
 * Если произошла ошибка - пишем сообщение
 */
    function add_new_task($connection, $user_id, $new_task, $file_name) {
        $result = [];
        if ($connection) {
            mysqli_set_charset($connection, "utf8");
            // Название задачи
            $name = is_param($new_task['name'], $connection, $new_task['name']);
            // ID пользователя
            $id = is_param($user_id, $connection, $user_id);
            // Название проекта
            $project = is_param($new_task['project'], $connection, $new_task['project']);
            // Дата окончания задачи
            $new_date = date('Y-m-d', strtotime($new_task['date']));
            $date = is_param($new_task['date'], $connection, $new_date);
            // файл
            $file_inside = is_param($file_name, $connection, $file_name);

            // Добавляем параметры в БД
            $sql = "INSERT INTO tasks (
                task,
                user_id,
                project_id,
                final_date,
                evidence
            ) VALUES ('" .
                $name . "', '" .
                $id . "', '" .
                $project . "', '" .
                $date . "', '" .
                $file_inside . "'" . "
            )";
            $result = mysqli_query($connection, $sql);
        } else {
            $result = 'Произошла ошибка: ' . mysqli_error($connection);
        };
        return $result;
    };


 /**
  * Валидация формы регистрации нового пользователя
  */
     function is_register_valid($connection, $user_data) {
         $errors = [];
        if (!empty($user_data['email'])) {
            $email = mysqli_real_escape_string($connection, $user_data['email']);
            $sql = "SELECT id FROM users WHERE email = '$email'";
            $result = mysqli_query($connection, $sql);
            if(mysqli_num_rows($result) > 0) {
                $errors['email'] = 'Пользователь с таким email уже существует!';
        };

        } else {
            $errors['email'] = 'Введите email';
        };

        if (empty($user_data['password'])) {
            $errors['password'] = 'Введите пароль';
        };

        if (empty($user_data['name'])) {
            $errors['name'] = 'Введите имя';
        };

        return $errors;
    };

/**
 * Добавляем в БД нового пользователя
 */
function add_new_user($connection, $user_data) {
        mysqli_set_charset($connection, "utf8");
        // Email
        $email = mysqli_real_escape_string($connection, $user_data['email']);
        // Имя
        $name = mysqli_real_escape_string($connection, $user_data['name']);
        // Пароль
        $password = password_hash($user_data['password'], PASSWORD_DEFAULT);
        // Добавляем параметры в БД
        $sql = "INSERT INTO users (
                email,
                user_name,
                parole
            ) VALUES('" .
                $email . "', '" .
                $name . "', '" .
                $password . "'" . "
            )";

        $new_user = mysqli_query($connection, $sql);

    if ($new_user == false) {
        $result = 'Произошла ошибка: ' . mysqli_error($connection);
    } else {
        $result = $new_user;
    };
    return $result;
};

?>
