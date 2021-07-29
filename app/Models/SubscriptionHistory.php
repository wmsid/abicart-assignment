<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionHistory extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $fillable = array('user_id', 'provider_id', 'provider_identifier','status_to','expire_at','action_at');
}
