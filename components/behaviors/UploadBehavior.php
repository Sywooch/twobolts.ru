<?php
/**
 * Created by PhpStorm.
 * User: rzyuzin
 * Date: 16.01.2017
 * Time: 16:31
 */

namespace app\components\behaviors;

use mongosoft\file\UploadImageBehavior;
use yii\db\BaseActiveRecord;
use yii\web\UploadedFile;

class UploadBehavior extends UploadImageBehavior
{
    /**
     * @var UploadedFile the uploaded file instance.
     */
    private $_file;

    public function init()
    {
        parent::init();
    }

    /**
     * This method is called at the beginning of inserting or updating a record.
     */
    public function beforeSave()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if (in_array($model->scenario, $this->scenarios)) {
            if ($this->_file instanceof UploadedFile) {
                if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                    if ($this->unlinkOnSave === true) {
                        $this->delete($this->attribute, true);
                    }
                }
                $model->setAttribute($this->attribute, $this->_file->name);
            } else {
                if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                    if ($this->unlinkOnSave === true) {
                        $this->delete($this->attribute, true);
                    }
                }
            }
        } else {
            if (!$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute)) {
                if ($this->unlinkOnSave === true) {
                    $this->delete($this->attribute, true);
                }
            }
        }
    }
}