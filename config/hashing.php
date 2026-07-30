<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | Argon2id ist der aktuelle Empfehlungsstandard (OWASP) für Passwort-Hashes.
    | Bestehende bcrypt-Hashes bleiben gültig; Laravel rehashed bei Login.
    |
    | Supported: "bcrypt", "argon", "argon2id"
    |
    */

    'driver' => env('HASH_DRIVER', 'argon2id'),

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        // verify=false: alte Hashes anderer Algorithmen nicht abweisen (Migration)
        'verify' => env('HASH_VERIFY', false),
        'limit' => env('BCRYPT_LIMIT', null),
    ],

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 4),
        // Wichtig: false, sonst werfen bestehende bcrypt-Hashes einen 500-Fehler
        'verify' => env('HASH_VERIFY', false),
    ],

    'rehash_on_login' => true,

];
