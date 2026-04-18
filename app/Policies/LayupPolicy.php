<?php

namespace App\Policies;

use App\Models\Layup;
use App\Models\User;

class LayupPolicy
{
    public function view(?User $user, Layup $layup): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return $user !== null;
    }

    public function update(?User $user, Layup $layup): bool
    {
        return $user !== null;
    }

    public function delete(?User $user, Layup $layup): bool
    {
        return $user !== null;
    }
}
