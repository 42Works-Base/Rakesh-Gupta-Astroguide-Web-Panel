<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralContent extends Model
{
    protected $table = 'general_content';

    protected $fillable = [
        'name',
        'sku',
        'content',
    ];

    public $timestamps = true;

    // If your timestamps are not the default column names (created_at, updated_at),
    // you can specify them like this:
    // const CREATED_AT = 'created_at';
    // const UPDATED_AT = 'updated_at';
}
