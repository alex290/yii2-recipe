# Транслитерация текста из русского в латиницу #

Подключаем скрипт

	<script src="js/translitCyr.js"></script>

В первое поле пишем класс например input-in - где будем вводить текст
А во втором пишем класс  input-out - где будет вводиться текст в латинице.

И запускаем

	$('.input-in').keyup(function(){
    	$('.input-out').val(cyrillicToTranslit().transform($(this).val()));
	});

Вот и все