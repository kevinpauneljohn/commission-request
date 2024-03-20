<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Ramsey\Uuid\Uuid;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'firstname',
        'middlename',
        'lastname',
        'date_of_birth',
        'mobile_number',
        'email',
        'username',
        'password',
        'role'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $appends = ['full_name'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function newUniqueId(): string
    {
        return (string) Uuid::uuid4();
    }

    public function uniqueIds(): array
    {
        return ['id'];
    }

    public function adminlte_image()
    {
        return 'https://picsum.photos/300/300';
    }

    public function adminlte_desc()
    {
        $user = auth()->user();
        return ucwords($user->firstname.' '.$user->lastname);
    }

    public function adminlte_profile_url()
    {
        return 'profile/username';
    }

    public function setRoleAttribute($value): void
    {
        $this->attributes['role'] = json_encode($value);
    }

    public function getRoleAttribute($value)
    {
        return json_decode($value);
    }

    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function getFullNameAttribute()
    {
        return "{$this->firstname} {$this->lastname}";
    }

    public function tasks()
    {
        return $this->hasMany(Task::class,'assigned_to');
    }

    public function creatorTasks()
    {
        return $this->hasMany(Task::class,'creator');
    }

    public function actionTakens()
    {
        return $this->hasMany(ActionTaken::class);
    }

    public function findings()
    {
        return $this->hasMany(Finding::class);
    }

    public function automations()
    {
        return $this->hasMany(Automation::class);
    }

    public function automationTasks()
    {
        return $this->hasMany(AutomationTask::class,'creator');
    }
}
