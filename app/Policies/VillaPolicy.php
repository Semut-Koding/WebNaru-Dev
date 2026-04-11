<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Villa;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillaPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Villa');
    }

    public function view(AuthUser $authUser, Villa $villa): bool
    {
        return $authUser->can('View:Villa');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Villa');
    }

    public function update(AuthUser $authUser, Villa $villa): bool
    {
        return $authUser->can('Update:Villa');
    }

    public function delete(AuthUser $authUser, Villa $villa): bool
    {
        return $authUser->can('Delete:Villa');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Villa');
    }

    public function restore(AuthUser $authUser, Villa $villa): bool
    {
        return $authUser->can('Restore:Villa');
    }

    public function forceDelete(AuthUser $authUser, Villa $villa): bool
    {
        return $authUser->can('ForceDelete:Villa');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Villa');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Villa');
    }

    public function replicate(AuthUser $authUser, Villa $villa): bool
    {
        return $authUser->can('Replicate:Villa');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Villa');
    }

}