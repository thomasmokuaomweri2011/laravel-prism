<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class RawMultipleAiController extends Controller
{
    public function __invoke(Request $request)
    {
        $providers = config('ai-providers.providers');
        $prompt = "Summarize Laravel's advantages for SaaS platforms.";
        $results = [];

        foreach ($providers as $name => $config) {
            try {
                $response = Http::withToken($config['key'])->post($config['endpoint'], [
                    'model' => $config['model'],
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                ]);

                $results[$name] = $response->json();
            } catch (\Throwable $e) {
                $results[$name] = ['error' => $e->getMessage()];
            }
        }

        return response()->json($results);
    }
}
