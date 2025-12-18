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

    public function getShortNameAttribute()
    {
        $words = explode(' ', $this->name);
        if (count($words) > 3) {
            return implode(' ', array_slice($words, -3));
        }
        return $this->name;
    }
}
