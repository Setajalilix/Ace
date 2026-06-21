<?php

namespace App\Domains\Notes\Enums;

enum NoteType: string
{
    case Quick = 'quick';
    case Permanent = 'permanent';
    case Project = 'project';

    public function label(): string
    {
        return match ($this) {
            self::Quick => 'Quick Note',
            self::Permanent => 'Permanent Note',
            self::Project => 'Project Note',
        };
    }
}
