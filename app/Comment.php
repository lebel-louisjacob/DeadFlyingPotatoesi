<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = ['user_id','station_id', 'text'];

    public function station()
    {
        return $this->belongsTo('App\Station');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
