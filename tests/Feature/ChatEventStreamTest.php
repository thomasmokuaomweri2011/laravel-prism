<?php

use Illuminate\Testing\TestResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

it('asEventStreamResponse streams SSE for tool-enabled chat', function () {
    // Arrange: minimal messages payload
    $payload = [
        'messages' => [
            ['role' => 'user', 'content' => 'Test tools'],
        ],
    ];

    // Act
    $response = $this->post('/chat-tools-run', $payload);

    // Assert: base response is a StreamedResponse (SSE), with proper headers
    $base = $response->baseResponse;
    expect($base)->toBeInstanceOf(StreamedResponse::class);
    $response->assertHeader('Content-Type', 'text/event-stream');
});