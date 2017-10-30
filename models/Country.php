<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "country".
 *
 * @property integer $id
 * @property string $name
 * @property string $iso_2
 * @property string $iso_3
 * @property string $numeric
 */
class Country extends ActiveRecord
{
	/**
	 * @inheritdoc
	 */
	public static function tableName()
	{
		return 'country';
	}

	/**
	 * @inheritdoc
	 */
	public function rules()
	{
		return [
			[['name'], 'string', 'max' => 255],
			[['iso_2'], 'string', 'max' => 2],
			[['iso_3'], 'string', 'max' => 3],
			[['numeric'], 'string', 'max' => 4],
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Name',
			'iso_2' => 'Iso 2',
			'iso_3' => 'Iso 3',
			'numeric' => 'Numeric',
		];
	}

	/**
	 * @return array
	 */
	public static function filterData()
	{
		return self::find()
			->orderBy(['name' => SORT_ASC])
			->asArray()
			->all();
	}
}