<?php

use App\Models\Prompt;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('gets prompts successfully', function () {
    $prompts = Prompt::factory()->count(2)->create(['user_id' => $this->user->id]);

    $response = $this->getJson('/prompts');

    $response->assertStatus(200)
        ->assertJson([
            'prompts' => $prompts->toArray(),
        ]);
});

it('returns error when fetching prompts unauthenticated', function () {
    Auth::logout();

    $response = $this->getJson('/prompts');

    $response->assertStatus(401);
});

it('posts a prompt successfully', function () {
    Http::fake([
        '*' => Http::response('mocked response from OpenAI', 200),
    ]);

    $payload = [
        'prompt' => 'Generate a test response',
    ];

    $response = $this->postJson('/prompt', $payload);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'id', 'prompt', 'prompt_answer', 'user_id'
        ])
        ->assertJsonPath('prompt', $payload['prompt']);
});
