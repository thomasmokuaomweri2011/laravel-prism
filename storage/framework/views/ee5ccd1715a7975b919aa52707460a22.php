<?php $__env->startSection('content'); ?>
    <div
        x-data="chatComponent()"
        class="max-w-4xl mx-auto mt-10 p-6 bg-white border rounded-2xl shadow-lg flex flex-col h-[85vh]"
    >
        <h2 class="text-2xl font-bold mb-4 text-gray-800 flex items-center gap-2">
            🔧 Chat with MCP Tools
        </h2>

        <!-- Messages -->
        <div class="flex-1 overflow-y-auto space-y-3 mb-4 border rounded-lg p-4 bg-gray-50" id="chat-window">
            <template x-for="msg in messages" :key="msg.id">
                <div :class="msg.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                    <div
                        class="px-4 py-2 rounded-2xl max-w-[75%] shadow-sm"
                        :class="msg.role === 'user'
                        ? 'bg-blue-600 text-white rounded-br-none'
                        : 'bg-gray-200 text-gray-800 rounded-bl-none'"
                    >
                        <span x-text="msg.content"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- Input -->
        <div class="flex items-center space-x-3">
            <input type="text"
                   x-model="input"
                   @keydown.enter="sendMessage"
                   class="flex-1 border rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-blue-400"
                   placeholder="Type your message...">
            <button @click="sendMessage"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-xl font-medium shadow-md transition">
                Send
            </button>
        </div>
    </div>

<!-- Import fetch-event-source and expose globally -->
<script type="module">
    import { fetchEventSource as fes } from "https://esm.sh/@microsoft/fetch-event-source";
    window.fetchEventSource = fes;
</script>

<!-- Define Alpine component -->
<script>
    function chatComponent() {
        return {
            messages: [],
            input: '',

            newId() {
                return (crypto?.randomUUID?.() ?? `${Date.now()}-${Math.random().toString(36).slice(2)}`);
            },

            sendMessage() {
                if (!this.input.trim()) return;

                this.messages.push({ id: this.newId(), role: 'user', content: this.input });

                const assistantId = this.newId();
                this.messages.push({ id: assistantId, role: 'assistant', content: '' });

                const getAssistantIndex = () => this.messages.findIndex(m => m.id === assistantId);

                window.fetchEventSource('/chat-mcp-run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ messages: this.messages }),
                    onmessage: (event) => {
                        if (event.data === "[DONE]") return;
                        try {
                            const data = JSON.parse(event.data);
                            if (data.token) {
                                const idx = getAssistantIndex();
                                if (idx !== -1) {
                                    this.messages[idx].content += data.token;
                                }
                                this.$nextTick(() => {
                                    const chatWindow = document.getElementById('chat-window');
                                    const isAtBottom = Math.abs(chatWindow.scrollHeight - chatWindow.scrollTop - chatWindow.clientHeight) < 10;
                                    if (isAtBottom) chatWindow.scrollTop = chatWindow.scrollHeight;
                                });
                            }
                        } catch (err) {
                            console.error("Bad chunk:", event.data, err);
                        }
                    },
                    onclose: () => console.log("Stream closed"),
                    onerror: (err) => console.error("Stream error", err),
                });

                this.input = '';
            }
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /Users/thomasomweri/Sites/laravel-prism/resources/views/chat/chat-mcp.blade.php ENDPATH**/ ?>