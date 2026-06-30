<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\ParentClassification;
use Illuminate\Auth\Access\HandlesAuthorization;

class ParentClassificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:ParentClassification');
    }

    public function view(AuthUser $authUser, ParentClassification $parentClassification): bool
    {
        return $authUser->can('View:ParentClassification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:ParentClassification');
    }

    public function update(AuthUser $authUser, ParentClassification $parentClassification): bool
    {
        return $authUser->can('Update:ParentClassification');
    }

    public function delete(AuthUser $authUser, ParentClassification $parentClassification): bool
    {
        return $authUser->can('Delete:ParentClassification');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:ParentClassification');
    }

    public function restore(AuthUser $authUser, ParentClassification $parentClassification): bool
    {
        return $authUser->can('Restore:ParentClassification');
    }

    public function forceDelete(AuthUser $authUser, ParentClassification $parentClassification): bool
    {
        return $authUser->can('ForceDelete:ParentClassification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:ParentClassification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:ParentClassification');
    }

    public function replicate(AuthUser $authUser, ParentClassification $parentClassification): bool
    {
        return $authUser->can('Replicate:ParentClassification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:ParentClassification');
    }

}