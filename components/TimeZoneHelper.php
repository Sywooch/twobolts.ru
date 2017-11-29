<?php
/**
 * Created by PhpStorm.
 * User: rzyuzin
 * Date: 12.10.2017
 * Time: 13:24
 */

namespace app\components;

use DateTime;
use DateTimeZone;
use IntlTimeZone;
use Yii;

class TimeZoneHelper
{
	const DEFAULT_TIMEZONE = 'Europe/Moscow';

	/**
	 * @return array
	 */
	public static function timeZones()
	{
		date_default_timezone_set('UTC');

		$identifiers = DateTimeZone::listIdentifiers();

		foreach($identifiers as $i)
		{
			// create date time zone from identifier
			$dtz = new DateTimeZone($i);

			// create timezone from identifier
			$tz = IntlTimeZone::createTimeZone($i);
			// if IntlTimeZone is unaware of timezone ID, use identifier as name, else use localized name

			if ($tz->getID() === 'Etc/Unknown' or $i === 'UTC') $name = $i;
			else $name =  $tz->getDisplayName(false, 3, Yii::$app->language);

			// time offset
			$offset = $dtz->getOffset(new DateTime());
			$sign   = ($offset < 0) ? '-' : '+';

			$tzs[] = [
				'code'   => $i,
				'name'   => '(UTC' . $sign . date('H:i', abs($offset)) . ') ' . $name,
				'offset' => $offset,
			];
		}

		ArrayHelper::multisort($tzs, ['offset', 'name']);

		return array_column($tzs, 'name', 'code');
	}

	/**
	 * @param $value
	 *
	 * @return false|string
	 */
	public static function timezoneDate($value)
	{
		$dt = new DateTime($value);
		$tz = new DateTimeZone($dt->getTimezone()->getName());
		$diff = $tz->getOffset($dt) / 3600;

		if ($diff > 0) {
			return date('Y-m-d H:i:s', strtotime($value . ' - ' . $diff . ' hours'));
		} else {
			return date('Y-m-d H:i:s', strtotime($value . ' + ' . (-1 * $diff) . ' hours'));
		}
	}
}