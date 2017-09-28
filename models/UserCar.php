<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "users_cars".
 *
 * @property string $id
 * @property integer $user_id
 * @property integer $car_id
 * @property string $type
 * @property string $image
 *
 * @property User $user
 * @property Car $car
 */
class UserCar extends UserDependency
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users_cars';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['car_id', 'type'], 'required'],
            [['image'], 'image', 'skipOnEmpty' => true],
            [['car_id'], 'integer'],
            [['type'], 'string', 'max' => 16],
            [['image'], 'string', 'max' => 255],
            [['car_id'], 'exist', 'skipOnError' => true, 'targetClass' => Car::className(), 'targetAttribute' => ['car_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'User ID'),
            'car_id' => Yii::t('app', 'Car ID'),
            'type' => Yii::t('app', 'Type'),
            'image' => Yii::t('app', 'Image'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCar()
    {
        return $this->hasOne(Car::className(), ['id' => 'car_id']);
    }
}
