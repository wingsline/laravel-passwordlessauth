<?php

return [

    'redirectTo' => 'home',

    /*
    |--------------------------------------------------------------------------
    | Remember login when using passwordless auth
    |--------------------------------------------------------------------------
    |
    | Set to true in order to remember the signed in user.
    |
    */
    'remember' => env('PASSWORDLESS_REMEMBER', true),
];
