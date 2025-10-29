// Minimal chat component core logic extracted for unit testing
// This mirrors the onmessage/onerror and sendMessage flow used in Blade components.
export function createChatComponent({ endpoint, fetchEventSource }) {
  const state = {
    messages: [],
    input: '',
    isStreaming: false,
    controller: null,
    error: '',
    newId() {
      return `${Date.now()}-${Math.random().toString(36).slice(2)}`;
    },
    async sendMessage() {
      if (!state.input.trim() || state.isStreaming) return;
      state.error = '';
      state.messages.push({ id: state.newId(), role: 'user', content: state.input });
      const assistantId = state.newId();
      state.messages.push({ id: assistantId, role: 'assistant', content: '' });
      const idxOf = () => state.messages.findIndex((m) => m.id === assistantId);
      state.controller = { abort: () => {} };
      state.isStreaming = true;
      const body = JSON.stringify({ messages: state.messages });

      fetchEventSource(endpoint, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body,
        signal: state.controller,
        onmessage: (event) => {
          try {
            const data = JSON.parse(event.data);
            if (event.event === 'tool_call') {
              const toolName = data.name || data.tool || data.tool_name || 'tool';
              state.messages.push({ id: state.newId(), role: 'assistant', content: `🔧 Using ${toolName}…` });
            } else if (event.event === 'tool_result') {
              const toolName = data.name || data.tool || data.tool_name || 'tool';
              const idx = state.messages.findIndex((m) => (m.content || '').includes('Using'));
              if (idx !== -1) state.messages[idx].content = `✅ ${toolName} completed.`;
            } else if (!event.event || event.event === 'text_delta') {
              const delta = data.delta || '';
              const i = idxOf();
              if (i !== -1) {
                state.messages[i] = { ...state.messages[i], content: (state.messages[i].content || '') + delta };
              } else {
                state.messages.push({ id: state.newId(), role: 'assistant', streaming: true, content: delta });
              }
            } else if (event.event === 'stream_end') {
              state.isStreaming = false;
            }
          } catch (e) {
            // swallow in tests
          }
        },
        onerror: (err) => {
          state.error = 'Connection lost. Please try again.';
          state.isStreaming = false;
        },
        onclose: () => {
          state.isStreaming = false;
        },
      });

      state.input = '';
    },
  };

  return state;
}