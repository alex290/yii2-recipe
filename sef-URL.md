# Yii2 ЧПУ ссылок (URL) для сайта на используя свой класс правил для urlManager из DB #

Большинство правил можно задать не создавая отдельный класс для этого, поэтому использовать его будем тогда, когда для этого понадобится брать данные из таблицы БД, остальные правила пропишем прямо в конфигурации. Для этого открываем файл *frontend\config\main.php* (если Yii2 Advanced) и в массиве components меняем (создаем) **urlManager** : 

	'urlManager' => [
		//'class' => 'yii\web\UrlManager',
		'showScriptName' => false, //отключаем r=routes 
		//запретить стандартные URL если не соответствует правилам класса
		'enableStrictParsing' => true,
		'enablePrettyUrl' => true, //отключаем index.php
		'rules' => array(
			'/' => 'post/index', //для главной страницы
			'site/captcha' => 'site/captcha', //для капчи ничего не меняем
			//эти страницы будут открываться при указании только одного действия
			'<action:search|login|logout|signup|request-password-reset>' => 'site/<action>',
			//остальные правила в своем классе SefRule
			['class' => 'common\components\SefRule',
				'connectionID' => 'db',
			],
		),
	],

Тут мы прописываем несколько правил, при использовании которых yii2 не должен использовать базу данных. Их мы размещаем до подключения нашего класса **SefRule** в массие **"rules"**, чтобы при соответствии, программа их применяла не доходя до выполнения следующих строк. А именно это: 

- главная страница;
- вывод капчи (например в форме контактов и комментариев);
- для страницы поиска.

В двух словах: ключем массива является такой вид URL какой мы хотим, а значением - стандартный вид URL для фреймворка - контроллер/действие[параметры]. К примеру если написать правило 

	search' => 'site/search',

то в адресной строке будет написано просто search, без указания имени контроллера (site). Т.к. я хочу вызывать по одному только названию действия и несколько других страниц контроллера site, я это объединил в одну строку: 

	'<action:search|login|logout|signup|request-password-reset>' => 'site/<action>',

