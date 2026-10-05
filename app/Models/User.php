<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'cpf',
        'type',
        'phone',
        'education_level',
        'google_id',
        'is_customer',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
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

    /**
     * Get the courses enrolled by the user
     */
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
            ->withPivot(['progress', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get the user's course enrollments
     */
    public function enrollments()
    {
        return $this->hasMany(UserCourse::class);
    }

    /**
     * Get the user's certificates
     */
    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    /**
     * Get the user's completed activities
     */
    public function completedActivities()
    {
        return $this->hasMany(UserActivity::class);
    }

    /**
     * Get the activities completed by the user (direct relationship)
     */
    public function activities()
    {
        return $this->belongsToMany(Activity::class, 'user_activities')
            ->withPivot(['completed', 'completed_at'])
            ->withTimestamps();
    }

    /**
     * Get the user's quiz/exam attempts
     */
    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->type === 'admin';
    }

    /**
     * Check if user is student
     */
    public function isStudent(): bool
    {
        return $this->type === 'student';
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    /**
     * Check if user is eligible for free access
     * Users with is_customer = true are existing customers and have free access
     */
    public function isEligibleForFreeAccess(): bool
    {
        return $this->is_customer === true;
    }

    /**
     * Check if user is a Google user
     */
    public function isGoogleUser()
    {
        return !is_null($this->google_id);
    }

    /**
     * Get education level options
     */
    public static function getEducationLevelOptions(): array
    {
        return [
            'fundamental' => 'Ensino Fundamental',
            'medio_incompleto' => 'Ensino Médio Incompleto',
            'medio_completo' => 'Ensino Médio Completo',
            'superior_incompleto' => 'Ensino Superior Incompleto',
            'superior_completo' => 'Ensino Superior Completo',
            'pos_graduacao' => 'Pós-graduação',
        ];
    }

    /**
     * Get education level label
     */
    public function getEducationLevelLabel(): ?string
    {
        return $this->education_level ? self::getEducationLevelOptions()[$this->education_level] : null;
    }

    /**
     * Get education level numeric index
     */
    public function getEducationLevelIndex(): ?int
    {
        $options = array_keys(self::getEducationLevelOptions());
        return $this->education_level ? array_search($this->education_level, $options) + 1 : null;
    }
}
