<?php

// skipcash.php

return [
    'client_id' => env('SKIPCASH_CLIENT_ID', ''),
    'key_id' => env('SKIPCASH_KEY_ID', ''),
    'key_secret' => env('SKIPCASH_KEY_SECRET', ''),
    'webhook_key' => env('SKIPCASH_WEBHOOK_KEY', ''),
    'url' => env('SKIPCASH_URL', 'https://api.skipcash.app'),
];
