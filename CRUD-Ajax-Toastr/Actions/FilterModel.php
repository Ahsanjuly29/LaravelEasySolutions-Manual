<?php

namespace App\Actions\ModelApi;

class FilterModel
{
    public function handle($request, $dataAmount)
    {
        set_time_limit(60); // Ensure the process doesn't timeout

        $taskData = ModelName::query();
        if (! empty($request->status)) {
            $taskData->where('status', $request->status);
        }
        if (! empty($request->searchName)) {
            $taskData->where('name', 'like', '%' . $request->searchName . '%');
        }

        return $taskData->orderBy('due_date', 'ASC')->paginate($dataAmount);
    }
}
