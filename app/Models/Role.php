<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['role_name', 'description'];

    /**
     * Backward-compatibility accessor if $role->name is called
     */
    public function getNameAttribute(): ?string
    {
        return $this->attributes['role_name'] ?? null;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }   
}