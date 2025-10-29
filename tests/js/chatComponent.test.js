import { describe, it, expect, vi, beforeEach } from 'vitest';
import { createChatComponent } from '../../resources/js/chat/chatCore.js';

function makeFESHarness() {
  const calls = [];
  const trigger = {
    send(event) {
      const last = calls.at(-1);
      if (last && last.opts.onmessage) last.opts.onmessage(event);
    },
    error(err = new Error('boom')) {
      const last = calls.at(-1);
      if (last && last.opts.onerror) last.opts.onerror(err);
    },
  };
  const fes = (url, opts) => {
    calls.push({ url, opts });
  };
  return { fes, calls, trigger };
}

describe('chatComponent core', () => {
  let harness;
  let comp;

  beforeEach(() => {
    harness = makeFESHarness();
    comp = createChatComponent({ endpoint: '/chat-tools-run', fetchEventSource: harness.fes });
    comp.input = 'Hello';
  });

  it('sendMessage dispatches user message and initiates streaming', async () => {
    await comp.sendMessage();
    expect(comp.isStreaming).toBe(true);
    expect(harness.calls.length).toBe(1);
    expect(comp.messages[0]).toMatchObject({ role: 'user', content: 'Hello' });
    expect(comp.messages[1]).toMatchObject({ role: 'assistant', content: '' });
  });

  it('onmessage processes text_delta events and updates messages', async () => {
    await comp.sendMessage();
    harness.trigger.send({ event: 'text_delta', data: JSON.stringify({ delta: 'Hel' }) });
    harness.trigger.send({ event: 'text_delta', data: JSON.stringify({ delta: 'lo' }) });
    expect(comp.messages[1].content).toBe('Hello');
  });

  it('onmessage displays tool_call and tool_result events', async () => {
    await comp.sendMessage();
    harness.trigger.send({ event: 'tool_call', data: JSON.stringify({ name: 'web_search' }) });
    const toolMsg = comp.messages.find((m) => (m.content || '').includes('Using'));
    expect(toolMsg).toBeTruthy();
    harness.trigger.send({ event: 'tool_result', data: JSON.stringify({ name: 'web_search' }) });
    const updated = comp.messages.find((m) => (m.content || '').includes('completed'));
    expect(updated).toBeTruthy();
  });

  it('onerror sets error state when a stream error occurs', async () => {
    await comp.sendMessage();
    harness.trigger.error();
    expect(comp.error).toBe('Connection lost. Please try again.');
    expect(comp.isStreaming).toBe(false);
  });
});