<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = ['role_name', 'description', 'name'];

    /**
     * Backward-compatibility accessor if $role->name is called
     */
    public function getNameAttribute(): ?string
    {
        return $this->attributes['role_name'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['role_name'] = $value;
    }

    public function newEloquentBuilder($query)
    {
        return new class($query) extends \Illuminate\Database\Eloquent\Builder {
            public function where($column, $operator = null, $value = null, $boolean = 'and')
            {
                if (is_array($column)) {
                    $newColumn = [];
                    foreach ($column as $key => $val) {
                        $newKey = ($key === 'name' || $key === 'roles.name') ? 'role_name' : $key;
                        $newColumn[$newKey] = $val;
                    }
                    return parent::where($newColumn, $operator, $value, $boolean);
                }
                if ($column === 'name' || $column === 'roles.name') {
                    $column = ($column === 'roles.name') ? 'roles.role_name' : 'role_name';
                }
                return parent::where($column, $operator, $value, $boolean);
            }
        };
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }   
}