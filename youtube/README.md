# Замена ссылок youtube #



## Замена видео iframe ##

	<iframe[^<]+src="[^<]+www.youtube.com/embed/([-_a-z0-9]{11})[^<]+</iframe>

Забираем данные ([-_a-z0-9]{11}) - Это id ссылки и пишем 

	https://www.youtube.com/watch?v=${1}

${1} - Это данные которые забрали

## Создание плеера iframe из ссылки youtube ##

	<?php if (!empty($model->video)): ?>
		<?php if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $model->video, $match)): ?>
		<div class="w-480">
		    <div class="video-container">
		        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?= $match[1] ?>?rel=0" frameborder="0" allowfullscreen></iframe>
		    </div>
		</div>
		<?php endif; ?>
	<?php endif; ?>