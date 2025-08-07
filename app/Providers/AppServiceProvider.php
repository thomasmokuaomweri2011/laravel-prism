<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Providers\McpServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        if ($this->app->runningInConsole() && $this->isMcpCommand()) {
            $this->app->register(McpServiceProvider::class);
        }
    }

    protected function isMcpCommand(): bool
    {
        $argv = $_SERVER['argv'] ?? [];
        return in_array('mcp:serve', $argv, true);
    }

    public function boot(): void
    {
        //
    }
}
