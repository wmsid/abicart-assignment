<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;
    protected $fillable = array('user_id', 'provider_id', 'provider_identifier','status');
    
    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }
}
