
<section class="content__side">
<h2 class="content__side-heading">Проекты</h2>

<nav class="main-navigation">
    <ul class="main-navigation__list">
        <?php foreach ($projects as $project) : ?>
            <li class="main-navigation__list-item
              <?php if (isset($project['id']) && intval($project['id']) === intval($_GET['id'])): ?>
              main-navigation__list-item--active
              <?php endif; ?>
            ">
              <a class="main-navigation__list-item-link" href="/index.php?id=<?= $project['id']; ?>"><?=esc($project['category']); ?></a>
              <span class="main-navigation__list-item-count">
               <?=do_counting($project['category'], $tasks) ?>
              </span>
            </li>
        <?php endforeach; ?>
    </ul>
</nav>
<a class="button button--transparent button--plus content__side-button"
   href="pages/form-project.html" target="project_add">Добавить проект</a>
</section>

<main class="content__main">
<h2 class="content__main-heading">Добавление задачи</h2>

<form class="form"  action="add.php" method="post" enctype="multipart/form-data" autocomplete="off">
  <div class="form__row">
    <label class="form__label" for="name">Название <sup>*</sup></label>
    <input class="form__input<?php if (isset($errors['name'])): ?>
    form__input--error
    <?php endif; ?>
    " type="text" name="name" id="name" value="<?=htmlspecialchars($task['name']) ?: NULL?>" placeholder="Введите название">
    <?php if (isset($errors['name'])): ?>
    <p class="form__message"> <?=$errors['name']; ?> </p>
    <?php endif; ?>
  </div>

  <div class="form__row">
    <label class="form__label" for="project">Проект</label>
    <select class="form__input form__input--select<?php if (isset($errors['project'])): ?>
    form__input--error
    <?php endif; ?>
    " name="project" id="project">
      <?php foreach ($projects as $project):?>
        <option value="<?=$project['id'];?>"<?=($task['project'] === $project['id']) ? 'selected' : NULL?>><?=$project['category'];?></option>
      <?php endforeach; ?>
    </select>
    <?php if (isset($errors['project'])): ?>
    <p class="form__message"> <?=$errors['project']; ?> </p>
    <?php endif; ?>
  </div>

  <div class="form__row">
    <label class="form__label" for="date">Дата выполнения</label>
    <input class="form__input form__input--date<?php if (isset($errors['date'])): ?>
    form__input--error
    <?php endif; ?>
    " type="date" name="date" id="date" value="<?=($task['date'] && !$errors['date']) ? $date_value = date('Y-m-d', strtotime(strip_tags($task['date']))) : NULL;?>" placeholder="Введите дату в формате ДД.ММ.ГГГГ">
    <?php if (isset($errors['date'])): ?>
    <p class="form__message"> <?=$errors['date']; ?> </p>
    <?php endif; ?>
  </div>

  <div class="form__row">
    <label class="form__label " for="preview">Файл</label>
    <div class="form__input-file">
      <input class="visually-hidden" type="file" name="preview" id="preview" value="">
      <label class="button button--transparent<?php if (isset($errors['preview'])): ?>
    form__input--error
    <?php endif; ?>
      " for="preview">
        <span>Выберите файл</span>
      </label>
    </div>
    <?php if (isset($errors['preview'])): ?>
    <p class="form__message"> <?=$errors['preview']; ?> </p>
    <?php endif; ?>
  </div>
<?=(!empty($errors)) ? '<p style="font: Arial, 20px; color: #ff0000;"> Для отправки формы требуется верно заполнить все поля </p>' : NULL;?>

  <div class="form__row form__row--controls">
    <input class="button" type="submit" name="" value="Добавить">
  </div>
</form>
</main>
