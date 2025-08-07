<?php

return [
    'providers' => [
        'openai' => [
            'provider' => Prism\Prism\Enums\Provider::OpenAI,
            'model' => 'gpt-4o-mini',
            'endpoint' => 'https://api.openai.com/v1/chat/completions',
            'key' => env('OPENAI_API_KEY'),
        ],
        'anthropic' => [
            'provider' => Prism\Prism\Enums\Provider::Anthropic,
            'model' => 'claude-3-5-sonnet-20240620',
            'endpoint' => 'https://api.anthropic.com/v1/messages',
            'key' => env('ANTHROPIC_API_KEY'),
        ],
        'mistral' => [
            'provider' => Prism\Prism\Enums\Provider::Mistral,
            'model' => 'mistral-large-latest',
            'endpoint' => 'https://api.mistral.ai/v1/chat/completions',
            'key' => env('MISTRAL_API_KEY'),
        ],
        'gemini' => [
            'provider' => Prism\Prism\Enums\Provider::Gemini,
            'model' => 'gemini-1.5-flash',
            'endpoint' => 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent',
            'key' => env('GOOGLE_API_KEY'),
        ],
        'perplexity' => [
            'provider' => Prism\Prism\Enums\Provider::DeepSeek,
            'model' => '',
            'endpoint' => 'https://api.deepseek.com/v1',
            'key' => env('DEEPSEEK_API_KEY'),
        ],
        'voyageai' => [
            'provider' => Prism\Prism\Enums\Provider::VoyageAI,
            'model' => 'voyage-1.5-turbo',
            'endpoint' => 'https://api.voyageai.com/v1/chat/completions',
            'key' => env('VOYAGEAI_API_KEY'),
        ],
        'xai' => [
            'provider' => Prism\Prism\Enums\Provider::XAI,
            'model' => 'gemini-1.5-flash',
            'endpoint' => 'https://api.x.ai/v1/chat/completions',
            'key' => env('XAI_API_KEY'),
        ],
    ],
];
