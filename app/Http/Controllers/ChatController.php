<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Prism\Prism\Facades\Tool;
use Prism\Prism\ValueObjects\Messages\AssistantMessage;
use Prism\Prism\ValueObjects\Messages\UserMessage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $response = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4o')
            ->withSystemPrompt('You are a helpful assistant.')
            ->withPrompt($request->message)
            ->asText();

        return response()->json([
            'reply' => $response->text,
        ]);
    }

    public function chatStream(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        $messages = collect($request->input('messages'))->map(function ($msg) {
            return match ($msg['role']) {
                'user' => new UserMessage($msg['content'] ?? ''),
                'assistant' => !empty($msg['content'])
                    ? new AssistantMessage($msg['content'])
                    : null,
                default => null,
            };
        })->filter()->values()->toArray();

        return response()->stream(function () use ($messages) {
            $stream = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o')
                ->withSystemPrompt('You are a helpful assistant.')
                ->withMessages($messages)
                ->asStream();

            foreach ($stream as $chunk) {
                echo "data: " . json_encode(['token' => $chunk->text]) . "\n\n";
                ob_flush();
                flush();
            }

            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }

    public function chatWithTools(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        $messages = collect($request->input('messages'))->map(function ($msg) {
            return match ($msg['role']) {
                'user' => new UserMessage($msg['content'] ?? ''),
                'assistant' => !empty($msg['content'])
                    ? new AssistantMessage($msg['content'])
                    : null,
                default => null,
            };
        })->filter()->values()->toArray();

        return response()->stream(function () use ($messages) {
            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o')
                ->withSystemPrompt('You are a helpful assistant with access to weather and web search tools.')
                ->withMessages($messages)
                ->withMaxSteps(3)
                ->withTools($this->tools())
                ->asStream();
            foreach ($response as $chunk) {
                echo "data: " . json_encode(['token' => $chunk->text, 'toolResults' => $chunk->toolResults]) . "\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',

        ]);
    }

    protected function tools(): array
    {
        return [
            Tool::as('web_search')
                ->for('Search the web for up-to-date information')
                ->withStringParameter('query', 'Search query string')
                ->using(function (string $query): string {
                    $res = Http::get("https://serpapi.com/search", [
                        'q' => $query,
                        'api_key' => config('services.serp.api_key'),
                    ]);

                    if ($res->ok()) {
                        $data = $res->json();
                        $results = collect($data['organic_results'] ?? [])->take(3);
                        return $results->map(fn($r) => ($r['title'] ?? '') . ' - ' . ($r['link'] ?? ''))
                            ->implode("\n");
                    }

                    return "Search failed for query: {$query}";
                }),
        ];
    }

    public function chatAudio(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
        ]);

        $response = Prism::audio()
            ->using(Provider::OpenAI, 'gpt-4o-audio-preview')
            ->withInputText($request->message)
            ->asStream();

        return new StreamedResponse(function () use ($response) {
            foreach ($response as $chunk) {
                echo "data: " . base64_encode($chunk->audio) . "\n\n";
                ob_flush();
                flush();
            }
            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
        ]);
    }

    public function chatImage(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string',
        ]);

        $response = Prism::image()
            ->using(Provider::OpenAI, 'dall-e-3')
            ->withPrompt($request->input('prompt'))
            ->generate();

        return response()->json([
            'image_url' => $response->firstImage()->url,
        ]);
    }

    public function chatWithMCP(Request $request)
    {
        $request->validate([
            'messages' => 'required|array',
        ]);

        $messages = collect($request->input('messages'))->map(function ($msg) {
            return match ($msg['role']) {
                'user' => new UserMessage($msg['content'] ?? ''),
                'assistant' => !empty($msg['content'])
                    ? new AssistantMessage($msg['content'])
                    : null,
                default => null,
            };
        })->filter()->values()->toArray();

        return response()->stream(function () use ($messages) {
            $mcpTools = \App\Services\MCP\Tools\WebTools::getTools();

            $response = Prism::text()
                ->using(Provider::OpenAI, 'gpt-4o')
                ->withSystemPrompt('You are a helpful assistant with access to MCP tools including weather and web search.')
                ->withMessages($messages)
                ->withMaxSteps(3)
                ->withTools($mcpTools)
                ->asStream();

            foreach ($response as $chunk) {
                echo "data: " . json_encode(['token' => $chunk->text, 'toolResults' => $chunk->toolResults]) . "\n\n";
                ob_flush();
                flush();
            }
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Connection' => 'keep-alive',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
