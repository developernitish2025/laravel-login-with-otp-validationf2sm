<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Namer extends Model {
    //
    use HasFactory;

    // Define fillable attributes ( i.e., which fields are mass assignable )
    protected $fillable = [
        'phone_number',
    ];

    // Define guarded attributes ( i.e., which fields are NOT mass assignable )
    protected $guarded = [
        'otp',           // Prevent mass assignment of OTP
        'otp_expiry',    // Prevent mass assignment of OTP expiry
    ];

    // Optionally, you can also use casting for specific fields
    protected $casts = [
        'otp_expiry' => 'datetime',
    ];
}
