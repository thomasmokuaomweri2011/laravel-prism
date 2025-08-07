<?php $__env->startSection('content'); ?>
    <div x-data="chatComponent()" class="w-full max-w-4xl h-[85vh] flex flex-col bg-white/80 backdrop-blur-lg border border-white/30 rounded-2xl shadow-2xl overflow-hidden">
        <!-- Top bar -->
        <div class="flex items-center justify-between gap-2 p-4 border-b bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center gap-3">
                <div class="size-9 rounded-full bg-blue-600 text-white grid place-items-center text-lg">🤖</div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Simple Chat</h2>
                    <p class="text-xs text-gray-500">Ask anything — e.g. “Generate a Laravel controller with routes.”</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button @click="clearChat" class="px-3 py-2 text-sm rounded-lg border text-gray-700 hover:bg-gray-50">Clear</button>
            </div>
        </div>

        <!-- Chat window -->
        <div id="chat-window" class="flex-1 overflow-y-auto p-5 space-y-4 bg-white/70 scrollbar-thin">
            <!-- Suggestions -->
            <div x-show="messages.length === 0" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <template x-for="s in suggestions" :key="s">
                    <button @click="useSuggestion(s)" class="text-left p-4 rounded-xl border bg-white hover:bg-gray-50 transition">
                        <div class="font-medium text-gray-800" x-text="s"></div>
                        <div class="text-xs text-gray-500 mt-1">Tap to insert</div>
                    </button>
                </template>
            </div>

            <!-- Messages -->
            <template x-for="msg in messages" :key="msg.id">
                <div class="flex items-start gap-3" :class="msg.role === 'user' ? 'justify-end' : 'justify-start'">
                    <template x-if="msg.role !== 'user'">
                        <div class="size-8 rounded-full bg-gray-200 grid place-items-center">🤖</div>
                    </template>
                    <div class="chat-bubble" :class="msg.role === 'user' ? 'chat-user' : 'chat-assistant'">
                        <span x-text="msg.content"></span>
                    </div>
                    <template x-if="msg.role === 'user'">
                        <div class="size-8 rounded-full bg-blue-600 text-white grid place-items-center">👤</div>
                    </template>
                </div>
            </template>

            <!-- Typing indicator -->
            <div x-show="isLoading" class="flex items-center gap-2 text-gray-600">
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.3s]"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce [animation-delay:-0.15s]"></span>
                <span class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></span>
                <span class="text-sm">Thinking…</span>
            </div>

            <!-- Error -->
            <div x-show="error" class="p-3 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm" x-text="error"></div>
        </div>

        <!-- Input -->
        <div class="p-4 border-t bg-white/80 backdrop-blur flex items-end gap-3">
        <textarea x-ref="ta" x-model="input" @keydown.enter.prevent="!$event.shiftKey && sendMessage()" @input="autoResize()" rows="1"
                  placeholder="Type a message (Shift+Enter for newline)"
                  class="flex-1 resize-none max-h-40 px-4 py-3 rounded-xl border bg-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 focus:outline-none"></textarea>
            <button @click="sendMessage" :disabled="!input.trim() || isLoading"
                    class="px-5 py-3 rounded-xl font-medium text-white bg-blue-600 enabled:hover:bg-blue-700 disabled:opacity-40 disabled:cursor-not-allowed">
                Send
            </button>
        </div>
    </div>

    <script>
        function chatComponent() {
            return {
                messages: [],
                input: '',
                isLoading: false,
                error: '',
                suggestions: [
                    'Summarize this URL: https://laravel.com/docs',
                    'Generate a Tailwind-styled login form in Blade',
                    'Explain how Vite works in Laravel',
                    'Create a RESTful controller with routes'
                ],
                newId() { return crypto.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`; },
                autoResize() {
                    const ta = this.$refs.ta;
                    ta.style.height = 'auto';
                    ta.style.height = Math.min(ta.scrollHeight, 160) + 'px';
                },
                useSuggestion(s) {
                    this.input = s;
                    this.$nextTick(() => this.$refs.ta.focus());
                },
                clearChat() {
                    this.messages = [];
                    this.error = '';
                    this.input = '';
                },
                async sendMessage() {
                    if (!this.input.trim() || this.isLoading) return;
                    this.error = '';
                    const userInput = this.input;

                    // Add user message
                    this.messages.push({ id: this.newId(), role: 'user', content: userInput });
                    this.input = '';
                    this.autoResize();

                    this.isLoading = true;

                    try {
                        const res = await fetch('/chat', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            },
                            body: JSON.stringify({ message: userInput })
                        });

                        if (!res.ok) throw new Error('Network response was not ok');

                        const data = await res.json();
                        this.messages.push({
                            id: this.newId(),
                            role: 'assistant',
                            content: data.reply || 'No response.'
                        });
                    } catch (e) {
                        console.error(e);
                        this.error = 'Something went wrong. Please try again.';
                    } finally {
                        this.isLoading = false;
                        this.$nextTick(() => {
                            const el = document.getElementById('chat-window');
                            el.scrollTop = el.scrollHeight;
                        });
                    }
                }
            }
        }
    </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/thomasomweri/Sites/laravel-prism/resources/views/chat/chat.blade.php ENDPATH**/ ?>