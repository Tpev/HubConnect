<?php

return [
    'provider' => env('GEO_PROVIDER', 'google'),

    'google' => [
        'key' => env('GOOGLE_PLACES_API_KEY', ''),
        // REST endpoints (Places Autocomplete & Details)
        'autocomplete_url' => 'https://maps.googleapis.com/maps/api/place/autocomplete/json',
        'details_url'      => 'https://maps.googleapis.com/maps/api/place/details/json',
        // What we’ll ask Google for in Autocomplete:
        // We'll filter locally by prediction.types containing these:
        'accepted_types'   => [
            'country',                      // Country
            'administrative_area_level_1',  // State / Province
            'locality',                     // City
        ],
        // Fields to request in Place Details (keep it lean)
        'details_fields'   => 'address_component,geometry/location,name',
    ],

    'locale'       => env('GEO_DEFAULT_LOCALE', 'en'),
    'bias_country' => env('GEO_DEFAULT_BIAS_COUNTRY', 'US'),
];
