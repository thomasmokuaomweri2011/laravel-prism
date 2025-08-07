@extends('layouts.app')

@section('content')
    <div class="w-full max-w-4xl h-[85vh] mx-auto flex flex-col bg-white/80 backdrop-blur-lg border border-white/30 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="p-6 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <h2 class="text-2xl font-semibold text-gray-900 text-center">📄 Generate Brochures</h2>
        </div>

        <!-- Scrollable Content -->
        <div class="flex-1 overflow-y-auto p-8 space-y-8 bg-white/70">
            <form method="POST" action="/generate" class="space-y-8">
                @csrf

                <div>
                    <label for="urls" class="block text-base font-medium text-gray-700 mb-3">
                        Website URLs
                    </label>
                    <textarea name="urls[]" id="urls" rows="10" placeholder="https://example.com"
                              class="w-full rounded-2xl border border-gray-300 bg-white/80 px-5 py-4 text-gray-800 placeholder-gray-400
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"></textarea>
                    <p class="text-sm text-gray-500 mt-2">Enter one or more URLs (one per line).</p>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                            class="px-8 py-3 rounded-xl bg-blue-600 text-white font-medium text-lg
                           hover:bg-blue-700 transition-all shadow-md hover:shadow-lg disabled:opacity-50">
                        Generate
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
