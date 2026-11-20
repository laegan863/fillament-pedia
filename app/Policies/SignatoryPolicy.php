<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Signatory;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Foundation\Auth\User as AuthUser;

class SignatoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:Signatory');
    }

    public function view(AuthUser $authUser, Signatory $signatory): bool
    {
        return $authUser->can('View:Signatory');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:Signatory');
    }

    public function update(AuthUser $authUser, Signatory $signatory): bool
    {
        return $authUser->can('Update:Signatory');
    }

    public function delete(AuthUser $authUser, Signatory $signatory): bool
    {
        return $authUser->can('Delete:Signatory');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:Signatory');
    }

    public function restore(AuthUser $authUser, Signatory $signatory): bool
    {
        return $authUser->can('Restore:Signatory');
    }

    public function forceDelete(AuthUser $authUser, Signatory $signatory): bool
    {
        return $authUser->can('ForceDelete:Signatory');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:Signatory');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:Signatory');
    }

    public function replicate(AuthUser $authUser, Signatory $signatory): bool
    {
        return $authUser->can('Replicate:Signatory');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:Signatory');
    }
}
