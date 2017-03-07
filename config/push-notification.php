<?php

return array(

    'PockeytIOS'     => array(
        'environment' =>'development',
        'certificate' =>'../pushcert.pem',
        'passPhrase'  =>env('APN_PASSWORD'),
        'service'     =>'apns'
    ),
    'PockeytAndroid' => array(
        'environment' =>'production',
        'apiKey'      =>env('GCM_KEY'),
        'service'     =>'gcm'
    )

);