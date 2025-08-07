<?php

namespace App\Console\Commands;

use App\Services\MCP\Tools\WebTools;
use Illuminate\Console\Command;

class McpServeCommand extends Command
{
    protected $signature = 'mcp:serve {server=local_web_tools}';
    protected $description = 'Start an MCP-compatible Relay server (default: local_web_tools)';

    public function handle(): int
    {
        $serverName = $this->argument('server');

        $this->info("🚀 Starting MCP server [{$serverName}]...");

        WebTools::register();
        
        $this->info("✅ MCP tools registered successfully!");
        $this->info("🔌 Ready to handle MCP requests...");

        while (true) {
            $input = fgets(STDIN);
            if ($input === false) {
                break;
            }
            $this->info("Received: " . trim($input));
        }

        return self::SUCCESS;
    }
}
