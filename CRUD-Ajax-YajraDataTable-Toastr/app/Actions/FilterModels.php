<?php

namespace App\Actions\ModelApi;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;

class FilterModels
{
    /**
     * Handle DataTable search, pagination, and response formatting.
     *
     * @param Request $request
     * @param Builder $query
     * @param Model $model
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Builder $query, Model $model)
    {
        set_time_limit(60); // Ensure the process doesn't timeout

        try {
            // Apply global search across fillable fields
            $search = $request->input('search.value');
            if (!empty($search)) {
                $columns = $model->getFillable();
                $query->where(function ($q) use ($columns, $search) {
                    foreach ($columns as $column) {
                        $q->orWhere($column, 'like', "%{$search}%");
                    }
                });
            }

            $perPage = $request->input('length', 10);
            if ($perPage == -1) {
                // In case `-1` is passed, return the full count (or cached)
                $perPage = $query->count();
            }

            // Keyset Pagination
            $lastId = $request->input('last_id'); // new param for keyset pagination

            if ($lastId) {
                // Use keyset pagination: fetch rows with id > last_id ordered by id asc
                $query->where('id', '>', $lastId)
                    ->orderBy('id', 'asc');

                $results = $query->limit($perPage)->get();

                // We can’t paginate with ->paginate() here, so:
                return response()->json([
                    'draw' => intval($request->input('draw')),
                    'recordsTotal' => Cache::remember('company_total', 300, fn() => $model->newQuery()->count()), // Cache count for 5 mins
                    'recordsFiltered' => $query->count(), // filtered count after search + last_id filter
                    'data' => $results,
                    'last_id' => $results->last()?->id, // for next batch keyset param
                ]);
            }

            // Fallback to Offset pagination as before
            $start = intval($request->input('start', 0));
            $page = floor($start / $perPage) + 1;

            $results = $query->paginate($perPage, ['*'], 'page', $page);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => Cache::remember('company_total', 300, fn() => $model->newQuery()->count()), // Cache count for 5 mins
                'recordsFiltered' => $results->total(),
                'data' => $results->items(),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'Server error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
