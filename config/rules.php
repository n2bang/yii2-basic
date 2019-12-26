<?php

return [
    [
        'class' => 'yii\rest\UrlRule',
        'pluralize' => false,
        'controller' => ['v1/project'],
        'tokens' => [
            '{id}' => '<id:\w+>'
        ],
        'extraPatterns' => [
            'POST register' => 'register',
        ],
    ],
    'register' => 'site/register',
    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    '<controller:\w+>/<id:\d+>/<slug:\w+>' => '<controller>/view',
];