<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PatientFollowUpForm;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientFollowUpFormPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PatientFollowUpForm');
    }

    public function view(AuthUser $authUser, PatientFollowUpForm $patientFollowUpForm): bool
    {
        return $authUser->can('View:PatientFollowUpForm');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PatientFollowUpForm');
    }

    public function update(AuthUser $authUser, PatientFollowUpForm $patientFollowUpForm): bool
    {
        return $authUser->can('Update:PatientFollowUpForm');
    }

    public function delete(AuthUser $authUser, PatientFollowUpForm $patientFollowUpForm): bool
    {
        return $authUser->can('Delete:PatientFollowUpForm');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PatientFollowUpForm');
    }

    public function restore(AuthUser $authUser, PatientFollowUpForm $patientFollowUpForm): bool
    {
        return $authUser->can('Restore:PatientFollowUpForm');
    }

    public function forceDelete(AuthUser $authUser, PatientFollowUpForm $patientFollowUpForm): bool
    {
        return $authUser->can('ForceDelete:PatientFollowUpForm');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PatientFollowUpForm');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PatientFollowUpForm');
    }

    public function replicate(AuthUser $authUser, PatientFollowUpForm $patientFollowUpForm): bool
    {
        return $authUser->can('Replicate:PatientFollowUpForm');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PatientFollowUpForm');
    }

}