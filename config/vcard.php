<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Collection & Assets
    |--------------------------------------------------------------------------
    */
    'collection'      => 'vcards',
    'asset_container' => 'assets',
    'asset_folder'    => 'qrcodes',
    'route_prefix'    => 'vcards',

    /*
    |--------------------------------------------------------------------------
    | QR Code
    |--------------------------------------------------------------------------
    */
    'qr_size'   => 400,
    'qr_margin' => 10,
    'qr_color'  => [26, 35, 126], // RGB — navy blue

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    | These values are passed to the template. Override after publishing.
    */
    'brand' => [
        'name'         => 'My Company',
        'tagline'      => '',
        'color_bg'     => '#b3d4f5',
        'color_dark'   => '#1a237e',
        'color_bright' => '#1e90ff',
        'color_mid'    => '#1565c0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Field handles
    |--------------------------------------------------------------------------
    | Map the addon's internal field names to your blueprint's actual handles.
    | Change these if you rename fields in the blueprint.
    */
    'fields' => [
        'name'     => 'title',
        'role'     => 'job_description',
        'mobile'   => 'mobile_phone',
        'phone'    => 'phone_number',
        'email'    => 'email',
        'website'  => 'website',
        'address'  => 'address',
        'org'      => 'org',
        'qr_code'  => 'qr_code',
    ],

];
