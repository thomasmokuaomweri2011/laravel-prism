<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Prism;

class PrismAiController extends Controller
{
    public function __invoke(Request $request)
    {
        $providers = config('ai-providers.providers');
        $prompt = "Summarize Laravel's advantages for SaaS platforms.";
        $results = [];

        foreach ($providers as $name => $config) {
            try {
                $results[$name] = Prism::text()
                    ->using($config['provider'], $config['model'])
                    ->withPrompt($prompt)
                    ->asText()
                    ->text;
            } catch (\Throwable $e) {
                $results[$name] = ['error' => $e->getMessage()];
            }
        }

        return response()->json($results);
    }
}
