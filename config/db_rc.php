<?php

return [
    'class' => 'yii\db\Connection',
    'dsn' => 'mysql:host=twobolts.mysql;dbname=twobolts_rc',
    'username' => 'twobolts_mysql',
    'password' => 'fi9aygsv',
    'charset' => 'utf8',
    'enableSchemaCache' => true,
    // Duration of schema cache.
    'schemaCacheDuration' => 3600,
    // Name of the cache component used to store schema information
    'schemaCache' => 'cache',
];
