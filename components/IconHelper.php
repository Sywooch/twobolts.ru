<?php
/**
 * Created by PhpStorm.
 * User: rzyuzin
 * Date: 03.06.2016
 * Time: 16:31
 */

namespace app\components;

use yii\helpers\Html;

class IconHelper
{
    private static $icons = [
        'add' => 'fa-plus-circle',
        'edit' => 'fa-pencil',
        'delete' => 'fa-trash',
        'thumb_up' => 'fa-thumbs-up',
        'thumb_down' => 'fa-thumbs-down',
        'comment' => 'fa-comment',
        'comments' => 'fa-comments',
        'commenting' => 'fa-commenting-o',
        'reset-comment' => 'fa-comment-o',
        'eye' => 'fa-eye',
        'checked' => 'fa-check',
        'triangle_down' => 'fa-caret-down',
        'triangle_up' => 'fa-caret-up',
        'upload' => 'fa-upload',
        'calendar' => 'fa-calendar',
        'bar-chart' => 'fa-bar-chart',
        'password' => 'fa-key',
        'info' => 'fa-info-circle',
        'attention' => 'fa-exclamation-triangle',
        'star' => 'fa-star',
        'star-open' => 'fa-star-o',
        'clone' => 'fa-clone',
        'lock' => 'fa-lock',
        'refresh' => 'fa-refresh',
        'recycle' => 'fa-recycle',
        'home' => 'fa-home',
        'setting' => 'fa-cog',
        'industry' => 'fa-industry',
        'criteria' => 'fa-server',
        'comparison' => 'fa-sliders',
        'news' => 'fa-newspaper-o',
        'rating' => 'fa-line-chart',
        'change' => 'fa-exchange',
        'external-link' => 'fa-external-link',
        'save' => 'fa-floppy-o',
        'exit' => 'fa-sign-out',
        'dashboard' => 'fa-dashboard',
        'cubes' => 'fa-cubes',
        'flask' => 'fa-flask',
        'car' => 'fa-car',
        'user' => 'fa-user'
    ];

    public static function show($icon, $options = [])
    {
        $icon = ArrayHelper::getValue(self::$icons, $icon);

        if ($icon) {
            if (isset($options['class'])) {
                $options['class'] = $options['class'] . ' fa ' . $icon;
            } else {
                $options['class'] = 'fa ' . $icon;
            }

            return Html::tag('i', '', $options);
        } else {
            return '';
        }
    }
}