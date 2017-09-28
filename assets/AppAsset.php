<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/fa/css/font-awesome.min.css',
        'css/likely.css',
        'css/site.css',
        'css/forms.css',
        'css/main.css',
        'css/comparison.css',
        'css/comment.css'
    ];

    public $js = [
        'js/jquery/spin.min.js',
        'js/jquery/jquery.actual.min.js',
        'js/likely.js',
        'js/php.js',
        'js/spinner.js',
	    'js/common.js',
        'js/main.js',
        'js/comparison.js',
        'js/comment.js',
        'js/car-tech.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];
}
