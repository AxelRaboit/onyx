<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Collection;

class DevDashboardService
{
    public function getStats(): array
    {
        $now = now();
        $months = collect(range(11, 0))->map(fn ($i) => $now->copy()->subMonths($i));

        $usersRaw = $this->monthlyCount(User::class, $months->first()->startOfMonth());
        $notesRaw = $this->monthlyCount(Note::class, $months->first()->startOfMonth());

        return [
            'users' => [
                'total' => User::count(),
                'newThisMonth' => User::whereYear('created_at', $now->year)
                    ->whereMonth('created_at', $now->month)
                    ->count(),
            ],
            'notes' => Note::count(),
            'charts' => [
                'labels' => $months->map(fn ($m) => $m->format('M Y'))->values(),
                'usersPerMonth' => $months->map(fn ($m) => $usersRaw[$m->format('Y-m')] ?? 0)->values(),
                'notesPerMonth' => $months->map(fn ($m) => $notesRaw[$m->format('Y-m')] ?? 0)->values(),
            ],
        ];
    }

    private function monthlyCount(string $model, mixed $from): Collection
    {
        return $model::selectRaw("TO_CHAR(created_at, 'YYYY-MM') as ym, COUNT(*) as total")
            ->where('created_at', '>=', $from)
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
            ->pluck('total', 'ym');
    }
}
