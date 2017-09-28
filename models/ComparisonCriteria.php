<?php

namespace app\models;

use app\components\behaviors\UploadBehavior;
use app\components\ImageHelper;
use Yii;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "comparisons_criteria".
 *
 * @property integer $id
 * @property string $name
 * @property string $placeholder
 * @property integer $sort_order
 * @property integer $show_on_home
 * @property string $icon
 *
 */
class ComparisonCriteria extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => UploadBehavior::className(),
                'attribute' => 'icon',
                'scenarios' => ['insert', 'update', 'delete'],
                'path' => '@webroot/uploads/icons',
                'url' => '@web/uploads/icons',
                'unlinkOnDelete' => true
            ],
        ]);
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'comparisons_criteria';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['sort_order', 'show_on_home'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['placeholder'], 'string', 'max' => 255],
            ['icon', 'image', 'extensions' => 'jpg, jpeg, gif, png', 'on' => ['insert', 'update']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'Criteria ID'),
            'name' => Yii::t('app/admin', 'Criteria Name'),
            'placeholder' => Yii::t('app/admin', 'Criteria Placeholder'),
            'sort_order' => Yii::t('app', 'Criteria Order'),
            'show_on_home' => Yii::t('app/admin', 'Show On Home'),
            'icon' => Yii::t('app/admin', 'Criteria Icon'),
        ];
    }

    /**
     * Изображение иконки
     *
     * @return string
     */
    public function getIcon()
    {
        return ImageHelper::getImageTag($this, 'icon');
    }

    /**
     * Удаляет файл иозбражения и очищает свойство
     *
     * @return $this
     */
    public function deleteIcon()
    {
        if (ImageHelper::deleteImageFile($this, 'icon')) {
            $this->icon = '';
        }

        return $this;
    }
}
