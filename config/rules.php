<?php

return [
    '' => 'site/index',
    'api/v1/project/create'=>'v1/project/create',
    'api/v1/project/<id:\d+>'=>'v1/project/get-item',
    'api/v1/project'=>'v1/project/index',
    'api/v1/docs'=>'v1/default/docs',
    'api/v1/default/json-schema'=>'v1/default/json-schema',
    'api/v1/accesstoken'=>'v1/default/accesstoken',
    'api/v1/refresh-access-token'=>'v1/default/refresh-access-token',
    'api/v1/logout'=>'v1/default/logout',
    'api/v1/register'=>'v1/default/register',
    'register' => 'site/register',
    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
    '<controller:\w+>/<id:\d+>/<slug:\w+>' => '<controller>/view',
];