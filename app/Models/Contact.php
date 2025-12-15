<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'dob',
        'last_message_sent_year',
    ];

    protected $casts = [
        'dob' => 'date',
    ];
}
