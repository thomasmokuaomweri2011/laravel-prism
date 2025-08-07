<?php $__env->startSection('content'); ?>
    <div
            x-data="chatComponent()"
            class="w-full max-w-4xl h-[85vh] flex flex-col bg-gradient-to-b from-[#f7fbff] via-[#edf4fa] to-[#e8eef6]
           border border-slate-200 rounded-2xl shadow-2xl overflow-hidden"
    >
        <!-- Top bar -->
        <div class="flex items-center justify-between gap-2 p-4 border-b bg-gradient-to-r from-[#e4edfb] to-[#d8e4f8]">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-full bg-indigo-500 text-white grid place-items-center text-lg shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m2 4v-4h1v4m-2-4V8m4 12H6a2 2 0 01-2-2V6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v8a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">Chat with Tools</h2>
                    <p class="text-xs text-slate-500">Ask your creative assistant for ideas or code.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button
                        @click="clearChat"
                        class="px-3 py-2 text-sm rounded-lg border text-slate-700 bg-white/60 hover:bg-white/80 transition"
                >
                    Clear
                </button>
                <button
                        @click="stop"
                        :disabled="!isStreaming"
                        class="px-3 py-2 text-sm rounded-lg border border-indigo-300 text-indigo-700 bg-indigo-50 hover:bg-indigo-100
                       transition disabled:opacity-40 disabled:cursor-not-allowed"
                >
                    Stop
                </button>
            </div>
        </div>

        <!-- Chat window -->
        <div id="chat-window" class="flex-1 overflow-y-auto p-5 space-y-4 bg-gradient-to-b from-[#f7fbff]/70 to-[#edf2f9]/80 scrollbar-thin">
            <!-- Suggestions -->
            <div x-show="messages.length === 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <template x-for="s in suggestions" :key="s">
                    <button
                            @click="useSuggestion(s)"
                            class="text-left p-4 rounded-xl border border-slate-200 bg-white/80 hover:bg-slate-50
                           transition shadow-sm hover:shadow-md"
                    >
                        <div class="font-medium text-slate-800" x-text="s"></div>
                        <div class="text-xs text-indigo-500 mt-1">Tap to insert</div>
                    </button>
                </template>
            </div>

            <!-- Messages -->
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex items-start gap-3" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <template x-if="msg.role !== 'user'">
                        <div class="size-8 rounded-full bg-indigo-100 grid place-items-center text-indigo-600 shadow-inner">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6l4 2m-4-8a9 9 0 110 18 9 9 0 010-18z" />
                            </svg>
                        </div>
                    </template>

                    <div
                            class="max-w-[75%] px-4 py-3 rounded-2xl shadow-sm"
                            :class="msg.role === 'user'
                        ? 'bg-gradient-to-r from-indigo-500 to-blue-500 text-white self-end'
                        : 'bg-white/80 border border-slate-200 text-slate-800'"
                    >
                        <span x-text="msg.content"></span>
                    </div>

                    <template x-if="msg.role === 'user'">
                        <div class="size-8 rounded-full bg-blue-500 text-white grid place-items-center shadow-md">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M5.121 17.804A13.937 13.937 0 0112 15c2.49 0 4.814.642 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="isStreaming" class="flex items-center gap-2 text-slate-600">
                <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                <span class="w-2 h-2 bg-blue-400 rounded-full animate-bounce"></span>
                <span class="text-sm">Thinking…</span>
            </div>

            <!-- Error -->
            <div x-show="error" class="p-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm" x-text="error"></div>
        </div>

        <!-- Input -->
        <div class="p-4 border-t bg-white/70 backdrop-blur flex items-end gap-3">
        <textarea
                x-ref="ta"
                x-model="input"
                @keydown.enter.prevent="!$event.shiftKey && sendMessage()"
                @input="autoResize()"
                rows="1"
                placeholder="Type your message (Shift+Enter for newline)"
                class="flex-1 resize-none max-h-40 px-4 py-3 rounded-xl border border-slate-200 bg-white
                   placeholder-slate-400 focus:ring-2 focus:ring-blue-400 focus:outline-none"
        ></textarea>

            <button
                    @click="sendMessage"
                    :disabled="!input.trim() || isStreaming"
                    class="px-5 py-3 rounded-xl font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-500
                   hover:from-blue-600 hover:to-indigo-600 transition-all disabled:opacity-40 disabled:cursor-not-allowed
                   flex items-center gap-2 shadow-md"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M5 12h14M12 5l7 7-7 7" />
                </svg>
                Send
            </button>
        </div>
    </div>

    <!-- Streaming Logic -->
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
                suggestions: [
                    'Give me creative brochure ideas',
                    'Summarize this Laravel page: https://laravel.com/docs',
                    'Explain Vite config for Laravel',
                    'Generate a Blade layout with Tailwind styles'
                ],
                newId() { return crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`; },
                autoResize() {
                    const ta = this.$refs.ta; ta.style.height = 'auto'; ta.style.height = Math.min(ta.scrollHeight, 160) + 'px';
                },
                useSuggestion(s) { this.input = s; this.$nextTick(() => this.$refs.ta.focus()); },
                clearChat() { this.messages = []; this.error='';this.input=''; },
                stop() { if (this.controller) { this.controller.abort(); this.isStreaming = false; } },
                async sendMessage() {
                    if (!this.input.trim() || this.isStreaming) return;
                    this.error='';
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
                        onmessage: (event) => {
                            if (event.data === '[DONE]') { this.isStreaming = false; return; }
                            try {
                                const data = JSON.parse(event.data);
                                if (data.role && data.role !== 'assistant') {
                                    this.messages.push({ id: this.newId(), role: data.role, content: data.token || data.content || '' });
                                } else if (data.token) {
                                    const i = idxOf();
                                    if (i !== -1) this.messages[i].content += data.token;
                                }
                                this.$nextTick(() => {
                                    const el = document.getElementById('chat-window');
                                    el.scrollTop = el.scrollHeight;
                                });
                            } catch (e) { console.error('Chunk parse error', e, event.data); }
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/thomasomweri/Sites/laravel-prism/resources/views/chat/chat-tools.blade.php ENDPATH**/ ?>