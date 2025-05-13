<?php
return [
    'azure' => [
        'appInsights' => [
            'instrumentationKey' => getenv('APPINSIGHTS_INSTRUMENTATIONKEY')
        ],
        'database' => [
            'server' => getenv('AZURE_SQL_SERVER'),
            'database' => getenv('AZURE_SQL_DATABASE'),
            'username' => getenv('AZURE_SQL_USERNAME'),
            'password' => getenv('AZURE_SQL_PASSWORD')
        ]
    ]
];