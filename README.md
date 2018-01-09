# Наработки по Yii2  #

**[pagesize](https://github.com/alex290/yii2-recipe/tree/master/pagesize)** - Изменение количество записей в пагинации

**[select-tree](https://github.com/alex290/yii2-recipe/tree/master/select-tree)** - Дерево категории в SELECT

**[translit-js](https://github.com/alex290/yii2-recipe/tree/master/translit-js)** - Транслитерация текста из русского в латиницу

**[sef-URL.md](https://github.com/alex290/yii2-recipe/blob/master/sef-URL.md)** - Yii2 ЧПУ ссылок (URL) для сайта используя свой класс правил для urlManager из DB

**[youtube](https://github.com/alex290/yii2-recipe/tree/master/youtube)** - Замена ссылок youtube

**[git-ftp](https://github.com/alex290/yii2-recipe/tree/master/git-ftp)** - git-ftp Быстрая установка на Windows. Настройка -- Как загружать изменения на хостинг через FTP

## Первая картинка из текста ##

	<?php
	 	$outputImg = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $model->body, $matches);
	 	$first_img = $matches [1] [0];
	?>

и выводим

	<img src="<?= $first_img ?>" style="float: left; margin-right: 8px" width="250px" height="auto" alt="">