<?php

namespace App\Services\MCP;

use Prism\Relay\Relay;

class McpClientService
{
    private ?Relay $relay = null;

    public function connect(string $serverName = 'local_web_tools'): self
    {
        $this->relay = new Relay($serverName);
        return $this;
    }

    public function getTools(): array
    {
        if (!$this->relay) {
            throw new \RuntimeException('MCP client not connected. Call connect() first.');
        }

        try {
            return $this->relay->tools();
        } catch (\Exception $e) {
            throw new \RuntimeException('Failed to get tools from MCP server: ' . $e->getMessage());
        }
    }

    public function isConnected(): bool
    {
        return $this->relay !== null;
    }

    public function disconnect(): void
    {
        $this->relay = null;
    }
}
