@extends('layouts.app')

@section('content')
    <div x-data="chatComponent()" class="w-full max-w-4xl h-[85vh] flex flex-col bg-white/80 backdrop-blur-lg border border-purple-100 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Header -->
        <div class="flex items-center justify-between gap-2 p-4 border-b bg-gradient-to-r from-indigo-50 to-purple-100">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 text-white grid place-items-center text-lg shadow-md">⚡</div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Streaming Chat</h2>
                    <p class="text-xs text-gray-500">Ask anything — e.g. “Generate a Laravel controller with routes.”</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="clearChat" class="px-3 py-2 text-sm rounded-lg border text-gray-700 hover:bg-purple-50 transition">Clear</button>
                <button @click="stop" :disabled="!isStreaming" class="px-3 py-2 text-sm rounded-lg border disabled:opacity-40 disabled:cursor-not-allowed text-purple-700 border-purple-200 hover:bg-purple-50 transition">Stop</button>
            </div>
        </div>

        <!-- Chat window -->
        <div id="chat-window" class="flex-1 overflow-y-auto p-5 space-y-4 bg-gradient-to-b from-purple-50/60 to-indigo-50/40 scrollbar-thin">
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex items-start gap-3" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <template x-if="msg.role !== 'user'">
                        <div class="size-8 rounded-full bg-gradient-to-br from-purple-500 to-indigo-600 text-white grid place-items-center shadow-sm">🤖</div>
                    </template>
                    <div :class="msg.role === 'user' ? 'bg-gradient-to-br from-indigo-600 to-purple-600 text-white p-3 rounded-xl max-w-[75%]' : 'bg-white text-gray-800 p-3 rounded-xl max-w-[75%] border border-gray-100 shadow-sm'">
                        <span x-text="msg.content"></span>
                    </div>
                    <template x-if="msg.role === 'user'">
                        <div class="size-8 rounded-full bg-gradient-to-br from-purple-400 to-indigo-500 text-white grid place-items-center shadow-sm">👤</div>
                    </template>
                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="isStreaming" class="flex items-center gap-2 text-gray-600">
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                <span class="text-sm">Streaming response...</span>
            </div>

            <div x-show="error" class="p-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm" x-text="error"></div>
        </div>

        <!-- Input -->
        <div class="p-4 border-t bg-gradient-to-r from-indigo-50 to-purple-50 backdrop-blur flex items-end gap-3">
            <textarea x-ref="ta" x-model="input" @keydown.enter.prevent="!$event.shiftKey && sendMessage()" @input="autoResize()" rows="1" placeholder="Type your message (Shift+Enter for newline)" class="flex-1 resize-none max-h-40 px-4 py-3 rounded-xl border bg-white placeholder-gray-400 focus:ring-2 focus:ring-purple-500 focus:outline-none"></textarea>
            <button @click="sendMessage" :disabled="!input.trim() || isStreaming" class="px-5 py-3 rounded-xl font-medium text-white bg-gradient-to-br from-purple-500 to-indigo-600 enabled:hover:opacity-90 disabled:opacity-40 disabled:cursor-not-allowed shadow-md">Send</button>
        </div>
    </div>

    <script type="module">
        import { fetchEventSource as fes } from "https://esm.sh/@microsoft/fetch-event-source";
        window.fetchEventSource = fes;
    </script>

    <script>
        function chatComponent() {
            return {
                messages: [],
                input: '',
                isStreaming: false,
                controller: null,
                error: '',

                newId() { return crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`; },

                autoResize() {
                    const ta = this.$refs.ta;
                    ta.style.height = 'auto';
                    ta.style.height = Math.min(ta.scrollHeight, 160) + 'px';
                },

                clearChat() { this.messages = []; this.error=''; this.input=''; },

                stop() {
                    if (this.controller) this.controller.abort();
                    this.isStreaming = false;
                },

                async sendMessage() {
                    if (!this.input.trim() || this.isStreaming) return;
                    this.error = '';

                    // push user message
                    this.messages.push({ id: this.newId(), role: 'user', content: this.input });
                    const assistantId = this.newId();
                    this.messages.push({ id: assistantId, role: 'assistant', content: '' });

                    const idxOf = () => this.messages.findIndex(m => m.id === assistantId);
                    this.controller = new AbortController();
                    this.isStreaming = true;

                    const body = JSON.stringify({ messages: this.messages });

                    window.fetchEventSource('/chat-stream-run', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        },
                        body,
                        signal: this.controller.signal,

                        onopen: () => { console.log('stream opened'); },
                        onmessage: (event) => {
                            try {
                                if (!event.event || event.event === 'text_delta') {
                                    const data = JSON.parse(event.data);
                                    if (data.delta) {
                                        const i = idxOf();
                                        if (i !== -1) {
                                            // replace object so Alpine detects reactivity change
                                            this.messages[i] = {
                                                ...this.messages[i],
                                                content: this.messages[i].content + data.delta
                                            };
                                        }
                                    }
                                } else if (event.event === 'stream_end') {
                                    this.isStreaming = false;
                                }
                            } catch (e) {
                                console.error('Stream chunk parse error', e, event.data);
                            }

                            // ensure scroll stays pinned to bottom
                            this.$nextTick(() => {
                                const el = document.getElementById('chat-window');
                                el.scrollTop = el.scrollHeight;
                            });
                        },
                        onerror: (err) => {
                            console.error('Stream error', err);
                            this.error = 'Connection lost. Please try again.';
                            this.isStreaming = false;
                        },
                        onclose: () => { this.isStreaming = false; },
                    });

                    this.input = '';
                    this.$nextTick(() => this.autoResize());
                }
            }
        }
    </script>
@endsection
