@extends('layouts.app')

@section('content')
    <div class="w-full max-w-4xl h-[85vh] mx-auto flex flex-col bg-white/80 backdrop-blur-lg border border-white/30 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-2xl font-semibold text-gray-900 text-center">📑 Generated Brochures</h2>
        </div>

        <!-- Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-6 bg-white/70">
            @forelse(($brochures ?? []) as $i => $b)
                <div class="p-6 rounded-2xl border border-gray-200 bg-white/80 shadow-sm hover:shadow-md transition-all">
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">
                        {{ $b['english']['title'] ?? 'Untitled Brochure' }}
                    </h3>
                    @if(!empty($b['english']['tagline']))
                        <p class="text-gray-600 mb-4">{{ $b['english']['tagline'] }}</p>
                    @endif

                    <a href="{{ route('brochure.download', ['index' => $i]) }}"
                       class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-blue-600 text-white text-sm font-medium
                          hover:bg-blue-700 transition-all shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V4" />
                        </svg>
                        Download PDF
                    </a>
                </div>
            @empty
                <div class="text-center text-gray-600 py-12">
                    <p class="text-lg">No brochures have been generated yet.</p>
                    <p class="text-sm text-gray-500 mt-2">Try creating some from the Generate page.</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
