<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['name', 'role_name', 'display_name', 'description'];

    public function getRoleNameAttribute(): ?string
    {
        return $this->attributes['role_name'] ?? $this->attributes['name'] ?? null;
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }   
}