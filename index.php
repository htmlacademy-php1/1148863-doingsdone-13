<?php
date_default_timezone_set("Europe/Moscow");
require_once('helpers.php');
require_once('data.php');





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
        if($name == $task['category']) {
            $number ++;
        }
    };
    return $number;
}

function esc($str) {
	$text = htmlspecialchars($str);
	return $text;
}

$page_content = include_template('main.php', [
    'tasks' => $tasks,
    'projects' => $projects
]);
$layout_content = include_template('layout.php', [
  'content' => $page_content,
  'title' => 'Дела в порядке',
   'user' => 'Константин'
]);


print($layout_content);

?>
