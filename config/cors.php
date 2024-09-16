<?php

return [
    'paths' => ['api/*'],  // Define the paths where CORS should be applied, like '/api/*'

    'allowed_methods' => ['*'],  // Allow all HTTP methods (GET, POST, PUT, DELETE, etc.)

    'allowed_origins' => ['*'],  // Allow any origin, you can specify specific domains if needed

    'allowed_origins_patterns' => [],  // You can use regex patterns to allow specific origins

    'allowed_headers' => ['*'],  // Allow all headers

    'exposed_headers' => [],  // Define headers that should be exposed to the front-end

    'max_age' => 0,  // Set the max age for the preflight request

    'supports_credentials' => true,  // If you want to support credentials (cookies, etc.), set to true
];
