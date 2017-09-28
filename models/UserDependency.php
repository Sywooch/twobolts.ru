<?php
namespace app\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\db\ActiveRecord;

class UserDependency extends ActiveRecord
{
    public $updated_by;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'user' => [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'user_id',
                'updatedByAttribute' => 'updated_by',
            ]
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}