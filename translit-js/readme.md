# Транслитерация текста из русского в латиницу #

Подключаем скрипт

	<script src="js/translitCyr.js"></script>

В первое поле пишем класс например input-in - где будем вводить текст
А во втором пишем класс  input-out - где будет вводиться текст в латинице.

И запускаем

	$('.input-in').keyup(function(){
    	$('.input-out').val(cyrillicToTranslit().transform($(this).val()));
	});

## Использование в Yii2 views/.../_form.php ##

Регистрируем js в форме

	$this->registerJsFile(
        '@web/js/translitCyr.js', ['depends' => [\yii\web\JqueryAsset::className()]]
	);

в заголовке указываем класс 

	<?= $form->field($model, 'name')->textInput(['maxlength' => true, 'class' => 'input-in form-control']) ?>

и во втором поле пишем

	<?= $form->field($model, 'alias')->textInput(['maxlength' => true, 'class' => 'input-out form-control']) ?>

И прописываем скрипт 

	$this->registerJs(<<<JS
    	$('.input-in').keyup(function(){
    		$('.input-out').val(cyrillicToTranslit().transform($(this).val()));
		});
	JS, ['depends' => [\yii\web\JqueryAsset::className()]]
	);