<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\Attraction;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttractionPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Attraction');
    }

    public function view(AuthUser $authUser, Attraction $attraction): bool
    {
        return $authUser->can('View:Attraction');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Attraction');
    }

    public function update(AuthUser $authUser, Attraction $attraction): bool
    {
        return $authUser->can('Update:Attraction');
    }

    public function delete(AuthUser $authUser, Attraction $attraction): bool
    {
        return $authUser->can('Delete:Attraction');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Attraction');
    }

    public function restore(AuthUser $authUser, Attraction $attraction): bool
    {
        return $authUser->can('Restore:Attraction');
    }

    public function forceDelete(AuthUser $authUser, Attraction $attraction): bool
    {
        return $authUser->can('ForceDelete:Attraction');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Attraction');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Attraction');
    }

    public function replicate(AuthUser $authUser, Attraction $attraction): bool
    {
        return $authUser->can('Replicate:Attraction');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Attraction');
    }

}