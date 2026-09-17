<?php

return [
    'default_breed_id' => env('DEFAULT_QUAIL_BREED_ID', 1),
    
    'breeds' => [
        'japanese' => [
            'name' => 'Japanese Quail (Coturnix Japonica)',
            'scientific_name' => 'Coturnix japonica',
            'description' => 'Most common commercial quail breed in the Philippines'
        ],
        'pharaoh' => [
            'name' => 'Pharaoh Quail',
            'scientific_name' => 'Coturnix coturnix pharaoh',
            'description' => 'Brown-colored strain of Japanese quail'
        ],
        'taiwan_brown' => [
            'name' => 'Japanese Coturnix Crossbreed (Taiwan Brown Line)',
            'scientific_name' => 'Coturnix japonica (Taiwan strain)',
            'description' => 'Improved strain from Taiwan with enhanced production'
        ]
    ]
];