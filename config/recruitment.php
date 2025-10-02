<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Canonical taxonomies for Openings
    |--------------------------------------------------------------------------
    | Edit these to match your business.
    */

    'specialties' => [
        // Medical device examples — change/add as you like
        'Cardiology', 'Orthopedics', 'Spine', 'Dental', 'ENT', 'Urology',
        'Neurology', 'Radiology', 'Oncology', 'Wound Care', 'Surgical Instruments',
        'Imaging', 'Diabetes', 'Respiratory', 'Dermatology', 'Ophthalmology',
    ],

    'territories' => [
        // US states — trim if you only use regions
        'Alabama','Alaska','Arizona','Arkansas','California','Colorado','Connecticut',
        'Delaware','Florida','Georgia','Hawaii','Idaho','Illinois','Indiana','Iowa',
        'Kansas','Kentucky','Louisiana','Maine','Maryland','Massachusetts','Michigan',
        'Minnesota','Mississippi','Missouri','Montana','Nebraska','Nevada','New Hampshire',
        'New Jersey','New Mexico','New York','North Carolina','North Dakota','Ohio',
        'Oklahoma','Oregon','Pennsylvania','Rhode Island','South Carolina','South Dakota',
        'Tennessee','Texas','Utah','Vermont','Virginia','Washington','West Virginia',
        'Wisconsin','Wyoming',
    ],
    'work_auth' => [
        ['label' => 'US Citizen',         'value' => 'US_CITIZEN'],
        ['label' => 'Permanent Resident', 'value' => 'GREEN_CARD'],
        ['label' => 'H-1B / Visa',        'value' => 'VISA_H1B'],
        ['label' => 'Other',              'value' => 'OTHER'],
    ],
];
