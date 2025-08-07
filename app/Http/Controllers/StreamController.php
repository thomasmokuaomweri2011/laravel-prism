<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StreamController extends Controller
{
    public function index()
    {
        return view('stream.index');
    }

    public function stream(Request $request)
    {
        $generator = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4o')
            ->withPrompt('Write a creative property description for a 3-bedroom apartment in Nairobi with a pool.')
            ->asStream();

        return new StreamedResponse(function () use ($generator) {
            foreach ($generator as $chunk) {
                echo "data: " . $chunk->text . "\n\n";
                ob_flush();
                flush();
            }

            echo "data: [DONE]\n\n";
            ob_flush();
            flush();
        }, 200, [
            'Content-Type' => 'text/event-stream',
            'Cache-Control' => 'no-cache',
            'X-Accel-Buffering' => 'no',
        ]);
    }
}
