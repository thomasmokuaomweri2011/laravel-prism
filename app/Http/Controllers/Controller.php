<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Prism\Prism\Enums\Provider;
use Prism\Prism\Prism;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $response = Prism::text()
            ->using(Provider::OpenAI, 'gpt-4o')
            ->withSystemPrompt('You are a helpful assistant.')
            ->withPrompt('Give me an inspiring quote about AI.')
            ->asText()
            ->text;

        return view('welcome', compact('response'));
    }
}
