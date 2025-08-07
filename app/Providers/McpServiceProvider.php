<?php

namespace App\Providers;

use App\Services\MCP\Tools\WebTools;
use Illuminate\Support\ServiceProvider;

class McpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (app()->runningInConsole()) {
            $argv = $_SERVER['argv'] ?? [];
            if (count($argv) > 1 && $argv[1] === 'mcp:serve') {
                WebTools::register();
            }
        }
    }
}
