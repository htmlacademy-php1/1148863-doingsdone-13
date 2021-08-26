<section class="content__side"></section>

<main class="content__main">
    <h2 class="content__main-heading">Внимание! Произошла ошибка!:</h2>
    <ul>
<?php foreach($error_page as $error):?>
    <li class="content__main-heading"><?=$error?></li>
<?php endforeach; ?>
</ul>
