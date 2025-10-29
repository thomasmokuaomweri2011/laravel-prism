<?php

namespace App\Mcp\Tools;

use Illuminate\JsonSchema\JsonSchema;
use Illuminate\Support\Facades\Http;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Tool;

class WebSearchTool extends Tool
{
    /**
     * The tool's description.
     */
    protected string $description = <<<'MARKDOWN'
        Search the web eg on news,current affairs etc.
    MARKDOWN;

    /**
     * Handle the tool request.
     */
    public function handle(Request $request): Response
    {
        $query = $request->get('query');

        $res = Http::get('https://serpapi.com/search', [
            'q' => $query,
            'api_key' => config('services.serp.api_key'),
        ]);

        if ($res->ok()) {
            $data = $res->json();
            $results = collect($data['organic_results'] ?? [])
                ->take(3)
                ->map(fn ($r) => ($r['title'] ?? 'Untitled').' - '.($r['link'] ?? ''))
                ->implode("\n");

            //logger()->info('Web search results', ['results' => $results]);

            return Response::text($results);
        }

        return Response::text("Search failed for query: {$query}");
    }


    /**
     * Get the tool's input schema.
     *
     * @return array<string, \Illuminate\JsonSchema\JsonSchema>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'query' => $schema->string()
                ->description('Search query string, e.g. "latest AI news"')
                ->required(),
        ];
    }
}
