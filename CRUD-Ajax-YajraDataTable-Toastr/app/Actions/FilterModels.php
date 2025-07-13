<?php

namespace App\Actions\ModelApi;

use App\Models\ModelName;

class FilterModels
{
    public function handle($request, $dataAmount)
    {
        // there is not administration Control. So, Each person can see only his/her own tasks.
        // $taskData = ModelName::owner(); // calling Scope of res
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
