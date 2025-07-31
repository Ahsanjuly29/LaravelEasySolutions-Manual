<?php

namespace App\Actions\TaskApi;

use App\Models\Task;

class FilterTasks
{
    public function handle($request, $dataAmount)
    {
        // there is not administration Control. So, Each person can see only his/her own tasks.
        $taskData = Task::owner();

        if (! empty($request->status)) {
            $taskData->where('status', $request->status);
        }

        if (! empty($request->searchName)) {
            $taskData->where('name', 'like', '%' . $request->searchName . '%');
        }

        return $taskData->orderBy('due_date', 'ASC')->paginate($dataAmount);
    }
}
