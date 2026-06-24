<?php

namespace App\Services;

use App\Contracts\ContentCollectionModel;
use App\Models\Analytics;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

final class AnalyticsService
{
    /**
     * The subject class to scope every query to, or null for site-wide stats.
     *
     * @var class-string<ContentCollectionModel>|null
     */
    private ?string $subjectType;

    public function __construct(?string $collectionSlug = null)
    {
        $this->subjectType = $collectionSlug !== null
            ? $this->resolveSubjectType($collectionSlug)
            : null;
    }

    /**
     * Total number of recorded visits.
     */
    public function totalVisits(): int
    {
        return $this->query()->count();
    }

    /**
     * Number of visits recorded within the last given number of days.
     */
    public function visitsInLastDays(int $days): int
    {
        return $this->query()
            ->where('created_at', '>=', $this->toDate($days))
            ->count();
    }

    /**
     * Daily visit counts for a window of the given number of days, zero-filled
     * and keyed by ISO date so the series is gap-free for charts.
     *
     * By default the window ends today. Pass $endDaysAgo to shift it back —
     * for example, $endDaysAgo equal to $days yields the immediately preceding
     * period, for period-over-period comparisons.
     *
     * @return array<string, int>
     */
    public function dailyVisits(int $days, int $endDaysAgo = 0): array
    {
        $end = Carbon::today()->subDays($endDaysAgo);
        $start = $end->copy()->subDays($days - 1);

        $counts = $this->query()
            ->whereBetween('created_at', [$start->startOfDay(), $end->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as date, COUNT(*) as aggregate')
            ->groupByRaw('DATE(created_at)')
            ->pluck('aggregate', 'date');

        $series = [];

        for ($offset = $days - 1; $offset >= 0; $offset--) {
            $date = $end->copy()->subDays($offset)->toDateString();
            $series[$date] = (int) ($counts[$date] ?? 0);
        }

        return $series;
    }

    /**
     * The content item with the most recorded visits, if any.
     */
    public function mostVisitedSubject(): ?ContentCollectionModel
    {
        $candidates = $this->query()
            ->whereNotNull('subject_id')
            ->select('subject_type', 'subject_id')
            ->selectRaw('COUNT(*) as aggregate')
            ->groupBy('subject_type', 'subject_id')
            ->orderByDesc('aggregate')
            ->get();

        foreach ($candidates as $candidate) {
            $subject = $candidate->subject;

            if ($subject instanceof ContentCollectionModel) {
                return $subject;
            }
        }

        return null;
    }

    /**
     * Total number of content items. Scoped to the current collection when one
     * is set, otherwise summed across every enabled collection registered in
     * config/content-collections.php.
     */
    public function totalContentItems(): int
    {
        if ($this->subjectType !== null) {
            return $this->subjectType::query()->count();
        }

        /** @var array<string, class-string> $collections */
        $collections = config('content-collections.collections', []);

        /** @var array<int, string> $disabled */
        $disabled = config('content-collections.disabled', []);

        $total = 0;

        foreach ($collections as $slug => $class) {
            if (in_array($slug, $disabled, true)) {
                continue;
            }

            if (! is_a($class, ContentCollectionModel::class, true)) {
                continue;
            }

            $total += $class::query()->count();
        }

        return $total;
    }

    /**
     * A fresh analytics query, constrained to the current collection scope.
     *
     * @return Builder<Analytics>
     */
    private function query(): Builder
    {
        return Analytics::query()
            ->when(
                $this->subjectType,
                fn (Builder $query): Builder => $query->where('subject_type', $this->subjectType),
            );
    }

    /**
     * Resolve a collection slug to its subject model class, or null when the
     * slug is not a known content collection.
     *
     * @return class-string<ContentCollectionModel>|null
     */
    private function resolveSubjectType(string $collectionSlug): ?string
    {
        /** @var array<string, class-string> $collections */
        $collections = config('content-collections.collections', []);

        $class = $collections[$collectionSlug] ?? null;

        return is_string($class) && is_a($class, ContentCollectionModel::class, true)
            ? $class
            : null;
    }

    private function toDate(int $days): Carbon
    {
        return Carbon::today()->subDays($days - 1);
    }
}
