<?php

declare(strict_types=1);

namespace App\Policies;

use Illuminate\Foundation\Auth\User as AuthUser;
use App\Models\StudyProgram;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudyProgramPolicy
{
    use HandlesAuthorization;
    
    public function viewAny(AuthUser $authUser): bool
    {
        return $authUser->can('ViewAny:StudyProgram');
    }

    public function view(AuthUser $authUser, StudyProgram $studyProgram): bool
    {
        return $authUser->can('View:StudyProgram');
    }

    public function create(AuthUser $authUser): bool
    {
        return $authUser->can('Create:StudyProgram');
    }

    public function update(AuthUser $authUser, StudyProgram $studyProgram): bool
    {
        return $authUser->can('Update:StudyProgram');
    }

    public function delete(AuthUser $authUser, StudyProgram $studyProgram): bool
    {
        return $authUser->can('Delete:StudyProgram');
    }

    public function deleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('DeleteAny:StudyProgram');
    }

    public function restore(AuthUser $authUser, StudyProgram $studyProgram): bool
    {
        return $authUser->can('Restore:StudyProgram');
    }

    public function forceDelete(AuthUser $authUser, StudyProgram $studyProgram): bool
    {
        return $authUser->can('ForceDelete:StudyProgram');
    }

    public function forceDeleteAny(AuthUser $authUser): bool
    {
        return $authUser->can('ForceDeleteAny:StudyProgram');
    }

    public function restoreAny(AuthUser $authUser): bool
    {
        return $authUser->can('RestoreAny:StudyProgram');
    }

    public function replicate(AuthUser $authUser, StudyProgram $studyProgram): bool
    {
        return $authUser->can('Replicate:StudyProgram');
    }

    public function reorder(AuthUser $authUser): bool
    {
        return $authUser->can('Reorder:StudyProgram');
    }

}