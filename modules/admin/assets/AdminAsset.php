<?php
namespace app\modules\admin\assets;

use app\assets\AppAsset;

class AdminAsset extends AppAsset
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/fa/css/font-awesome.min.css',
        'css/site.css',
        'css/forms.css',
        'css/admin.css',
    ];

    public $js = [
        'js/jquery/spin.min.js',
        'js/php.js',
        'js/spinner.js',
	    'js/common.js',
        'js/car-tech.js',
        'js/admin.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    public function init()
    {
        parent::init();
        $this->publishOptions['forceCopy'] = true;
    }
}