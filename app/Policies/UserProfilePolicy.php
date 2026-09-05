<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserProfilePolicy
{
    /**
     * A user may only update their own profile.
     * Admins have no special power here — profile expression is personal.
     */
    public function update(User $authUser, User $targetUser): bool
    {
        return $authUser->id === $targetUser->id;
    }
}
