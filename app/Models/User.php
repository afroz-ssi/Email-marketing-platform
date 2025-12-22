<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'country_code',
        'password',
        'is_approved',
        'phone',
        'dob',
        'license_no',
        'role',
        'medical_practice',
        'profile_photo_path',
        'provider',
        'provider_id',
        'status',

        'certifications',
        'biography',
        'insurance',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'active' => 'string',

        'certifications' => 'array',
        'documents' => 'array',
        'packages' => 'array',
    ];

    protected $appends = [
        'full_name',
        'role_name',
        'profile_photo_url',
        'documents_url',
        'clean_phone',
    ];

    public function getdocumentsUrlAttribute()
    {
        if (!is_array($this->documents)) {
            return [];
        }

        return array_filter(array_map(function ($image) {
            if (is_string($image)) {
                return asset($image);
            }
            return null;
        }, $this->documents));   
    }




    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = bcrypt($password);
    }

    public function getCleanPhoneAttribute()
    {
        return $this->phone ? preg_replace('/\D/', '', $this->phone) : null;
    }

        public function getRoleNameAttribute()
    {
        if ($this->roles()->exists())
            return $this->roles()->first()->name;
        else
            return 0;
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getDobAttribute($val)
    {
        return $val ? date('m-d-Y', strtotime($val)) : null;
    }

    public function getProfilePhotoUrlAttribute()
    {
        // return ("{$this->profile_photo_path}") ? url()->to('/' . "{$this->profile_photo_path}") : asset('admin_assets/demo.png');

        $path = asset('admin_assets/demo.png');
        if ($this->profile_photo_path) {
            $path = asset($this->profile_photo_path);
        }
        return $path;
    }
    public function availability()
    {
        return $this->hasMany(WellnessProfessionalAvailability::class, 'professional_id');
    }

    public function service()
    {
        return $this->hasMany(Service::class, 'user_id');
    }

    public function gallery()
    {
        return $this->hasMany(WellnessProfessionalGallery::class, 'professional_id');
    }

    public function review()
    {
        return $this->hasMany(ReviewWellnessProfessional::class, 'professional_id');
    }
    public function bookings()
    {
        return $this->hasMany(ServiceBooking::class, 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(AdminActivity::class, 'admin_id', 'id');
    }

    public function expertise()
    {
        return $this->hasMany(AreasOfExpertise::class, 'professional_id');
    }

    public function bookingsAsClient() {
        return $this->hasMany(ServiceBooking::class, 'user_id');
    }
    
    public function bookingsAsProfessional() {
        return $this->hasMany(ServiceBooking::class, 'professional_id');
    }

    public function membershipBookings()
    {
        return $this->hasMany(MembershipBooking::class, 'professional_id');
    }


    // ============ If the user is a client and has a doctor ===========
    public function doctor()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    // ============ If the user is a doctor and has many clients ========
    public function clients()
    {
        return $this->hasMany(User::class, 'provider_id');
    }


    public function sentReferrals()
    {
        return $this->hasMany(ShareReferralsCode::class, 'professional_id');
    }

    public function receivedReferral()
    {
        return $this->hasOne(ShareReferralsCode::class, 'referred_professional_id');
    }

    public function ProfessionalDocuments()
    {
        return $this->hasMany(ProfessionalDocuments::class, 'professional_id');
    }

    public function ProfessionalCertificates()
    {
        return $this->hasMany(ProfessionalCertificate::class, 'professional_id');
    }

    public function referralCodes()
    {
        return $this->hasMany(ReferralCode::class, 'professional_id');
    }
}
