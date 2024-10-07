<?php

namespace App\Services;

use App\Models\Prompt;

class PromptService
{
    public function savePrompt(
        ?int $promptId = null,
        string $prompt,
        string $promptAnswer,
        ?int $userId = null,
    ): Prompt
    {
        $promptModel = !empty($promptId) ? Prompt::find($promptId) : new Prompt();

        $promptModel->prompt = $prompt;
        $promptModel->prompt_answer = $promptAnswer;

        if (!$promptId) {
            $promptModel->user_id = $userId;
        }

        $promptModel->save();

        return $promptModel->refresh();
    }
}