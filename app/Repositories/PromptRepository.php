<?php

namespace App\Repositories;

use App\Models\Prompt;
use Illuminate\Support\Collection;

/**
 * Class PromptRepository
 *
 * Repository for Prompt Model
 */
class PromptRepository
{
    public function getLast2PromptsByUser(int $user_id): Collection
    {
        return Prompt::where('user_id', $user_id)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();
    }
}
