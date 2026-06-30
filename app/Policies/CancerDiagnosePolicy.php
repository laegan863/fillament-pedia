<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\CancerDiagnose;
use Illuminate\Auth\Access\HandlesAuthorization;

class CancerDiagnosePolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:CancerDiagnose');
    }

    public function view(AuthUser $authUser, CancerDiagnose $cancerDiagnose): bool
    {
        return $authUser->can('View:CancerDiagnose');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:CancerDiagnose');
    }

    public function update(AuthUser $authUser, CancerDiagnose $cancerDiagnose): bool
    {
        return $authUser->can('Update:CancerDiagnose');
    }

    public function delete(AuthUser $authUser, CancerDiagnose $cancerDiagnose): bool
    {
        return $authUser->can('Delete:CancerDiagnose');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:CancerDiagnose');
    }

    public function restore(AuthUser $authUser, CancerDiagnose $cancerDiagnose): bool
    {
        return $authUser->can('Restore:CancerDiagnose');
    }

    public function forceDelete(AuthUser $authUser, CancerDiagnose $cancerDiagnose): bool
    {
        return $authUser->can('ForceDelete:CancerDiagnose');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:CancerDiagnose');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:CancerDiagnose');
    }

    public function replicate(AuthUser $authUser, CancerDiagnose $cancerDiagnose): bool
    {
        return $authUser->can('Replicate:CancerDiagnose');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:CancerDiagnose');
    }

}