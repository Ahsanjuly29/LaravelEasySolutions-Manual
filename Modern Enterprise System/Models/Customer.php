<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\FormatsTimestamps;

class Customer extends Model
{
    use FormatsTimestamps;

    protected $fillable = [
        'name',
        'email',
        'phone',
    ];
}
