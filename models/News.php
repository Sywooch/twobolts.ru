<?php

namespace app\models;

use app\components\ArrayHelper;
use app\components\ImageHelper;
use app\components\UrlHelper;
use dosamigos\gallery\Gallery;
use mongosoft\file\UploadBehavior;
use mongosoft\file\UploadImageBehavior;
use yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\Html;
use yii\helpers\StringHelper;

/**
 * This is the model class for table "news".
 *
 * @property integer $id
 * @property string $created
 * @property string $modified
 * @property string $title
 * @property string $content
 * @property string $excerpt
 * @property integer $include_excerpt
 * @property string $featured_image
 * @property string $video
 * @property string $gallery
 * @property string $source
 * @property string $featured_image_caption
 * @property integer $user_id
 * @property integer $num_views
 * @property string $url_title
 *
 * @property User $user
 * @property Model[] $models
 * @property Manufacturer[] $manufacturers
 * @property Comment[] $comments
 */
class News extends UserDependency
{
    const NEWS_PER_PAGE = 9;
    const NEWS_LARGE = 3;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'modified',
                'updatedAtAttribute' => 'modified',
                'value' => date('Y-m-d H:i:s')
            ],
            [
                'class' => UploadImageBehavior::className(),
                'attribute' => 'featured_image',
                'scenarios' => ['insert', 'update'],
                'path' => '@webroot/uploads/news',
                'url' => '@web/uploads/news',
                'unlinkOnDelete' => true
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'news';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'created'], 'required'],
            [['created', 'modified', 'excerpt', 'include_excerpt', 'video', 'gallery', 'source', 'featured_image_caption', 'url_title'], 'safe'],
            [['title', 'content', 'excerpt', 'video', 'gallery', 'source', 'featured_image_caption', 'url_title'], 'string'],
            [['include_excerpt', 'user_id'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['featured_image', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'created' => Yii::t('app', 'Publish Date'),
            'modified' => Yii::t('app', 'Modified'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'excerpt' => Yii::t('app', 'Excerpt'),
            'include_excerpt' => Yii::t('app', 'Include Excerpt'),
            'featured_image' => Yii::t('app', 'Featured Image'),
            'video' => Yii::t('app', 'Video'),
            'gallery' => Yii::t('app', 'Gallery'),
            'source' => Yii::t('app', 'Source'),
            'featured_image_caption' => Yii::t('app', 'Featured Image Caption'),
            'user_id' => Yii::t('app', 'Author'),
            'num_views' => Yii::t('app', 'Num Views'),
            'url_title' => Yii::t('app', 'Url Title'),
        ];
    }

    public function getExcerpt()
    {
        return $this->excerpt ? $this->excerpt : StringHelper::truncateWords(strip_tags($this->content), 20);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModels()
    {
        return $this->hasMany(Model::className(), ['id' => 'model_id'])
            ->viaTable('news_models', ['news_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getManufacturers()
    {
        return $this->hasMany(Manufacturer::className(), ['id' => 'manufacturer_id'])
            ->viaTable('news_models', ['news_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['object_id' => 'id'])
            ->andWhere(['object' => self::className()])
            ->orderBy(['created' => SORT_ASC]);
    }

    /**
     * @return int
     */
    public function getCommentsCount()
    {
        return $this->hasMany(Comment::className(), ['object_id' => 'id'])
            ->andWhere(['object' => self::className()])
            ->count();
    }

    /**
     * @param bool
     * @return string
     */
    public function getUrl($full = true)
    {
        $url = $this->url_title ? $this->url_title : $this->id;
        return $full ? UrlHelper::absolute('news/' . $url) : $url;
    }

    /**
     * @param $url
     * @return array|null|yii\db\ActiveRecord|self
     */
    public static function findByUrl($url)
    {
        return News::find()
            ->where('url_title = :url', [':url' => $url])
            ->one();
    }

    /**
     * Отрисовка галереи новсти
     *
     * @return string
     */
    public function renderGallery()
    {
        if ($this->gallery) {
            $gallery = json_decode($this->gallery);
            $items = [];

            foreach ($gallery as $item)
            {
                $thumb = ImageHelper::createThumbnailFile($item, ImageHelper::GALLERY_IMG_WIDTH, ImageHelper::GALLERY_IMG_HEIGHT, false);

                $items[] = [
                    'url' => UrlHelper::absolute($item),
                    'src' => UrlHelper::absolute($thumb),
                ];
            }

            return Gallery::widget([
                'items' => $items,
                'options' => [
                    'class' => 'news-view-gallery-wrapper'
                ]
            ]);
        }

        return '';
    }

    /**
     * Формирует эскиз для изображения
     *
     * @param string $size
     * @return string
     */
    public function getThumbnailImage($size = 'small')
    {
        if ($this->featured_image) {
            $imageFile = ImageHelper::getImageFile($this, 'featured_image');

            switch ($size)
            {
                case 'small':
                    $thumbWidth = ImageHelper::GALLERY_IMG_WIDTH;
                    $thumbHeight = ImageHelper::GALLERY_IMG_HEIGHT;
                    break;
                default:
                    $thumbWidth = ImageHelper::THUMBNAIL_LARGE_WIDTH;
                    $thumbHeight = ImageHelper::THUMBNAIL_LARGE_HEIGHT;
                    break;
            }
            $thumb = ImageHelper::createThumbnailFile($imageFile, $thumbWidth, $thumbHeight, false);

            return Html::img(UrlHelper::absolute($thumb));
        }

        return '';
    }

    /**
     * Формирование связей с производителями и моделями
     *
     * @return $this
     */
    public function setModelsRelations()
    {
        $ids = Yii::$app->request->post('news_models_ids', []);
        $types = Yii::$app->request->post('news_models_types', []);

        if ($ids) {
            foreach ($ids as $key => $id) {
                $link = $types[$key] . '_id';

                $newsModel = NewsModels::find()
                    ->where([
                        'news_id' => $this->id,
                        $link => $id
                    ])
                    ->one();

                if (!$newsModel) {
                    $newsModel = new NewsModels();
                    $newsModel->news_id = $this->id;
                    $newsModel->{$link} = $id;
                    $newsModel->save();
                }
            }
        } else {
            NewsModels::deleteAll(['news_id' => $this->id]);
        }

        return $this;
    }

    /**
     * @param bool $path
     * @return string
     */
    public function getFeaturedImage($path = false)
    {
        // without behavior
        $file = Yii::getAlias('@webroot') . $this->featured_image;

        if (@file_get_contents($file)) {
            return $path ? $file : UrlHelper::absolute($this->featured_image);
        }

        // with behavior
        /** @var self|UploadBehavior $this */
        $file = Yii::getAlias('@webroot') . $this->getUploadUrl('featured_image');
        if (file_get_contents($file)) {
            return $path ? $file : UrlHelper::absolute($this->getUploadUrl('featured_image'));
        }

        return '';
    }

    /**
     * Удаляет файл иозбражения и очищает свойство
     *
     * @return $this
     */
    public function deleteFeaturedImage()
    {
        if (ImageHelper::deleteImageFile($this, 'featured_image')) {
            $this->featured_image = '';
        }

        return $this;
    }

    /**
     * Обрабатывает массив файлов галереи и преобразует его в JSON для сохранения
     *
     * @return $this
     */
    public function prepareGallery()
    {
        $post = Yii::$app->request->post('gallery', []);

        if ($post) {
            $gallery = [];

            foreach ($post as $item) {
                $pos = strpos($item, 'temp');
                if ($pos === false) {
                    $gallery[] = $item;
                    continue;
                }

                $path = pathinfo($item);
                $newName = '/uploads/' . $path['basename'];

                if (copy(Yii::getAlias('@webroot') . $item, Yii::getAlias('@webroot') . $newName)) {
                    $gallery[] = $newName;
                }
            }

            $this->gallery = json_encode($gallery);
        }

        return $this;
    }

    /**
     * Удаляет файлы галереи
     *
     * @return $this
     */
    public function deleteGallery()
    {
        $gallery = $this->gallery ? json_decode($this->gallery) : [];

        foreach ($gallery as $item)
        {
            unlink(Yii::getAlias('@webroot') . $item);
        }

        return $this;
    }

    /**
     * Удаляет файл галереи
     *
     * @return $this
     */
    public function deleteGalleryImage()
    {
        $file = Yii::$app->request->post('key');

        if (is_file(Yii::getAlias('@webroot') . $file)) {
            unlink(Yii::getAlias('@webroot') . $file);

            $gallery = $this->gallery ? json_decode($this->gallery) : [];
            $keyToDelete = false;

            foreach ($gallery as $key => $item)
            {
                if ($item == $file) {
                    $keyToDelete = $key;
                    break;
                }
            }

            if ($keyToDelete !== false) {
                unset($gallery[$keyToDelete]);
            }

            $this->gallery = json_encode(array_values($gallery));
        }

        return $this;
    }

    /**
     * Загружает изображение к новости
     * Обрабатывает галерею
     *
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        parent::beforeSave($insert);

        $this->prepareGallery();

        return true;
    }

    /**
     * После успешного сохранения новости, вызывает процедуру формирования связей с производителями и моделями
     *
     * @param bool $insert
     * @param array $changedAttributes
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        if (Yii::$app->request->post($this->formName())) {
            $this->setModelsRelations();
        }
    }

    /**
     * Вызывает процедуру удаления файлов изображения и галереи перед удалением новости
     *
     * @return bool
     */
    public function beforeDelete()
    {
        parent::beforeDelete();

        $this->deleteFeaturedImage()->deleteGallery();

        return true;
    }
}
