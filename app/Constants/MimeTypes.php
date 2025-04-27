<?php

namespace App\Constants;

class MimeTypes
{
    public const IMAGE = [
        'image/jpeg',
        'image/png',
        'image/webp',
        'image/gif',
        'image/svg+xml',
    ];

    public const PDF = [
        'application/pdf',
    ];

    public const DOCS = [
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];

    public const ALL = [
        ...self::IMAGE,
        ...self::PDF,
        ...self::DOCS,
    ];
}
