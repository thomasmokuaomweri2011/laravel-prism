@extends('layouts.app')

@section('content')
    <div class="max-w-4xl w-full mx-6 p-12 bg-white/70 backdrop-blur-xl border border-white/40 rounded-3xl shadow-2xl relative overflow-hidden">

        <!-- Decorative gradient ring -->
        <div class="absolute inset-0 rounded-3xl bg-gradient-to-r from-indigo-500/10 via-purple-500/10 to-pink-500/10"></div>

        <blockquote class="relative text-center text-3xl font-semibold italic leading-relaxed px-12 text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 z-10">
            <span class="absolute -top-6 left-8 text-7xl text-indigo-200 select-none">“</span>
            {{ $response }}
            <span class="absolute -bottom-10 right-8 text-7xl text-pink-200 select-none">”</span>
        </blockquote>
    </div>
@endsection
