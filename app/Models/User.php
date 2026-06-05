<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

// protected $fillable = ['name', 'password', 'username','study_program_id','unit_id','role'];
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,SoftDeletes, HasRoles;

    protected $fillable = [
        'name', 

        'password', 
        'username',
        'study_program_id',
        'unit_id'
    ];
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function activities(): HasMany
    {
    return  $this->hasMany(Activity::class);
    }

    public function studyProgram(): BelongsTo
    {

        return $this->belongsTo(StudyProgram::class); 
    }


     public function unit(): BelongsTo
    {

        return $this->belongsTo(Unit::class); 
    }

}
