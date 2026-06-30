<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\GeneralClassification;
use Illuminate\Auth\Access\HandlesAuthorization;

class GeneralClassificationPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:GeneralClassification');
    }

    public function view(AuthUser $authUser, GeneralClassification $generalClassification): bool
    {
        return $authUser->can('View:GeneralClassification');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:GeneralClassification');
    }

    public function update(AuthUser $authUser, GeneralClassification $generalClassification): bool
    {
        return $authUser->can('Update:GeneralClassification');
    }

    public function delete(AuthUser $authUser, GeneralClassification $generalClassification): bool
    {
        return $authUser->can('Delete:GeneralClassification');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:GeneralClassification');
    }

    public function restore(AuthUser $authUser, GeneralClassification $generalClassification): bool
    {
        return $authUser->can('Restore:GeneralClassification');
    }

    public function forceDelete(AuthUser $authUser, GeneralClassification $generalClassification): bool
    {
        return $authUser->can('ForceDelete:GeneralClassification');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:GeneralClassification');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:GeneralClassification');
    }

    public function replicate(AuthUser $authUser, GeneralClassification $generalClassification): bool
    {
        return $authUser->can('Replicate:GeneralClassification');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:GeneralClassification');
    }

}