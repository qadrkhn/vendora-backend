<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Model;
use App\Models\File\File;

class Category extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'url',
        'featured',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [

    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'featured' => 'boolean'
        ];
    }

    // Relationships
    public function file()
    {
        return $this->morphOne(File::class, 'fileable');
    }
}
