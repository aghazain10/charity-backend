<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = ['user_id', 'campaign_id', 'amount', 'message', 'is_anonymous'];

    protected static function boot()
    {
        parent::boot();

        // When a donation is created, update the campaign
        static::created(function ($donation) {
            $donation->campaign->increment('current_amount', $donation->amount);
        });

        
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
