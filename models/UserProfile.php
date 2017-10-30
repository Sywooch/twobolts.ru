<?php

namespace app\models;

use yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

/**
 * This is the model class for table "user_profiles".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $country
 * @property string $website
 * @property string $last_name
 * @property string $first_name
 * @property string $city
 * @property string $address
 * @property string $zip_code
 * @property string $phone
 * @property string $about
 * @property bool $notification
 *
 * @property User $user
 */
class UserProfile extends UserDependency
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_profiles';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['last_name', 'first_name', 'city', 'address', 'zip_code', 'phone', 'about'], 'safe'],
	        [['notification'], 'boolean'],
            [['about'], 'string', 'max' => 2048],
            [['country', 'phone', 'website', 'address'], 'string', 'max' => 255],
            [['last_name', 'first_name', 'city', 'zip_code'], 'string', 'max' => 10],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['country', 'city', 'about'], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process'],
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
            'country' => Yii::t('app', 'Country'),
            'website' => Yii::t('app', 'Website'),
            'last_name' => Yii::t('app', 'Last Name'),
            'first_name' => Yii::t('app', 'First Name'),
            'city' => Yii::t('app', 'City'),
            'address' => Yii::t('app', 'Address'),
            'zip_code' => Yii::t('app', 'Zip Code'),
            'phone' => Yii::t('app', 'Phone'),
            'about' => Yii::t('app', 'About self'),
	        'notification' => Yii::t('app', 'Get new notifications on email')
        ];
    }

	/**
	 * @param string $wrapTag
	 *
	 * @return string
	 */
    public function getFullLocation($wrapTag = '')
    {
        $result = [];

        if ($this->zip_code) {
            $result[] = $this->zip_code;
        }

        if ($this->country) {
            $result[] = $this->country;
        }

        if ($this->city) {
            $result[] = $this->city;
        }

        return $wrapTag ? Html::tag($wrapTag, implode(', ', $result)) : implode(', ', $result);
    }

	/**
	 * @return string
	 */
    public function getFullName()
    {
    	$name = $this->last_name;

    	if ($this->first_name) {
    		$name .= ' ';
	    }

	    $name .= $this->last_name;

    	return $name;
    }
}
