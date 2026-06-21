<?php

namespace App\Domains\Tasks\Enums;

enum TaskPriority: int
{
    case P1 = 1;
    case P2 = 2;
    case P3 = 3;

    public function label(): string
    {
        return match ($this) {
            self::P1 => 'Must today',
            self::P2 => 'Should do',
            self::P3 => 'Optional',
        };
    }

    public function shortLabel(): string
    {
        return 'P'.$this->value;
    }

    public function color(): string
    {
        return match ($this) {
            self::P1 => '#E05D44',
            self::P2 => '#E6A23C',
            self::P3 => '#A8958B',
        };
    }

    public function bgClass(): string
    {
        return match ($this) {
            self::P1 => 'bg-[#FEE8E4] text-[#C0392B] border-[#F5C4BC]',
            self::P2 => 'bg-[#FEF3E0] text-[#B7791F] border-[#F5DFB8]',
            self::P3 => 'bg-[#F5F0EB] text-[#8B7355] border-[#E8DDD4]',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::P1 => 'fire',
            self::P2 => 'bolt',
            self::P3 => 'leaf',
        };
    }
}
