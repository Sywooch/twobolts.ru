<?php
/**
 * Created by PhpStorm.
 * User: rzyuzin
 * Date: 18.07.2016
 * Time: 17:43
 */

namespace app\components;


class ArrayHelper extends \yii\helpers\ArrayHelper
{
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
}