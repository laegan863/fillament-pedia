<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\PatientFollowupAmmendments;
use Illuminate\Auth\Access\HandlesAuthorization;

class PatientFollowupAmmendmentsPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:PatientFollowupAmmendments');
    }

    public function view(AuthUser $authUser, PatientFollowupAmmendments $patientFollowupAmmendments): bool
    {
        return $authUser->can('View:PatientFollowupAmmendments');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:PatientFollowupAmmendments');
    }

    public function update(AuthUser $authUser, PatientFollowupAmmendments $patientFollowupAmmendments): bool
    {
        return $authUser->can('Update:PatientFollowupAmmendments');
    }

    public function delete(AuthUser $authUser, PatientFollowupAmmendments $patientFollowupAmmendments): bool
    {
        return $authUser->can('Delete:PatientFollowupAmmendments');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:PatientFollowupAmmendments');
    }

    public function restore(AuthUser $authUser, PatientFollowupAmmendments $patientFollowupAmmendments): bool
    {
        return $authUser->can('Restore:PatientFollowupAmmendments');
    }

    public function forceDelete(AuthUser $authUser, PatientFollowupAmmendments $patientFollowupAmmendments): bool
    {
        return $authUser->can('ForceDelete:PatientFollowupAmmendments');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:PatientFollowupAmmendments');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:PatientFollowupAmmendments');
    }

    public function replicate(AuthUser $authUser, PatientFollowupAmmendments $patientFollowupAmmendments): bool
    {
        return $authUser->can('Replicate:PatientFollowupAmmendments');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:PatientFollowupAmmendments');
    }

}