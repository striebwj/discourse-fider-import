<?php

return [

    /**
     * Discourse Url (example: https://forums.classicpress.net)
     */
    'base_url' => env('DISCOURSE_URL'),

    /**
     * Discourse API Key
     *
     * Best practice is to go to your sites api settings, add a new api key
     * with the Discobot permissions, and ability to write new posts.
     */
    'key' => env('DISCOURSE_KEY'),

    /**
     * Discourse Category ID to create threads in.
     */
    'category_id' => env('DISCOURSE_CATEGORY_ID'),
];
