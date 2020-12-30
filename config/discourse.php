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
     * Discourse Username
     *
     * The username used when creating the API Key, recommendation is Discobot
     */
    'username' => env('DISCOURSE_USERNAME'),

    /**
     * Discourse Category ID to create threads in.
     */
    'category_id' => env('DISCOURSE_CATEGORY_ID'),
];
