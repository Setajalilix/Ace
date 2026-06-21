<?php

namespace App\Shared\Policies;

use App\Domains\Auth\Models\User;

trait AuthorizesOwnership
{
    protected function owns(User $user, object $model): bool
    {
        return isset($model->user_id) && $user->id === $model->user_id;
    }
}
