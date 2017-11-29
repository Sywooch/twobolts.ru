<?php

namespace app\components;

/**
 * Class ArrayHelper
 * @package app\components
 */
class ArrayHelper extends \yii\helpers\ArrayHelper
{
	/**
	 * @param $haystack
	 * @param string $delimiter
	 *
	 * @return string
	 */
    public static function toString($haystack, $delimiter = '; ')
    {
        $result = [];
        
        if (is_array($haystack)) {
            foreach ($haystack as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $result[] = static::toString($value, $delimiter);
                } else {
                    $result[] = $value;
                }
            }
        }

        return implode($delimiter, $result);
    }

	/**
	 * Рекурсивный поиск элемента массива по значению
	 *
	 * @param string $needle
	 * @param array $haystack
	 * @return bool|int|string
	 */
	public static function elementByValue($needle, $haystack)
	{
		foreach ($haystack as $key => $value)
		{
			$currentKey = $key;
			if ($needle === $value || (is_array($value) && self::elementByValue($needle, $value) !== false)) {
				return $currentKey;
			}
		}

		return false;
	}
}