<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "bodies".
 *
 * @property integer $body_id
 * @property string $body_name
 * @property string $body_url_title
 *
 * @property Model[] $models
 */
class Body extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'bodies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body_name'], 'required'],
            [['body_url_title'], 'safe'],
            [['body_url_title'], 'string'],
            [['body_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'body_id' => Yii::t('app', 'Body ID'),
            'body_name' => Yii::t('app/admin', 'Name'),
            'body_url_title' => Yii::t('app', 'Body Url Title'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getModels()
    {
        return $this->hasMany(Model::className(), ['body_id' => 'body_id']);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->body_name ? $this->body_name : '';
    }

    /**
     * @return array|ActiveRecord[]
     */
    public static function filterData()
    {
        $query = self::find();

        return $query->orderBy(['body_name' => SORT_ASC])->all();
    }
}
