<?php

namespace App\Policies;

use App\Models\Layer;
use App\Models\User;

class LayerPolicy
{
    public function view(?User $user, Layer $layer): bool
    {
        return true;
    }

    public function create(?User $user): bool
    {
        return $user !== null;
    }

    public function update(?User $user, Layer $layer): bool
    {
        return $user !== null;
    }

    public function delete(?User $user, Layer $layer): bool
    {
        return $user !== null;
    }
}
