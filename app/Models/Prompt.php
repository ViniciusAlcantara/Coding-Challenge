<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prompt extends Model
{
    use HasFactory;

    protected $table = 'prompts';
    protected $primaryKey = 'id';
    public $incrementing = true;

    /**
     * Get the user associated with the user.
     */
    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
