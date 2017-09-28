<?php

namespace app\controllers;

use app\models\Upload;
use Imagine\Exception\Exception;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Imagine\Image\Point;
use yii;
use yii\web\Response;
use yii\web\UploadedFile;

class UploaderController extends BaseController
{
    public function init()
    {
        parent::init();
        $this->enableCsrfValidation = false;
    }

    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        $model = new Upload();
        
        if (Yii::$app->request->isPost) {
            $model->imageFile = UploadedFile::getInstance($model, 'imageFile');

            if ($model->upload()) {
                echo json_encode([
                    'result' => "file_uploaded",
                    'resultCode' => 'ok',
                    'fileName' =>  $model->imageFile
                ]);
                
                exit;
            }
        }

        echo json_encode([
            'result' => $model->getErrors(),
            'resultCode' => 'failed',
            'fileName' =>  ''
        ]);
    }

    public function actionCrop()
    {
        $src = Yii::$app->request->post('src');
        $x = Yii::$app->request->post('x');
        $y = Yii::$app->request->post('y');
        $width = Yii::$app->request->post('width');
        $height = Yii::$app->request->post('height');
        $cropWidth = Yii::$app->request->post('cropWidth');
        $cropHeight = Yii::$app->request->post('cropHeight');
        $dirName = Yii::$app->request->post('dirName');

        $path = pathinfo($src);
        $baseName = $path['basename'];
        $distName = time() . '.' . $path['extension'];
        $sourceImage = Yii::$app->basePath . '/' . Yii::$app->params['webRoot'] . '/uploads/temp/'.$baseName;
        $distImage = Yii::$app->basePath . '/' . Yii::$app->params['webRoot'] . '/uploads/'. ($dirName ? $dirName . '/' : '') . $distName;

        try {
            $imagine = new Imagine();
            $image = $imagine->open($sourceImage);
            $image->crop(new Point($x, $y), new Box($width, $height))
                ->resize(new Box($cropWidth, $cropHeight))
                ->save($distImage);

            unlink($sourceImage);
            
            echo json_encode([
                'src' => 'uploads/'. ($dirName ? $dirName . '/' : '') . $distName,
                'name' => $distName
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'error' => $e->getMessage()
            ]);
        }
    }
}
