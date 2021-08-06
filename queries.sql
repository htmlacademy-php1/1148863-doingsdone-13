/*
 Придумываем пару пользователей в таблице users
 */
INSERT INTO users (reg_date, email, user_name, parole)
VALUES ('2018-01-01 00:01:02','manya@yandex.ru', 'maria', '2935nyt'),
       ('2018-09-09 00:09:20','vanya@yandex.ru', 'ivan', '2958oo');

/*
 Добавляем проекты из списка в таблицу projects
 */
INSERT INTO projects (user_id, category)
VALUES (1, "Входящие"),
       (1, "Учёба"),
       (2, "Работа"),
       (1, "Домашние дела"),
       (2, "Авто");

/*
 Добавляем задачи из списка в таблицу tasks
 */
INSERT INTO tasks (user_id, project_id, task, final_date)
VALUES (2, 3, 'Собеседование в IT компании', '2019-12-01'),
       (2, 3, 'Выполнить тестовое задание', '2019-11-06'),
       (1, 2, 'Сделать задание первого раздела', '2019-12-21'),
       (1, 1, 'Встреча с другом', '2019-12-22'),
       (1, 4, 'Купить корм для кота', NULL),
       (1, 4, 'Заказать пиццу', NULL);

/*
 Получаем список проектов для одного пользователя
 */
SELECT category FROM projects
WHERE user_id = 2;

/*
 Получаем список задач для одного проекта
 */
SELECT * FROM tasks
LEFT JOIN projects ON projects.id = tasks.project_id
WHERE projects.category LIKE 'Работа';

/*
 Помечаем задачу, как выполненную
 */
UPDATE tasks SET ready_or_not = 1
WHERE task = 'Купить корм для кота';

/*
 Обновляем название задачи по её идентификатору
 */
UPDATE tasks SET task = 'Сделать задание четвертого раздела'
WHERE id = 3;
