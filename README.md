# Laravel Prism

A Laravel 12 demo showcasing multi-AI provider orchestration with Prism PHP, real-time streaming (SSE), tool-calling (web search, weather), brochure generation to PDF, image/audio AI, and Model Context Protocol (MCP) integration via Prism Relay.

## Features
- Unified AI across providers (OpenAI, Anthropic, Mistral, Gemini, VoyageAI, X.AI, DeepSeek) via Prism
- Raw provider calls example (no SDK) for comparison
- Server-Sent Events streaming for chat and demos
- Tool-calling with SerpAPI and OpenWeather
- Brochure generator: scrape site → generate bilingual content (EN/Swahili) → export PDF
- Image generation (DALL·E) and audio streaming (TTS/STT preview)
- MCP-ready: local Relay server and tools

## Requirements
- PHP 8.2+
- Composer
- Node.js 18+ and npm
- SQLite (default) or another database
- Optional API keys depending on features you want to try:
  - OPENAI_API_KEY, ANTHROPIC_API_KEY, MISTRAL_API_KEY, GOOGLE_API_KEY or GEMINI_API_KEY, DEEPSEEK_API_KEY, VOYAGEAI_API_KEY, XAI_API_KEY
  - SERPAPI_KEY (web search tool), OPENWEATHER_API_KEY (weather tool)

## Quick start
1) Install deps
- composer install
- npm install

2) Environment
- cp .env.example .env
- php artisan key:generate
- touch database/database.sqlite
- php artisan migrate

3) Run dev (concurrent processes)
- composer run dev
  - Starts: PHP server, queue listener, log viewer (pail), Vite

Alternatively, run separately:
- php artisan serve
- php artisan queue:listen --tries=1
- php artisan pail --timeout=0
- npm run dev

## Configure providers
Edit config/ai-providers.php and config/prism.php, then set env vars in .env as needed, e.g.:
- OPENAI_API_KEY=...
- ANTHROPIC_API_KEY=...
- MISTRAL_API_KEY=...
- GOOGLE_API_KEY=...  (or GEMINI_API_KEY=... depending on provider block)
- DEEPSEEK_API_KEY=...
- VOYAGEAI_API_KEY=...
- XAI_API_KEY=...
- SERPAPI_KEY=...
- OPENWEATHER_API_KEY=...

## Endpoints overview
- GET / → Welcome
- GET /brochure → Input URLs UI
- POST /generate → Build brochures (EN/Swahili) from URLs
- GET /brochure/download/{index} → Download brochure PDF
- GET /stream → SSE demo UI
- GET /stream/run → Streamed property description
- GET /ai/raw → Raw HTTP calls to providers (JSON)
- GET /ai/prism → Same prompt via Prism across providers (JSON)
- GET /ai-debate → Debate UI
- GET /ai-debate/stream → Alternating model debate (OpenAI vs Ollama)
- GET /chat → Simple chat UI
- POST /chat → JSON reply
- GET /chat-stream → Streaming chat UI (SSE)
- POST /chat-stream-run → Stream tokens
- GET /chat-tools → Tool-calling chat UI
- POST /chat-tools-run → Uses SerpAPI + OpenWeather tools
- GET /chat-mcp → MCP-enabled chat UI
- POST /chat-mcp-run → Uses MCP tools
- POST /api/chat/audio → Streams TTS audio (base64 frames)
- POST /api/chat/image → Returns generated image_url

## Brochure generator
- Paste one or more URLs on /brochure.
- The app fetches HTML, extracts title/meta, summarizes content via Prism (gpt-4o-mini), and detects a likely logo.
- View bilingual results and download a PDF brochure per site.

## Tool-calling
- Configure SERPAPI_KEY and OPENWEATHER_API_KEY in .env (see config/services.php).
- Tools are defined in:
  - app/Http/Controllers/ChatController.php (web_search)
  - app/Services/MCP/Tools/WebTools.php (web_search, get_weather)

## Streaming (SSE)
- Uses response()->stream and Prism::text()->asStream().
- See /stream, /chat-stream, and /ai-debate/stream for examples.

## MCP (Model Context Protocol)
- Relay config: config/relay.php
- Start local stdio server with Prism Relay tools:
  - php artisan mcp:serve
- Tools registered under app/Services/MCP/Tools/WebTools.php
- You can wire a client via App\Services\MCP\McpClientService if needed.

## Scripts
- Composer
  - composer run dev → dev servers + Vite + logs
  - composer test → clears config and runs tests
- NPM
  - npm run dev → Vite
  - npm run build → Production build

## Testing
- composer test

## Formatting
- Install Pint (already in require-dev) and run:
  - vendor/bin/pint

## Notes
- Queue: database driver; run php artisan queue:listen in dev (composer run dev handles this).
- Session/cache use database; migrations provided.

## License
MIT
