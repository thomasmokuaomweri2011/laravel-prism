<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel Prism</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen flex flex-col text-gray-900 relative overflow-hidden">

<!-- 🌄 Global Background -->
<div class="fixed inset-0 -z-10 bg-green">
    <img src="{{ Vite::asset('resources/images/table.jpg') }}"
         alt="background"
         class="w-full h-full object-cover object-center brightness-[0.65] saturate-125">
    <div class="absolute inset-0"></div>
</div>

<!-- Navigation -->
<header class="bg-white/20 backdrop-blur-md border-b border-white/20 sticky top-0 z-10">
    <nav class="container mx-auto flex justify-center gap-6 py-4 text-sm font-semibold text-white drop-shadow-md">
        @php
            $navItems = [
                ['label' => 'Home', 'url' => '/', 'match' => '/'],
                ['label' => 'Brochure', 'url' => '/brochure', 'match' => 'brochure*'],
                ['label' => 'Chat', 'url' => '/chat', 'match' => 'chat'],
                ['label' => 'Stream', 'url' => '/chat-stream', 'match' => 'chat-stream*'],
                ['label' => 'Tools', 'url' => '/chat-tools', 'match' => 'chat-tools*'],
            ];
        @endphp

        @foreach ($navItems as $item)
            @php $active = request()->is($item['match']); @endphp
            <a href="{{ $item['url'] }}"
               class="relative px-2 pb-1 transition
                      {{ $active
                        ? 'text-blue-300 after:absolute after:bottom-0 after:left-0 after:w-full after:h-[2px] after:bg-blue-300 after:rounded-full drop-shadow-sm'
                        : 'hover:text-blue-200 hover:after:absolute hover:after:bottom-0 hover:after:left-0 hover:after:w-full hover:after:h-[2px] hover:after:bg-blue-200/50 hover:after:rounded-full'
                      }}">
                {{ $item['label'] }}
            </a>
        @endforeach
    </nav>
</header>

<!-- Centered Content -->
<main class="flex-1 flex justify-center items-center p-6">
    @yield('content')
</main>

<footer class="text-center py-4 text-gray-300 text-sm bg-black/40 backdrop-blur-md border-t border-white/10">
    © {{ date('Y') }} Powered by Thomas Omweri
</footer>

<!-- Syntax highlighter -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/styles/github-dark.min.css">
<script src="https://cdn.jsdelivr.net/npm/highlight.js@11.9.0/lib/common.min.js"></script>

</body>
</html>
