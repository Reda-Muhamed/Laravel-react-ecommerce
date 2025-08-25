<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;
use Stripe\Stripe;
use Stripe\StripeClient;

/**
 * @method bool hasAnyRole(string|array|\Spatie\Permission\Models\Role|\Illuminate\Support\Collection $roles)
 * @method bool hasRole(string|array|\Spatie\Permission\Models\Role|\Illuminate\Support\Collection $roles)
 * @method bool hasPermissionTo(string|\Spatie\Permission\Models\Permission $permission)
 */
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;
    use HasRoles;
    use Billable;

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
     * Get the vendor associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function vendor(): HasOne
    {
        return $this->hasOne(Vendor::class, 'user_id');
    }

    public function createStripeAccount()
    {
        $stripe = new StripeClient(config('app.stripe_secret'));

        $account = $stripe->accounts->create([
            'type' => 'express',
            // dd($this->email),
            'email' => $this->email,
        ]);
        // dd($account);

        $this->stripe_id = $account->id;
        $this->save();

        return $account;
    }

    public function getStripeAccountLink()
    {
        $stripe = new StripeClient(config('app.stripe_secret'));

        $link = $stripe->accountLinks->create([

            'account' => $this->stripe_id,
            'refresh_url' => route('stripe.account.refresh'),
            'return_url' => route('stripe.account.return'),
            'type' => 'account_onboarding',
        ]);

        return $link->url;
    }

    public function isStripeAccountActive()
    {
        if (!$this->stripe_id) {
            return false;
        }

        $stripe = new StripeClient(config('app.stripe_secret'));
        $account = $stripe->accounts->retrieve($this->stripe_id, []);
        return $account->details_submitted && $account->charges_enabled;
    }
    // In User model
    public function transfer(int $amount, string $currency = 'USD')
    {
         Stripe::setApiKey(config('app.stripe_secret'));
        return \Stripe\Transfer::create([
            'amount' => $amount,
            'currency' => $currency,
            'destination' => $this->stripe_id, // vendor's connected Stripe account
        ]);
    }
}
