<?php
namespace app\components;

use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use mongosoft\file\UploadBehavior;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class ImageHelper
{
	const GALLERY_IMG_WIDTH = 320;
	const GALLERY_IMG_HEIGHT = 180;
	const THUMBNAIL_LARGE_WIDTH = 480;
	const THUMBNAIL_LARGE_HEIGHT = 270;

    /**
     * Формирование <img> для аттрибута модели
     *
     * @param ActiveRecord|UploadBehavior $model
     * @param string $attribute
     * @return string
     */
    public static function getImageTag($model, $attribute)
    {
        if ($model->{$attribute} && $imageFile = static::getImageFile($model, $attribute)) {
            return Html::img(UrlHelper::absolute($imageFile));
        }

        return '';
    }

    /**
     * Получение имени файля для аттрибута модели
     *
     * @param ActiveRecord|UploadBehavior $model
     * @param string $attribute
     * @param bool
     * @return string
     */
    public static function getImageFile($model, $attribute, $fullPath = false)
    {
        if ($model->{$attribute}) {
	        // without behavior
	        $file = Yii::getAlias('@webroot') . '/uploads/avatars/' . $model->{$attribute};

	        if (@file_get_contents($file)) {
		        $imageFile = '/uploads/avatars/' . $model->{$attribute};
	        } else {
		        $imageFile = $model->getUploadUrl($attribute);

		        if (!is_file(Yii::getAlias('@webroot') . $imageFile)) {
			        $imageFile = $model->{$attribute};
		        }
	        }

            return $fullPath ? Yii::getAlias('@webroot') . $imageFile : $imageFile;
        }

        return '';
    }

    /**
     * Создает файл эскиза
     *
     * @param $sourceName
     * @param integer
     * @param integer
     * @param bool
     * @return string
     */
    public static function createThumbnailFile($sourceName, $thumbWidth, $thumbHeight, $createOnly = true)
    {
        $thumbUrl = parse_url($sourceName);
        $thumbPath = pathinfo($thumbUrl['path']);
        $outputName = $thumbPath['dirname'] . '/' . $thumbPath['filename'] . '_thumb_' . $thumbWidth . 'x' . $thumbHeight . '.' . $thumbPath['extension'];

        if (!file_exists(Yii::getAlias('@webroot') . $outputName)) {
            $size = getimagesize(Yii::getAlias('@webroot') . $sourceName);
            $ratio = $size[0] / $thumbWidth;
            $height = round($size[1] / $ratio);
            $width = $thumbWidth;

            if ($height < $thumbHeight) {
                $ratio = $size[1] / $thumbHeight;
                $height = $thumbHeight;
                $width = round($size[0] / $ratio);
            }

            $imagine = new Imagine();
            $image = $imagine->open(Yii::getAlias('@webroot') . $sourceName);
            $image->resize(new Box($width, $height));

            $x = $y = 0;

            if ($width > $thumbWidth) {
                $x = ($width - $thumbWidth) / 2;
            }

            if ($height > $thumbHeight) {
                $y = ($height - $thumbHeight) / 2;
            }

            if ($x || $y) {
                $image->crop(new Point($x, $y), new Box($thumbWidth, $thumbHeight));
            }

            $image->save(Yii::getAlias('@webroot') . $outputName);
        }

        if (!$createOnly) {
            return $outputName;
        }

        return '';
    }

    /**
     * Удаляет файл фттрибута модели
     *
     * @param $model
     * @param $attribute
     * @return bool
     */
    public static function deleteImageFile($model, $attribute)
    {
        $file = static::getImageFile($model, $attribute);
        if (is_file(Yii::getAlias('@webroot') . $file)) {
            unlink(Yii::getAlias('@webroot') . $file);

            return true;
        }

        return false;
    }

	/**
	 * @param $source
	 * @param $cropWidth
	 * @param $cropHeight
	 * @param null $x
	 * @param null $y
	 * @param bool $resize
	 */
    public static function crop($source, $cropWidth, $cropHeight, $x = null, $y = null, $resize = true)
    {
	    $imagine = new Imagine();

	    $image = $imagine->open(Yii::getAlias('@webroot') . $source);
	    $width = $image->getSize()->getWidth();
	    $height = $image->getSize()->getHeight();

	    if ($resize) {
		    $ratio = $width / $cropWidth;
		    $height = round($height / $ratio);
		    $width = $cropWidth;

		    if ($height < $cropHeight) {
			    $ratio = $height / $cropHeight;
			    $height = $cropHeight;
			    $width = round($width / $ratio);
		    }

		    $image->resize(new Box($width, $height));
	    }

	    if (is_null($x)) {
		    if ($width > $cropWidth) {
			    $x = ($width - $cropWidth) / 2;
		    } else {
		    	$x = 0;
		    }
	    }

	    if (is_null($y)) {
		    if ($height > $cropHeight) {
			    $y = ($height - $cropHeight) / 2;
		    } else {
		    	$y = 0;
		    }
	    }

	    $image->crop(new Point($x, $y), new Box($cropWidth, $cropHeight));
	    $image->save(Yii::getAlias('@webroot') . $source);
    }
}