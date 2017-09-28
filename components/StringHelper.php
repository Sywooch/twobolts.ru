<?php

namespace app\components;

class StringHelper extends \yii\helpers\StringHelper
{
    public static function ellipsize($string, $length, $position = 1, $ellipsis = '&hellip;')
    {
        // Strip tags
        $str = trim(strip_tags($string));

        // Is the string long enough to ellipsize?
        if (mb_strlen($str) <= $length) {
            return $str;
        }

        $beg = mb_substr($str, 0, floor($length * $position));
        $position = ($position > 1) ? 1 : $position;

        if ($position === 1) {
            $end = mb_substr($str, 0, -($length - mb_strlen($beg)));
        } else {
            $end = mb_substr($str, -($length - mb_strlen($beg)));
        }

        return $beg . $ellipsis . $end;
    }
}