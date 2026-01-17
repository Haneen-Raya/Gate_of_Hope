<?php

namespace Modules\Core\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Modules\Assessments\Models\AssessmentResult;
use Modules\Beneficiaries\Models\Beneficiary;
use Modules\CaseManagement\Models\BeneficiaryCase;
use Modules\CaseManagement\Models\CaseEvent;
use Modules\CaseManagement\Models\CaseReferral;
use Modules\Entities\Models\Entitiy;
use Modules\HumanResources\Models\Specialist;
use Modules\Programs\Models\Program;
use PhpParser\Node\Stmt\Case_;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
     *
     */
    public function beneficiary()
    {
        return $this->hasOne(Beneficiary::class);
    }

    /**
     *
     */
    public function specialist()
    {
        return $this->hasOne(Specialist::class);
    }

    /**
     *
     */
    public function assesmentsConducted(): HasMany
    {
        return $this->hasMany(AssessmentResult::class,'assessed_by');
    }

    /**
     *
     */
    public function assesmentsUpdated(): HasMany
    {
        return $this->hasMany(AssessmentResult::class,'updated_by');
    }

    /**
     *
     */
    public function cases(): HasMany
    {
        return $this->hasMany(BeneficiaryCase::class,'case_manager_id');
    }

    /**
     *
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     *
     */
    public function entitiy()
    {
        return $this->hasOne(Entitiy::class);
    }

    /**
     *
     */
    public function caseEventCreated(): HasMany
    {
        return $this->hasMany(CaseEvent::class,'created_by');
    }

    /**
     *
     */
    public function referralsCreated(): HasMany
    {
        return $this->hasMany(CaseReferral::class,'created_by');
    }

    /**
     *
     */
    public function referralsUpdated(): HasMany
    {
        return $this->hasMany(CaseReferral::class,'updated_by');
    }

    /**
     *
     */
    public function programsCreated(): HasMany
    {
        return $this->hasMany(Program::class,'created_by');
    }
}
