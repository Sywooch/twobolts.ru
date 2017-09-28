<?php
namespace app\models;

use yii;
use yii\base\Model;
use yii\web\UploadedFile;

class Upload extends Model
{
    /**
     * @var UploadedFile
     */
    public $imageFile;

    public $path = 'temp/';

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false],
        ];
    }

    public function upload()
    {
        if ($this->validate()) {
            $this->imageFile->saveAs(Yii::$app->basePath . '/' . Yii::$app->params['webRoot'] . '/uploads/' . $this->path . $this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
}