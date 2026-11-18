<?php

return [
    'default_breed_id' => env('DEFAULT_QUAIL_BREED_ID', 1),

    // Minimum model confidence required before showing a detected breed.
    'confidence_threshold' => env('QUAIL_CONFIDENCE_THRESHOLD', 0.60),

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
            'breed_id' => 2,
            'common_name' => 'Japanese Coturnix Quail (Taiwan Crossbreed)',
            'scientific_name' => 'Coturnix japonica',
            'species' => 'Japanese quail',
            'breed_or_variety' => 'Taiwan crossbreed / breeding line',
            'typical_adult_weight' => 'Typical estimate: 100–135 g per adult',
            'typical_maturity_age' => 'Typical estimate: 42–56 days (6–8 weeks)',
            'typical_egg_production' => 'Typical estimate: 250–300 eggs per hen per year',
            'reference_note' => 'A crossbreed or breeding line identified by its breeding background. Weight, maturity, and egg production vary with genetics, sex, age, diet, lighting, and management.',
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
            'reference_note' => 'A domestic Japanese quail variety associated with brown, streaked wild-type plumage. Production figures are general reference ranges, not measurements of the detected bird.',
        ],
    ],

    /*
     * Legacy mapping kept for older history records and external callers.
     * New classifications should always use breed_references above.
     */
    'breeds' => [
        'japanese' => [
            'name' => 'Japanese Quail (Coturnix Japonica)',
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
