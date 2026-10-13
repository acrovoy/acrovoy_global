<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\HasApiTokens;
use App\Models\UserSetting;
use App\Domain\Contact\Models\Contact;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domain\Contact\Traits\HasContacts;
use App\Domain\Media\Models\Media;
use App\Domain\Email\Services\EmailService;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasContacts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'currency',
        'role',
        'purchase_country',
        'premium_plan_id',
        'timezone',
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

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_blocked' => 'boolean',
    ];

    /**
     * Send the email verification link through the ACROVOY email queue.
     */
    public function sendEmailVerificationNotification(): void
    {

    

        $expires = (int) config('auth.verification.expire', 60);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes($expires),
            [
                'id' => $this->getKey(),
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );

        app(EmailService::class)->sendSystemEmail(
            recipient: $this->getEmailForVerification(),
            title: 'Verify your ACROVOY email address',
            messageText: "Thank you for registering with ACROVOY.\n\n"
                . "Please verify your email address by clicking the button below.\n\n"
                . "This link will expire in {$expires} minutes.",
            actionUrl: $verificationUrl,
            actionLabel: 'Verify Email Address',
        );
    }

    public function premiumPlan()
    {
        return $this->belongsTo(PremiumSellerPlan::class, 'premium_plan_id');
    }

    // РЎРІСЏР·СЊ СЃ РїСЂРµРјРёСѓРј-РїР»Р°РЅРѕРј РїРѕРєСѓРїР°С‚РµР»СЏ
    public function buyerPremiumPlan()
    {
        return $this->belongsTo(PremiumSellerPlan::class, 'buyer_premium_plan_id');
    }

    // РџСЂРѕРІРµСЂРєР° Р°РєС‚РёРІРЅРѕРіРѕ РїСЂРµРјРёСѓРј-РїР»Р°РЅР° РїРѕСЃС‚Р°РІС‰РёРєР°
    public function isSupplierPremium()
    {
        return $this->premium_plan_id
            && $this->supplier_premium_start
            && $this->supplier_premium_end
            && now()->between($this->supplier_premium_start, $this->supplier_premium_end);
    }

    // РџСЂРѕРІРµСЂРєР° Р°РєС‚РёРІРЅРѕРіРѕ РїСЂРµРјРёСѓРј-РїР»Р°РЅР° РїРѕРєСѓРїР°С‚РµР»СЏ
    public function isBuyerPremium()
    {
        return $this->buyer_premium_plan_id
            && $this->buyer_premium_start
            && $this->buyer_premium_end
            && now()->between($this->buyer_premium_start, $this->buyer_premium_end);
    }

    public function getFullNameAttribute()
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class);
    }

    public function defaultAddress()
    {
        return $this->hasOne(UserAddress::class)->where('is_default', true);
    }

    // РћС‚Р·С‹РІС‹, РѕСЃС‚Р°РІР»РµРЅРЅС‹Рµ РїРѕР»СЊР·РѕРІР°С‚РµР»РµРј
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // РЎРїРѕСЂС‹, РѕС‚РєСЂС‹С‚С‹Рµ РїРѕР»СЊР·РѕРІР°С‚РµР»РµРј
    public function orderDisputes()
    {
        return $this->hasMany(OrderDispute::class);
    }

    // Р—Р°РєР°Р·С‹ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ (РµСЃР»Рё РµС‰С‘ РЅРµС‚)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'model');
    }

    public function avatar()
    {
        return $this->media()
            ->where('collection', 'avatars')
            ->latest()
            ->first();
    }

    public function wishlist()
    {
        return $this->belongsToMany(
            \App\Models\Product::class,
            'wishlists'
        )->withTimestamps();
    }

    public function companyMemberships()
    {
        return $this->hasMany(CompanyUser::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class);
    }

    public function setting(
        string $key,
        mixed $default = null
    ): mixed {
        return $this->settings
            ->firstWhere('key', $key)
            ?->value ?? $default;
    }

    public function setSetting(
        string $key,
        mixed $value
    ): UserSetting {
        return UserSetting::setValue(
            $this->id,
            $key,
            $value
        );
    }

    public function shippingTemplates()
    {
        return $this->morphMany(
            ShippingTemplate::class,
            'provider',
            'provider_type',
            'provider_id'
        )->where('is_active', 1);
    }

    public function addresses()
    {
        return $this->hasMany(UserAddress::class)
            ->where('user_type', self::class);
    }

    public function businessTypes()
    {
        return $this->morphToMany(
            BusinessType::class,
            'business_typeable'
        );
    }

    public function profileContact()
    {
        return $this->morphOne(Contact::class, 'contactable')
            ->where('show_in_profile', true);
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }
}
