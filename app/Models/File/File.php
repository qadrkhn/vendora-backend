<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Model;

use App\Models\File\FileMetadata;

class File extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'url',
        'path',

        'disk',

        'fileable_id',
        'fileable_type',

    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'disk',
        'fileable_id',
        'fileable_type',
        'path'
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
        ];
    }

    // Relationships
    public function fileable()
    {
        return $this->morphTo();
    }

    public function metadata()
    {
        return $this->hasOne(FileMetadata::class);
    }


}
