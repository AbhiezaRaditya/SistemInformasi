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
        return $authUser->can('View:StudyProgram') || $authUser->can('view_study_program');
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
}