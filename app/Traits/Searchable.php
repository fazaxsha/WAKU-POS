<?php
// app/Traits/Searchable.php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    /**
     * Apply search & filter dari request ke query builder.
     *
     * @param Builder $query
     * @param Request $request
     * @param array   $searchColumns  kolom yang di-search (ilike)
     * @param array   $filterColumns  ['request_key' => 'column_name']
     * @param array   $dateRangeColumns ['from_key' => 'column', 'to_key' => 'column']
     */
    protected function applySearch(
        Builder $query,
        Request $request,
        array $searchColumns = [],
        array $filterColumns = [],
        array $dateRange = []
    ): Builder {
        // Full-text search across columns
        if ($request->filled('search') && !empty($searchColumns)) {
            $term = $request->input('search');
            $query->where(function (Builder $q) use ($term, $searchColumns) {
                foreach ($searchColumns as $col) {
                    $q->orWhere($col, 'ilike', '%' . $term . '%');
                }
            });
        }

        // Exact / select filters
        foreach ($filterColumns as $requestKey => $column) {
            if ($request->filled($requestKey)) {
                $query->where($column, $request->input($requestKey));
            }
        }

        // Date range filter
        if (!empty($dateRange)) {
            $fromKey = $dateRange['from_key'] ?? 'from';
            $toKey   = $dateRange['to_key']   ?? 'to';
            $column  = $dateRange['column']   ?? 'created_at';

            if ($request->filled($fromKey)) {
                $query->whereDate($column, '>=', $request->input($fromKey));
            }
            if ($request->filled($toKey)) {
                $query->whereDate($column, '<=', $request->input($toKey));
            }
        }

        return $query;
    }

    /**
     * Kembalian standar per-page (bisa di-override per controller)
     */
    protected function perPage(Request $request, int $default = 15): int
    {
        $perPage = (int) $request->input('per_page', $default);
        return in_array($perPage, [10, 15, 25, 50, 100]) ? $perPage : $default;
    }
}