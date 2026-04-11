<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\VisitorCounter;
use Illuminate\Auth\Access\HandlesAuthorization;

class VisitorCounterPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:VisitorCounter');
    }

    public function view(AuthUser $authUser, VisitorCounter $visitorCounter): bool
    {
        return $authUser->can('View:VisitorCounter');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:VisitorCounter');
    }

    public function update(AuthUser $authUser, VisitorCounter $visitorCounter): bool
    {
        return $authUser->can('Update:VisitorCounter');
    }

    public function delete(AuthUser $authUser, VisitorCounter $visitorCounter): bool
    {
        return $authUser->can('Delete:VisitorCounter');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:VisitorCounter');
    }

    public function restore(AuthUser $authUser, VisitorCounter $visitorCounter): bool
    {
        return $authUser->can('Restore:VisitorCounter');
    }

    public function forceDelete(AuthUser $authUser, VisitorCounter $visitorCounter): bool
    {
        return $authUser->can('ForceDelete:VisitorCounter');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:VisitorCounter');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:VisitorCounter');
    }

    public function replicate(AuthUser $authUser, VisitorCounter $visitorCounter): bool
    {
        return $authUser->can('Replicate:VisitorCounter');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:VisitorCounter');
    }

}