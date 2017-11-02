# Запуск #

Чтобы использовать этот виджет с gridview, добавьте этот виджет на представлении, где gridview будет:

	<?php echo \common\PageSize::widget(); ?>

и установить свойство filterSelector из gridview в, как показано в следующем примере.

	<?= GridView::widget([
	     'dataProvider' => $dataProvider,
	     'filterModel' => $searchModel,
			'filterSelector' => 'select[name="per-page"]',
	     'columns' => [
	         ...
	     ],
	]); ?>
Please note that per-page here is the string you use for pageSizeParam setting of the PageSize widget.

Configurations

Following properties are available for customizing the widget.

	label: Text for the lbel
	defaultPageSize: Это значение будет использоваться, если размер страницы не выбран
	pageSizeParam: Имя параметра размера страницы, используемого для виджета разбиения по страницам в представлении сетки
	sizes: Массив значений ключей, используемых в качестве размеров страниц. И key, и value должны быть целыми числами
	template: Строка шаблона, используемая для рендеринга элементов. По умолчанию '{list} {label}'
	options: HTML-атрибутов для элемента <Select> элемент
	labelOptions: HTML-атрибуты элемента <label
	encodeLabel: Whether to encode label text

**Для увеличения лимита нужно поставить параметр **

			'Pagination' => [
                'pageSizeLimit' => [1, 200],
            ],
