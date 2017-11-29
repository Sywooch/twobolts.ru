<?php

namespace app\components\widgets;

use app\components\ArrayHelper;
use app\models\Car;
use app\models\CarRequest;
use app\models\ComparisonCriteria;
use app\models\Engine;
use app\models\Manufacturer;
use app\models\Model;
use yii\base\Widget;

/**
 *
 * Class ComparisonForm
 * @package app\components\widgets
 *
 * @property Car $car
 * @property CarRequest $carRequest
 * @property array $requestData
 *
 * @property Manufacturer[] $manufacturers
 * @property ComparisonCriteria[] $criteria
 *
 * @property integer $mainManufacturerId
 * @property array $mainModels
 * @property integer $mainModelId
 * @property array $mainEngines
 * @property integer $mainEngineId
 * @property string $mainPhoto
 * @property string $mainTime
 * @property boolean $mainState
 *
 * @property integer $compareManufacturerId
 * @property array $compareModels
 * @property integer $compareModelId
 * @property array $compareEngines
 * @property integer $compareEngineId
 * @property string $comparePhoto
 * @property string $compareTime
 * @property boolean $compareState
 *
 * @property array $garage
 * @property array $before
 *
 */
class ComparisonForm extends Widget
{
	public $car;
	public $carRequest;
	public $requestData = [];

	public $criteria;
	public $manufacturers;

	public $mainManufacturerId;
	public $mainModels = [];
	public $mainModelId;
	public $mainEngines = [];
	public $mainEngineId;
	public $mainPhoto;
	public $mainTime;
	public $mainState;

	public $compareManufacturerId;
	public $compareModels = [];
	public $compareModelId;
	public $compareEngines = [];
	public $compareEngineId;
	public $comparePhoto;
	public $compareTime;
	public $compareState;

	public $garage = [];
	public $before = [];

	const TYPE_MAIN = 'main';
	const TYPE_COMPARE = 'compare';

	/**
	 * @return string
	 */
	public function run()
	{
		if ($this->criteria) {
			if ($this->requestData) {
				$this->garage = ArrayHelper::getValue($this->requestData, 'garage', []);
				$this->before = ArrayHelper::getValue($this->requestData, 'before', []);

				$this->load();
			} elseif ($this->car) {
				$this->mainManufacturerId = $this->car->manufacturer_id;
				$this->mainModels = $this->mapModels($this->car->manufacturer);

				$this->mainModelId = $this->car->model_id;
				$this->mainEngines = $this->mapEngines($this->car->model);

				$this->mainEngineId = $this->car->engine_id;
				$this->mainPhoto = Car::getDefaultImage($this->car->getImage());
			}

			return $this->render('comparison_form', [
				'comparison' => $this
			]);
		} else {
			return '';
		}
	}

	/**
	 * Load data
	 */
	public function load()
	{
		$this->loadParts(self::TYPE_MAIN);
		$this->loadParts(self::TYPE_COMPARE);
	}

	/**
	 * @param $type
	 */
	public function loadParts($type)
	{
		$this->{$type . 'ManufacturerId'} = ArrayHelper::getValue($this->requestData, $type . 'ManufacturerId');

		if ($this->{$type . 'ManufacturerId'}) {
			$manufacturer = Manufacturer::findOne($this->{$type . 'ManufacturerId'});
			$this->{$type . 'Models'} = $this->mapModels($manufacturer);
		}

		$this->{$type . 'ModelId'} = ArrayHelper::getValue($this->requestData, $type . 'ModelId');

		if ($this->{$type . 'ModelId'}) {
			$model = Model::findOne($this->{$type . 'ModelId'});
			$this->{$type . 'Engines'} = $this->mapEngines($model);
		}

		$this->{$type . 'EngineId'} = ArrayHelper::getValue($this->requestData, $type . 'EngineId');
		$this->{$type . 'Photo'} = ArrayHelper::getValue($this->requestData, $type . 'Photo');
		$this->{$type . 'Time'} = ArrayHelper::getValue($this->requestData, $type . 'Time');

		$inGarage = in_array($type, $this->garage);
		$inBefore = in_array($type, $this->before);

		if ($inGarage) {
			$this->{$type . 'State'} = true;
		} elseif ($inBefore) {
			$this->{$type . 'State'} = false;
		} else {
			$this->{$type . 'State'} = null;
		}
	}

	/**
	 * @param Manufacturer $manufacturer
	 *
	 * @return array
	 */
	public function mapModels($manufacturer)
	{
		return ArrayHelper::map(
			$manufacturer->getModels(true)->all(),
			'id',
			function($model) {
				/** @var Model $model */
				return $model->getFullName(false);
			}
		);
	}

	/**
	 * @param Model $model
	 *
	 * @return array
	 */
	public function mapEngines($model)
	{
		return ArrayHelper::map(
			$model->getEngines(true)->all(),
			'id',
			function($model) {
				/** @var Engine $model */
				return $model->getName();
			}
		);
	}

	/**
	 * @return bool
	 */
	public function isComparable()
	{
		return $this->mainManufacturerId
		       && $this->mainModelId
		       && $this->mainEngineId
		       && $this->mainTime
		       && $this->compareManufacturerId
		       && $this->compareModelId
		       && $this->compareEngineId
		       && $this->compareTime;
	}

	/**
	 * @param $type
	 *
	 * @return string
	 */
	public function carTitle($type)
	{
		if ($this->isComparable()) {
			$car = Car::findByParts($this->{$type . 'ManufacturerId'}, $this->{$type . 'ModelId'}, $this->{$type . 'EngineId'});

			if ($car) {
				return '<p>' . $car->manufacturer->name . '</p>' .
				       '<p>' . $car->model->getFullName(false) . '</p>' .
				       '<p>' . $car->engine->getName() . '</p>';
			}
		}

		return '';
	}

	/**
	 * @param ComparisonCriteria $criteria
	 * @param string $type
	 *
	 * @return integer
	 */
	public function activeCriteriaPoint($criteria, $type)
	{
		$requestCriteria = ArrayHelper::getValue($this->requestData, 'criteria');

		if ($requestCriteria) {
			foreach ($requestCriteria as $item)
			{
				if ($item['criteria_id'] == $criteria->id) {
					return $item['criteria_' . $type . '_value'];
				}
			}
		}

		return 0;
	}

	/**
	 * @param ComparisonCriteria $criteria
	 *
	 * @return string
	 */
	public function activeCriteriaComment($criteria)
	{
		$requestCriteria = ArrayHelper::getValue($this->requestData, 'criteria');

		if ($requestCriteria) {
			foreach ($requestCriteria as $item)
			{
				if ($item['criteria_id'] == $criteria->id) {
					return $item['criteria_comment'];
				}
			}
		}

		return '';
	}
}