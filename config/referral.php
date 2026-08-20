<?php

return [
    'monthly_limit' => 5,
    'points_per_referral' => 10,

    'rewards' => [
        'half_session' => [
            'key' => 'half_session',
            'label' => 'Free Half Session (Diskon 50%)',
            'points' => 20,
            'discount_percent' => 50,
        ],
        'full_session' => [
            'key' => 'full_session',
            'label' => 'Free 1 Session (Gratis 100%)',
            'points' => 40,
            'discount_percent' => 100,
        ],
    ],
];
