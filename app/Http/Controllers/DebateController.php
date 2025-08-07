<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class DebateController extends Controller
{
    public function index()
    {
        return view('debate');
    }

    public function stream(Request $request)
    {
        $systemPrompts = [
            'gpt-4o' => 'You are argumentative and direct. Respond with clarity and assertiveness.',
            'llama3.2' => 'You are peaceful and calm. Respond thoughtfully and politely.',
        ];

        $rounds = 6;
        $debate = [];
        $lastResponse = '';

        for ($i = 1; $i <= $rounds; $i++) {
            $isGpt = $i % 2 === 1;
            $model = $isGpt ? 'gpt-4o' : 'llama3.2';
            $provider = $isGpt ? Provider::OpenAI : Provider::Ollama;
            $speaker = $isGpt ? '🗣️ GPT-4o (Argumentative)' : '🌿 Llama 3.2 (Peaceful)';

            $response = Prism::text()
                ->using($provider, $model)
                ->withSystemPrompt($systemPrompts[$model])
                ->withPrompt($lastResponse)
                ->asText()
                ->text;

            $lastResponse = $response;

            $debate[] = [
                'speaker' => $speaker,
                'round' => $i,
                'response' => $response,
            ];
        }

        return view('debate', compact('debate'));
    }
}
