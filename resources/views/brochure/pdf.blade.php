<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $brochure['english']['title'] ?? 'Brochure' }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #222;
            margin: 30px;
            line-height: 1.5;
            font-size: 14px;
        }
        h1, h2 {
            color: #1a1a1a;
        }
        h1 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
        }
        h2 {
            font-size: 18px;
            margin-top: 25px;
            margin-bottom: 10px;
        }
        p {
            margin: 5px 0;
        }
        ul {
            margin: 5px 0 10px 20px;
        }
        .logo {
            display: block;
            margin: 0 auto 20px auto;
            max-height: 80px;
        }
        hr {
            border: none;
            border-top: 1px solid #ccc;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            margin-top: 30px;
            color: #555;
        }
    </style>
</head>
<body>

<!-- Logo -->
@if(!empty($brochure['logo']))
    <img src="{{ $brochure['logo'] }}" class="logo" alt="Logo" style="max-height:80px; display:block; margin:0 auto 20px;">
@endif

<h1>{{ $brochure['english']['title'] ?? 'Brochure' }}</h1>

<!-- Dynamic Language Sections -->
@foreach($brochure as $lang => $data)
    @continue(in_array($lang, ['url', 'logo'])) {{-- skip non-language keys --}}

    @php
        $languageName = ucfirst($lang);
        $flag = match($lang) {
            'english' => '🇬🇧',
            'swahili' => '🇰🇪',
            'french' => '🇫🇷',
            default => '🌐'
        };
    @endphp

    <h2>{{ $flag }} {{ $languageName }} Version</h2>

    @if(!empty($data['title']))
        <p><strong>Title:</strong> {{ $data['title'] }}</p>
    @endif

    @if(!empty($data['tagline']))
        <p><em>"{{ $data['tagline'] }}"</em></p>
    @endif

    @if(!empty($data['mission']))
        <p><strong>Mission:</strong> {{ $data['mission'] }}</p>
    @endif

    @if(!empty($data['services']))
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
