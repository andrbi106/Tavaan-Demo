<?php

/**
 * تَوان | Tavaan — Web Routes (Demo v0.0.1)
 */

return [
    'GET /' => 'HomeController@index',
    'GET /api' => 'ApiController@status',
    'GET /info' => 'ApiController@phpInfo',
];
