<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\AttractionCounter;
use Illuminate\Auth\Access\HandlesAuthorization;

class AttractionCounterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:AttractionCounter');
    }

    public function view(AuthUser $authUser, AttractionCounter $attractionCounter): bool
    {
        return $authUser->can('View:AttractionCounter');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:AttractionCounter');
    }

    public function update(AuthUser $authUser, AttractionCounter $attractionCounter): bool
    {
        return $authUser->can('Update:AttractionCounter');
    }

    public function delete(AuthUser $authUser, AttractionCounter $attractionCounter): bool
    {
        return $authUser->can('Delete:AttractionCounter');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:AttractionCounter');
    }

    public function restore(AuthUser $authUser, AttractionCounter $attractionCounter): bool
    {
        return $authUser->can('Restore:AttractionCounter');
    }

    public function forceDelete(AuthUser $authUser, AttractionCounter $attractionCounter): bool
    {
        return $authUser->can('ForceDelete:AttractionCounter');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:AttractionCounter');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:AttractionCounter');
    }

    public function replicate(AuthUser $authUser, AttractionCounter $attractionCounter): bool
    {
        return $authUser->can('Replicate:AttractionCounter');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:AttractionCounter');
    }

}