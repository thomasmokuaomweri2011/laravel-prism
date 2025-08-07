<?php

return [

    /*
    |--------------------------------------------------------------------------
    | MCP Relay Servers
    |--------------------------------------------------------------------------
    | Each server defines how Relay communicates with MCP clients.
    | For stdio-based servers, both "command" and "env" are required.
    */

    'servers' => [

        // 🧠 Local stdio-based MCP server
        'local_web_tools' => [
            'transport' => Prism\Relay\Enums\Transport::Stdio,
            'command' => ['php', 'artisan', 'mcp:serve', 'local_web_tools'],

            'env' => [
                'APP_ENV' => env('APP_ENV', 'local'),
                'APP_DEBUG' => env('APP_DEBUG', true),
                'PATH' => env('PATH'),
                'HOME' => env('HOME'),
                'TMPDIR' => sys_get_temp_dir(),
            ],
        ],
        'puppeteer' => [
            'transport' => Prism\Relay\Enums\Transport::Stdio,
            'command' => ['npx', '-y', '@modelcontextprotocol/server-puppeteer'],
            'timeout' => env('RELAY_PUPPETEER_SERVER_TIMEOUT', 60),
            'env' => [],
        ],

        'http_web_tools' => [
            'transport' => Prism\Relay\Enums\Transport::Http,
            'host' => '127.0.0.1',
            'port' => 8085,
        ],
    ],
];