Подробнее о составлении правил маршрутизации можно прочитать тут: [github.com](https://github.com/yiisoft/yii2/blob/master/docs/guide-ru/runtime-routing.md) 

Также в компоненте конфигурации urlManager мы отключаем вывод точки входа - страницу ***index.php***, других служебных надписей (routes) и запрещаем фреймворку, при несоответствии нашим правилам, самовольно искать способ открыть запрашиваемую страницу (убираем дубли страниц). 

## Создание класса правил ЧПУ. ##

Как видно из правил **urlManager**, класс будет размещаться в *common\components*. Ниже приведу полный код файла **SefRule.php**: 

	<?php
	namespace common\components;
	
	use common\models\Sef;
	use yii\web\UrlRuleInterface;
	use yii\base\Object;
	
	class SefRule extends Object implements UrlRuleInterface{
	
		public $connectionID = 'db';
		public $name;
	
		public function init(){
			if ($this->name === null) {
				$this->name = __CLASS__;
			}
		}
	
		//Формирует ссылки в заданном виде (часть формируется в коде, остальное берется из БД)
		public function createUrl($manager, $route, $params){
			//debug($route);
			//Определяем контроллеры, у которых к страницам нужно добавлять .html
			$controller = explode('/',$route)[0]; //Получаем контроллер        
			if ($route == 'post/view' || $controller == 'site') $html = '.html';
			else $html = '';
	
			//Если передаются параметры (напрмиер ?id=3&page=2) сохраняем в $link по-очереди
			$link ='';
			$page ='';
			if(count($params)){
				$link = "?";
				$page = false;
				foreach ($params as $key => $value){
					if($key == 'page'){
						$page = $value;
						continue;
					}
					$link .= "$key=$value&";
				}
				$link = substr($link, 0, -1); //удаляем последний символ (&)
			}
			//Из БД получаем строку со ссылкой на которую нужно будет поменять
			$sef = Sef::find()->where(['link' => $route.$link])->one();
	
			if ($sef){
				//Если есть - добавляем пагинацию в конец (?page=2)
				if ($page) return $sef->link_sef."$html?page=$page";
				else return $sef->link_sef.$html;
			}
			return false;
		}
	
		//Разбирает входящий URL запрос, преобразует ссылки произвольного вида (из БД поле link_sef) в нужный для Yii2
		public function parseRequest($manager, $request){        
	
			//Получаем URL
			$pathInfo = $request->getPathInfo();    
	
			//Получаем 1 часть, до слэша, если есть
			$alias = explode('/',$pathInfo)[0];
			//Если на конце .html, то убираем для поиска в БД
			$alias_small = str_replace(".html","",$alias);
	
			//не выводить .html для указанных URL (первая часть алиаса)
			$not_html = [
				'category','tag','posts','notes'
			];
	
			/*
			* Проверяем наличие URL (до слэша) в $not_html
			* Если есть, то в URL не должно быть окончание .html
			* $exception = true разрешает поиск URL в БД
			*/
			$exception = false;
			if(array_search($alias_small, $not_html) !== FALSE){            
				if (preg_match("/^(.*)\.html$/",$pathInfo)) return false;
				$exception = true;
			}     
	
			//Будем искать в БД если ссылка содержит .html или есть в $not_html
			if(preg_match('/^(.*)\.html$/', $pathInfo, $matches) || $exception){    
	
				$pathInfo = isset($matches[1]) ? $matches[1] : $pathInfo;
	
				//получаем из БД данные по строке содержащей заданный алиас
				$sef  = Sef::find()->where(['link_sef' => $pathInfo])->one();
	
				if($sef){
					//Разбивает строку типа post/view?id=5 на массив по разделителю
					$link_data = explode('?',$sef->link);
					//берем только первую часть без параметров (контроллер/действие)
					$route = $link_data[0]; 
					$params = array();
					//если есть параметры - вставляем их 
					if(isset($link_data[1])){
						$temp = explode('&',$link_data[1]);
						foreach($temp as $t){
							$t = explode('=', $t);
							$params[$t[0]] = $t[1];
						}
					}
					//$route - контроллер/действие
					//$params - параметры
					return [$route, $params];
				}
			}
			return false;
		}
	}


Наш класс **SefRule** наследует методы от класса **Object** и реализует интерфейс **UrlRuleInterface**. Он состоит из двух основных методов, один из которых **createUrl()** - выводит из БД URL в том виде в котором нам нужно, а второй **parseRequest()** преобразует нашу фантазию в вид стандартный для Yii2. То есть он ищет URL произвольного вида в БД, возвращая нормальный URL. Все указанные методы - стандартные, переименовывать их нельзя.

В методе **createUrl()** я указал контроллеры (у меня это **PostController** и **SiteController**), при выводе страниц которых будет добавляться префикс .html, указывающий что это статическая страница. 

## Теперь касательно БАЗЫ ДАННЫХ. ##

Для тех, кто использует yii миграции для работы с БД, выкладываю соответствующий код: 

	public function safeUp()
	{
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=InnoDB';
		}
		$this->createTable('{{%sef}}', [
			'id' => $this->primaryKey(),
			'link' => $this->string()->notNull(),
			'link_sef' => $this->string()->notNull(),        
		], $tableOptions);
	}
	public function safeDown()
	{
		$this->dropTable('{{%sef}}');
	}

Можно создать вручную. А именно - таблицу я назвал **sef**, в ней только 3 поля: 

- id (primaryKey, AUTO_INCREMENT);
- link (varchar(255));
- link_sef (varchar(255)).

Заполненная данными таблица в phpMyAdmin может выглядеть так: 

![](https://github.com/alex290/yii2-sef/raw/master/images/hpu.jpg)

В поле link указываем обычные URL, которые нужны Yii2. Они задаются в формате контроллер/действие/[параметры] и посмотреть их можно прямо в адресной строке. Как видно по изображению у меня записи формируются с помощью 4-х контроллеров. Основной post, который управляет записями из БД, вспомогательный site, который формирует несколько статических, служебных страниц, например страницу контактов. Так же отдельно контроллер для рубрик и меток. 

После контроллера нужно указать действие которые определяет файл для вывода страницы. Для записей которые берутся из БД, у меня это view. Дальше указывается id конкретной записи/рубрики/метки по которой производится выборка из БД. 

В поле link_sef указывается фраза-алиас, то есть то, как мы хотим чтобы было прописано в адресной строке и ссылках. Алиас может быть любой, но необходимо, чтобы он был уникальный, т.к. один URL может соответствовать только одной странице. Для рубрик (категорий) я сделал, чтобы он начинался с category, а для меток с tag, что логично и ЧПУ-доступно :) 

Вот и все, теперь мы имеем короткие, понятные URL. Причем страницы наших постов будут называться не 1,2,3 а иметь свое URL-название. Для удобства можно подключить автоматическое задание URL-алиаса в админке сайта при создании/редактировании постов и прочего. Но это уже материал для другой статьи. 
