<?php
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'name' => 'Twobolts.ru',
    'bootstrap' => ['log'],
    'language' => 'ru-RU',
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'enableCookieValidation' => true,
            'enableCsrfValidation' => true,
            'cookieValidationKey' => 'NoThoughtsNoDreamsNoWishesAndNoFear',
            //'baseUrl' => 'http://tb2.local',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'assetManager' => [
            'appendTimestamp' => YII_ENV_DEV,
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning']
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                'social/auth' => 'social/auth',
                '<alias:index||faq|privacy-policy|terms-and-conditions|testimonials>' => 'site/<alias>',
                '<alias:sign-in|sign-out|reset-password|recover-password|register>' => 'system/<alias>',
                'profile/disconnect/<clientName:[A-Za-z0-9-_.]+>' => 'profile/disconnect',
                'profile/<alias:save|recover-email|edit-password|favorites|delete-car>' => 'profile/<alias>',
                'profile/favorites/<username:[A-Za-z0-9-_.]+>' => 'profile/favorites',
                'profile/<username:[A-Za-z0-9-_.]+>' => 'profile/view',
                'comparison/view/<comparisonId:[A-Za-z0-9-_.]+>' => 'comparison/view',
                'comparison/manufacturer/<manufacturerId:[A-Za-z0-9-_.]+>' => 'comparison/manufacturer',
                'comparison/model/<modelId:[A-Za-z0-9-_.]+>' => 'comparison/model',
                'comparison/user/<username:[A-Za-z0-9-_.]+>' => 'comparison/user',
                'comparison/add/<carId:[A-Za-z0-9-_.]+>' => 'comparison/add',
                'catalog/car-request' => 'catalog/car-request',
                'catalog/get-manufacturer-cars' => 'catalog/get-manufacturer-cars',
                'catalog/manufacturer/<manufacturerId:[A-Za-z0-9-_.]+>' => 'catalog/manufacturer',
                'catalog/<carId:[A-Za-z0-9-_.]+>' => 'catalog/view',
                'news/get-news' => 'news/get-news',
                'news/<newsId:[A-Za-z0-9-_.]+>' => 'news/view',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
                'admin/sign-out' => 'admin/default/sign-out',
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
            ],
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/error' => 'error.php',
                        'app/email' => 'email.php',
                        'app/meta' => 'meta.php',
                        'app/admin' => 'admin.php'
                    ],
                ],
            ],
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
            'clients' => [
                'vkontakte' => [
                    'class' => 'yii\authclient\clients\VKontakte',
                    'clientId' => '4024793',
                    'clientSecret' => 'Qw7HK3TRaWzwEITtP6r5',
                    'scope' => 'offline'
                ],
                'facebook' => [
                    'class' => 'yii\authclient\clients\Facebook',
                    'clientId' => '163335883876279',
                    'clientSecret' => '0df707813edac8f0c83bdffc63b82f56',
                    'scope' => 'public_profile,user_photos,email',
                ],
                'twitter' => [
                    'class' => 'yii\authclient\clients\Twitter',
                    'consumerKey' => 'qblcLgdtuAavV95P7g553g',
                    'consumerSecret' => 'JBdpHMZkGITlJe38Bpaxxq04qnSOJvRHygnzwhCY',
                ],
                'google' => [
                    'class' => 'yii\authclient\clients\Google',
                    'clientId' => '767139963816-sn4pvqhn68nignifunkiml4bpqf8vftb.apps.googleusercontent.com',
                    'clientSecret' => 'LLYTnNcyB76YSwf42MiVAeqT',
                ],
            ],
        ]
    ],
    'modules' => [
        'admin' => [
            'class' => 'app\modules\admin\AdminModule',
        ],
        'dynagrid'=> [
            'class'=>'\kartik\dynagrid\Module',
            // other module settings
        ],
        'gridview'=> [
            'class'=>'\kartik\grid\Module',
            // other module settings
        ],
    ],
    'aliases' => [
        '@PasswordHash' => '@vendor/phpass',
        '@admin' => '@app/modules/admin'
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];


}

return $config;
