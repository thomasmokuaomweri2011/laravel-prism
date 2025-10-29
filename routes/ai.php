<?php

use App\Mcp\Servers\WebSearchServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/web-search', WebSearchServer::class);
