<?php
//le fichier permet d'autoriser les navigateur entre les 2 ports
return [
  'paths' => ['api/*', 'sanctum/csrf-cookie'],

  'allowed_methods' => ['*'],

  'allowed_origins' => ['http://localhost:4200'],

  // En production tu mettras : ['https://ton-domaine.com']

  'allowed_origins_patterns' => [],

  'allowed_headers' => ['*'],

  'exposed_headers' => [],

  'max_age' => 86400, // Le navigateur met en cache le preflight 24h

  'supports_credentials' => true,
];
