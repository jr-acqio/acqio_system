<?php

// if (\App::environment('local')) {
//   $allowedOrigins = ['localhost:8000'];
// }else{
//   $allowedOrigins= ['boletos.acqio.co'];
// }

return [
    /*
     |--------------------------------------------------------------------------
     | Laravel CORS
     |--------------------------------------------------------------------------
     |
     | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
     | to accept any value.
     |
     */
    'supportsCredentials' => false,
    'allowedOrigins' => /*['localhost:8000',*/ ['boletos.acqio.co'],
    // 'allowedOrigins' => $allowedOrigins,
    'allowedHeaders' => ['*'],
    'allowedMethods' => ['GET'],
    'exposedHeaders' => [],
    'maxAge' => 0,
];
