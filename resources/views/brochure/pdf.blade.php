<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $brochure['english']['title'] ?? 'Brochure' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            margin: 35px;
            line-height: 1.6;
            font-size: 13.5px;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 25px;
            color: #111;
        }
        h2 {
            font-size: 17px;
            margin-top: 30px;
            margin-bottom: 10px;
            color: #222;
        }
        p { margin: 4px 0; }
        ul { margin: 6px 0 10px 25px; }
        li { margin-bottom: 2px; }

        .logo {
            display: block;
            margin: 0 auto 20px auto;
            max-height: 80px;
        }

        .section {
            margin-bottom: 25px;
        }

        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 25px 0;
        }

        .footer {
            text-align: center;
            font-size: 11.5px;
            margin-top: 40px;
            color: #555;
        }

        .tagline {
            font-style: italic;
            color: #555;
        }
    </style>
</head>
<body>

{{-- Logo --}}
@if(!empty($brochure['logo']))
    <img src="{{ $brochure['logo'] }}" alt="Logo" class="logo">
@endif

{{-- Title --}}
<h1>{{ $brochure['english']['title'] ?? 'Brochure' }}</h1>

{{-- Dynamic Languages --}}
@php
    $languages = collect($brochure)->except(['url', 'logo'])->toArray();
    $flags = [
        'english' => '🇬🇧',
        'swahili' => '🇰🇪',
        'french'  => '🇫🇷',
    ];
@endphp

@foreach($languages as $lang => $data)
    <div class="section">
        <h2>{{ $flags[$lang] ?? '🌐' }} {{ ucfirst($lang) }} Version</h2>

        @if(!empty($data['title']))
            <p><strong>Title:</strong> {{ $data['title'] }}</p>
        @endif

        @if(!empty($data['tagline']))
            <p class="tagline">"{{ $data['tagline'] }}"</p>
        @endif

        @if(!empty($data['mission']))
            <p><strong>Mission:</strong> {{ $data['mission'] }}</p>
        @endif

        @if(!empty($data['services']) && is_array($data['services']) && count($data['services']) > 0)
            <p><strong>Services:</strong></p>
            <ul>
                @foreach($data['services'] as $service)
                    <li>{{ $service }}</li>
                @endforeach
            </ul>
        @endif

        @if(!empty($data['contact']))
            <p><strong>Contact:</strong> {{ $data['contact'] }}</p>
        @endif
    </div>

    @if(!$loop->last)
        <hr>
    @endif
@endforeach

@if(!empty($brochure['url']))
    <div class="footer">
        Source: <a href="{{ $brochure['url'] }}">{{ $brochure['url'] }}</a>
    </div>
@endif

</body>
</html>
