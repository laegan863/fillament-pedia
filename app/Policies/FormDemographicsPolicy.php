<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\FormDemographics;
use Illuminate\Auth\Access\HandlesAuthorization;

class FormDemographicsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:FormDemographics');
    }

    public function view(AuthUser $authUser, FormDemographics $formDemographics): bool
    {
        return $authUser->can('View:FormDemographics');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:FormDemographics');
    }

    public function update(AuthUser $authUser, FormDemographics $formDemographics): bool
    {
        return $authUser->can('Update:FormDemographics');
    }

    public function delete(AuthUser $authUser, FormDemographics $formDemographics): bool
    {
        return $authUser->can('Delete:FormDemographics');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:FormDemographics');
    }

    public function restore(AuthUser $authUser, FormDemographics $formDemographics): bool
    {
        return $authUser->can('Restore:FormDemographics');
    }

    public function forceDelete(AuthUser $authUser, FormDemographics $formDemographics): bool
    {
        return $authUser->can('ForceDelete:FormDemographics');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:FormDemographics');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:FormDemographics');
    }

    public function replicate(AuthUser $authUser, FormDemographics $formDemographics): bool
    {
        return $authUser->can('Replicate:FormDemographics');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:FormDemographics');
    }

}