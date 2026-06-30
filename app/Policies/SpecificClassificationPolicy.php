<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\SpecificClassification;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpecificClassificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:SpecificClassification');
    }

    public function view(AuthUser $authUser, SpecificClassification $specificClassification): bool
    {
        return $authUser->can('View:SpecificClassification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:SpecificClassification');
    }

    public function update(AuthUser $authUser, SpecificClassification $specificClassification): bool
    {
        return $authUser->can('Update:SpecificClassification');
    }

    public function delete(AuthUser $authUser, SpecificClassification $specificClassification): bool
    {
        return $authUser->can('Delete:SpecificClassification');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:SpecificClassification');
    }

    public function restore(AuthUser $authUser, SpecificClassification $specificClassification): bool
    {
        return $authUser->can('Restore:SpecificClassification');
    }

    public function forceDelete(AuthUser $authUser, SpecificClassification $specificClassification): bool
    {
        return $authUser->can('ForceDelete:SpecificClassification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:SpecificClassification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:SpecificClassification');
    }

    public function replicate(AuthUser $authUser, SpecificClassification $specificClassification): bool
    {
        return $authUser->can('Replicate:SpecificClassification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:SpecificClassification');
    }

}