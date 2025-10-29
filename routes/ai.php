<?php

use App\Mcp\Servers\CalculatorServer;
use App\Mcp\Servers\WebSearchServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/web-search', WebSearchServer::class);
Mcp::web('/mcp/calculator', CalculatorServer::class);
