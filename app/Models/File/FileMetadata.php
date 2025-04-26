<?php

namespace App\Models\File;

use Illuminate\Database\Eloquent\Model;
use App\Models\File\File;

class FileMetadata extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'original_name',
        'visibility',

        'mime_type',
        'size',
        'type',

        'custom',

        'file_id'
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
            'custom' => 'array',
        ];
    }

    // Relationships
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
