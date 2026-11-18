<?php

return [
    'default_breed_id' => env('DEFAULT_QUAIL_BREED_ID', 1),

    // Minimum model confidence required before showing a detected breed.
    'confidence_threshold' => env('QUAIL_CONFIDENCE_THRESHOLD', 0.50),

    /*
     * Reference data for the two classes supported by the trained
     * MobileNetV2 model. Keys are the exact class labels saved in
     * public/models/model_metadata.json.
     *
     * These are general reference ranges for the breed/variety, never
     * measurements or predictions about the individual bird in an image.
     */
    'breed_references' => [
        'japanese_quail_group' => [
            // The trained Japanese group maps to the common Japanese quail
            // breed shown in the app's breed info (quail_breeds ID 1).
            'breed_id' => 1,
            'common_name' => 'Japanese Quail / Coturnix Quail',
            'scientific_name' => 'Coturnix japonica',
            'species' => 'Japanese quail',
            'breed_or_variety' => 'Common domestic Japanese quail / Coturnix quail',
            'typical_adult_weight' => '120.00 g mature weight (typical)',
            'typical_maturity_age' => '42 days (typical)',
            'typical_egg_production' => '280 eggs per hen per year (typical)',
            'optimal_temperature' => '20.5 °C',
            'optimal_humidity' => '57.5 %',
            'description' => 'Most common Japanese quail breed.',
            'reference_note' => 'Most common Japanese quail breed. Weight, maturity, egg production, temperature, and humidity are general breed reference values, not measurements of the individual bird or a DHT22 reading.',
        ],
        'pharaoh_quail' => [
            'breed_id' => 3,
            'common_name' => 'Pharaoh Quail',
            'scientific_name' => 'Coturnix japonica',
            'species' => 'Japanese quail',
            'breed_or_variety' => 'Pharaoh variety (wild-type plumage)',
            'typical_adult_weight' => 'Typical estimate: 100–130 g per adult',
            'typical_maturity_age' => 'Typical estimate: 42–56 days (6–8 weeks)',
            'typical_egg_production' => 'Typical estimate: 200–250 eggs per hen per year',
            'optimal_temperature' => '20.5 °C',
            'optimal_humidity' => '57.5 %',
            'description' => 'Domestic Japanese quail variety with gray to dark gray plumage.',
            'reference_note' => 'A domestic Japanese quail variety identified here by gray to dark gray plumage. Production figures are general reference ranges, not measurements of the detected bird.',
        ],
    ],

    /*
     * Legacy mapping kept for older history records and external callers.
     * New classifications should always use breed_references above.
     */
    'breeds' => [
        'japanese' => [
            'name' => 'Japanese Quail / Coturnix Quail',
            'scientific_name' => 'Coturnix japonica',
            'description' => 'Japanese quail species information; not a breed prediction.'
        ],
        'pharaoh' => [
            'name' => 'Pharaoh Quail',
            'scientific_name' => 'Coturnix japonica',
            'description' => 'Domestic Japanese quail variety with wild-type plumage.'
        ],
        'taiwan_brown' => [
            'name' => 'Japanese Coturnix Quail (Taiwan Crossbreed)',
            'scientific_name' => 'Coturnix japonica',
            'description' => 'Domestic Japanese quail crossbreed or breeding line.'
        ]
    ]
];
