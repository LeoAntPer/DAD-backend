<?php

namespace App\Policies;

use App\Models\ViewAuthUser;

class UserPolicy
{
    public function view(ViewAuthUser $user, ViewAuthUser $model)
    {
        return $user->user_type == "A" || $user->id == $model->id;
    }
    public function update(ViewAuthUser $user, ViewAuthUser $model)
    {
        return $user->user_type == "A" || $user->id == $model->id;
    }
    public function updatePassword(ViewAuthUser $user, ViewAuthUser $model)
    {
        return $user->id == $model->id;
    }
}
