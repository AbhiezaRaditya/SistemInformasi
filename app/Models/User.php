<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\HasName; // <-- TAMBAHKAN INI

#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements HasName // <-- TAMBAHKAN IMPLEMENTS
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes, HasRoles;

    protected $fillable = [
        'name',
        'password',
        'username',
        // DIHAPUS: 'study_program_id' dan 'unit_id' sudah tidak ada di tabel users lagi
        // (sekarang dikelola lewat relasi many-to-many studyPrograms() & units())
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
            'last_login_at' => 'datetime',
            'last_logout_at' => 'datetime',
        ];
    }

    public function activities(): HasMany
    {
        return $this->hasMany(Activity::class);
    }

    // DIUBAH: dari belongsTo() jadi belongsToMany() karena sekarang 1 user bisa
    // punya lebih dari 1 Program Studi.
    public function studyPrograms(): BelongsToMany
    {
        return $this->belongsToMany(StudyProgram::class, 'study_program_user');
    }

    // DIUBAH: dari belongsTo() jadi belongsToMany() karena sekarang 1 user bisa
    // punya lebih dari 1 Unit.
    public function units(): BelongsToMany
    {
        return $this->belongsToMany(Unit::class, 'unit_user');
    }

    /**
     * Mengembalikan nama user untuk Filament
     */
    public function getFilamentName(): string
    {
        return $this->name;
    }
}