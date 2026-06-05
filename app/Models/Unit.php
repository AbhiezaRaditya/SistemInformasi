<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'codename','study_program_id'];

    public function studyProgram(): BelongsTo
    {
        return $this->belongsTo(StudyProgram::class); 
    }
}
