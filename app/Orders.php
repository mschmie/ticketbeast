<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    protected $guarded = [];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

}
