<?php

namespace App\Services\MCP\Tools;

use Illuminate\Support\Facades\Http;
use Prism\Prism\Facades\Tool;

class WebTools
{
    public static function register(): void
    {
        self::getTools();
    }
    
    public static function getTools(): array
    {
        return [
            Tool::as('web_search')
                ->for('Search the web for up-to-date information')
                ->withStringParameter('query', 'Search query string')
                ->using(function (string $query): string {
                    $res = Http::get('https://serpapi.com/search', [
                        'q' => $query,
                        'api_key' => config('services.serp.api_key'),
                    ]);

                    if ($res->ok()) {
                        $data = $res->json();
                        return collect($data['organic_results'] ?? [])
                            ->take(3)
                            ->map(fn ($r) => ($r['title'] ?? 'Untitled') . ' - ' . ($r['link'] ?? ''))
                            ->implode("\n");
                    }

                    return "Search failed for query: {$query}";
                }),

            Tool::as('get_weather')
                ->for('Get the current weather for a given city')
                ->withStringParameter('city', 'City name, e.g. Nairobi')
                ->using(function (string $city): string {
                    $res = Http::get('https://api.openweathermap.org/data/2.5/weather', [
                        'q' => $city,
                        'appid' => config('services.weather.api_key'),
                        'units' => 'metric',
                    ]);

                    if ($res->ok()) {
                        $data = $res->json();
                        return "Weather in {$city}: {$data['main']['temp']}°C, {$data['weather'][0]['description']}";
                    }

                    return "Could not fetch weather for {$city}.";
                }),
        ];
    }
}
