<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VillaUnit;
use Illuminate\Auth\Access\HandlesAuthorization;

class VillaUnitPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VillaUnit');
    }

    public function view(AuthUser $authUser, VillaUnit $villaUnit): bool
    {
        return $authUser->can('View:VillaUnit');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VillaUnit');
    }

    public function update(AuthUser $authUser, VillaUnit $villaUnit): bool
    {
        return $authUser->can('Update:VillaUnit');
    }

    public function delete(AuthUser $authUser, VillaUnit $villaUnit): bool
    {
        return $authUser->can('Delete:VillaUnit');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VillaUnit');
    }

    public function restore(AuthUser $authUser, VillaUnit $villaUnit): bool
    {
        return $authUser->can('Restore:VillaUnit');
    }

    public function forceDelete(AuthUser $authUser, VillaUnit $villaUnit): bool
    {
        return $authUser->can('ForceDelete:VillaUnit');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VillaUnit');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VillaUnit');
    }

    public function replicate(AuthUser $authUser, VillaUnit $villaUnit): bool
    {
        return $authUser->can('Replicate:VillaUnit');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VillaUnit');
    }

}