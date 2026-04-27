<?php

namespace App\Policies;

use App\Models\Device;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DevicePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasPermissionTo('manage-devices') || $user->hasPermissionTo('view-dashboard');
    }

    public function view(User $user, Device $device)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $device->user_id === $user->id;
    }

    public function create(User $user)
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Device $device)
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Device $device)
    {
        return $user->hasRole('admin');
    }

    public function sendCommand(User $user, Device $device)
    {
        if ($user->hasRole('admin')) {
            return true;
        }

        return $user->hasPermissionTo('send-commands') && $device->user_id === $user->id;
    }
}
