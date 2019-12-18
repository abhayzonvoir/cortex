<?php

declare(strict_types=1);

return [

    // Tags Database Tables
    'tables' => [

        'tags' => 'tags',
        'taggables' => 'taggables',

    ],

    // Tags Models
    'models' => [
        'tag' => \Cortex\Tags\Models\Tag::class,
    ],

];
