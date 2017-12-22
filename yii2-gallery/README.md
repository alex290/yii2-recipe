# Галерея для Yii2 #

https://github.com/zxbodya/yii2-gallery-manager

Устанавливаем расширение

	composer require --prefer-dist zxbodya/yii2-gallery-manager "*@dev"

или добавить

	"zxbodya/yii2-gallery-manager": "*@dev"

в раздел require вашего composer.json файла.

далее создаем миграцию

	yii migrate/create gallery_ext

И дописываем в этот файл 

	class m150318_154933_gallery_ext
		extends zxbodya\yii2\galleryManager\migrations\m140930_003227_gallery_manager
	{
	
	}

и создаем таблицу

	yii migrate

Генерируем модель Gallery и прописываем поведение

	public function behaviors()
	{
	    return [
	         'galleryBehavior' => [
	             'class' => GalleryBehavior::className(),
	             'type' => 'product',
	             'extension' => 'jpg',
	             'directory' => Yii::getAlias('@webroot') . '/images/product/gallery',
	             'url' => Yii::getAlias('@web') . '/images/product/gallery',
	             'versions' => [
	                 'small' => function ($img) {
	                     /** @var \Imagine\Image\ImageInterface $img */
	                     return $img
	                         ->copy()
	                         ->thumbnail(new \Imagine\Image\Box(200, 200));
	                 },
	                 'medium' => function ($img) {
	                     /** @var Imagine\Image\ImageInterface $img */
	                     $dstSize = $img->getSize();
	                     $maxWidth = 800;
	                     if ($dstSize->getWidth() > $maxWidth) {
	                         $dstSize = $dstSize->widen($maxWidth);
	                     }
	                     return $img
	                         ->copy()
	                         ->resize($dstSize);
	                 },
	             ]
	         ]
	    ];
	}


Получается такая модель

	<?php
	
	namespace app\models;
	
	use Yii;
	use zxbodya\yii2\galleryManager\GalleryBehavior;
	
	/**
	 * This is the model class for table "gallery_image".
	 *
	 * @property integer $id
	 * @property string $type
	 * @property string $ownerId
	 * @property integer $rank
	 * @property string $name
	 * @property string $description
	 */
	class Gallery extends \yii\db\ActiveRecord {
	
	    /**
	     * @inheritdoc
	     */
	    public static function tableName() {
	        return 'gallery_image';
	    }
	
	    public function behaviors() {
	        return [
	            'galleryBehavior' => [
	                'class' => GalleryBehavior::className(),
	                'type' => 'product',
	                'extension' => 'jpg',
	                'directory' => Yii::getAlias('@webroot') . '/images/product/gallery',
	                'url' => Yii::getAlias('@web') . '/images/product/gallery',
	                'versions' => [
	                    'small' => function ($img) {
	                        /** @var \Imagine\Image\ImageInterface $img */
	                        return $img
	                            ->copy()
	                            ->thumbnail(new \Imagine\Image\Box(200, 200));
	                    },
	                    'medium' => function ($img) {
	                        /** @var Imagine\Image\ImageInterface $img */
	                        $dstSize = $img->getSize();
	                        $maxWidth = 800;
	                        if ($dstSize->getWidth() > $maxWidth) {
	                            $dstSize = $dstSize->widen($maxWidth);
	                        }
	                        return $img
	                            ->copy()
	                            ->resize($dstSize);
	                    },
	                ]
	            ]
	        ];
	    }
	
	    /**
	     * @inheritdoc
	     */
	    public function rules() {
	        return [
	            [['ownerId'], 'required'],
	            [['rank'], 'integer'],
	            [['description'], 'string'],
	        ];
	    }
	
	    /**
	     * @inheritdoc
	     */
	    public function attributeLabels() {
	        return [
	            'id' => 'ID',
	            'type' => 'Type',
	            'ownerId' => 'Owner ID',
	            'rank' => 'Rank',
	            'name' => 'Name',
	            'description' => 'Description',
	        ];
	    }
	
	}


Создаем контроллер

	<?php
	
	namespace app\modules\admin\controllers;
	
	use app\models\Gallery;
	use zxbodya\yii2\galleryManager\GalleryManagerAction;
	
	class GalleryController extends \yii\web\Controller
	{
	    
		public function actions()
	    {
	        return [
	            'galleryApi' => [
	                'class' => GalleryManagerAction::className(),
	                // mappings between type names and model classes (should be the same as in behaviour)
	                'types' => [
	                    'portfolio' => Gallery::className()
	                ]
	            ],
	        ];
	    }
	
	    public function actionIndex()
	    {
	        $dataProvider = Gallery::find()->all();
	        return $this->render('index', ['model' => $dataProvider[0]]);
	    }
	
	
	}

