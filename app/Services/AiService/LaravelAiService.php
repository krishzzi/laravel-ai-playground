<?php

namespace App\Services\AiService;

use Illuminate\Foundation\Auth\User;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Enums\Lab;

class LaravelAiService
{

    protected ?User $user = null;

    public function __construct(Lab $lab,?User $user = null)
    {

    }

    public function inference(string $prompt,array $attachment = [])
    {

    }



}
