<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebPages extends Model
{
    protected $table = 'webpage';

    protected $fillable = [
        'title',
        'content',
        'type',
    ];
}
