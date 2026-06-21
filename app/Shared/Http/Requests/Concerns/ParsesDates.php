<?php

namespace App\Shared\Http\Requests\Concerns;

use App\Shared\Calendar\JalaliDateService;
use Carbon\Carbon;

trait ParsesDates
{
    protected function parseDateInput(?string $gregorian, ?string $jalali): ?Carbon
    {
        if ($gregorian) {
            return Carbon::parse($gregorian);
        }
        if ($jalali) {
            return app(JalaliDateService::class)->parse($jalali);
        }

        return null;
    }

    protected function parseJalaliFromRequest(string $prefix): ?Carbon
    {
        if ($combined = $this->input($prefix.'_jalali')) {
            return app(JalaliDateService::class)->parse($combined);
        }
        $y = $this->input($prefix.'_jalali_year');
        $m = $this->input($prefix.'_jalali_month');
        $d = $this->input($prefix.'_jalali_day');
        if ($y && $m && $d) {
            return app(JalaliDateService::class)->parse("{$y}/{$m}/{$d}");
        }

        return null;
    }

    protected function resolvedDate(string $prefix): ?Carbon
    {
        $jalaliService = app(JalaliDateService::class);

        if ($combined = $this->input($prefix.'_jalali')) {
            return $jalaliService->parse($combined)->startOfDay();
        }

        if ($jalali = $this->parseJalaliFromRequest($prefix)) {
            return $jalali->startOfDay();
        }

        if ($gregorian = $this->input($prefix)) {
            return Carbon::parse($gregorian)->startOfDay();
        }

        return null;
    }

    protected function requireResolvedDate($validator, string $prefix, string $label): void
    {
        $validator->after(function ($validator) use ($prefix, $label) {
            if (! $this->resolvedDate($prefix)) {
                $validator->errors()->add($prefix, "The {$label} field is required.");
            }
        });
    }
}
