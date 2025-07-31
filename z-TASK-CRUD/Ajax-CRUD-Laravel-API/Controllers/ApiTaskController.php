<?php

namespace App\Http\Controllers\ApiAuth;

use App\Actions\TaskApi\FilterTasks;
use App\Http\Controllers\Controller;
use App\Http\Requests\TaskRequest;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiTaskController extends Controller
{
    /**
     * Display all listing of task.
     *
     * @response
     */
    public function index(Request $request, FilterTasks $filterTasks)
    {
        try {
            $data = $filterTasks->handle($request, 100); // dataAmount

            return successResponse('Showing All Tasks', $data);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Store new task.
     */
    public function store(TaskRequest $request)
    {
        try {
            $data = $request->validated();
            $data['created_by'] = $data['assigned_to'] = Auth::user()->id;

            $taskData = Task::create($data);

            return successResponse('New Task has been Created', $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Display the specified task.
     */
    public function show($id)
    {
        try {
            $taskData = Task::owner()->find($id);
            if (empty($taskData)) {
                throw new \Exception('Unable to Find This Task');
            }

            $taskData['url'] = route('api-task.update', $id);
            $taskData['due_date'] = date('Y-m-d', strtotime($taskData->due_date));
            $taskData->unsetRelation('assignedTo')->unsetRelation('createdBy');

            // Removing Unnecessary Data
            unset($taskData->created_by);
            unset($taskData->assigned_to);

            return successResponse('Open Modal', $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    public function edit($id)
    {
        return $this->show($id);
    }

    /**
     * Update task in DB.
     */
    public function update(TaskRequest $request, string $id)
    {
        try {
            $taskData = Task::owner()->find($id);
            if (empty($taskData)) {
                throw new \Exception('Unable to Find This Task');
            }

            $taskData->update($request->validated());

            return successResponse('This Task has been Updated', $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Remove the Task from storage.
     */
    public function destroy(Request $request)
    {
        try {
            Task::owner()->whereIn('id', explode(',', $request->ids))
                ->delete();
            return successResponse('This Task has been Destroyed');
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }



    /**
     * Update task Status.
     */
    public function changeStatus(Request $request)
    {
        try {
            $taskData = Task::owner()->find($request->id);
            if (empty($taskData)) {
                throw new \Exception('Unable to Find This Task');
            }

            $taskData->update([
                'status' => $request->status,
            ]);

            return successResponse('Status updated to ' . $request->status, $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }

    /**
     * Update Due Date Of task.
     */
    public function changeDueDate(Request $request)
    {
        try {
            $taskData = Task::owner()->find($request->id);
            if (empty($taskData)) {
                throw new \Exception('Unable to Find This Task');
            }

            $taskData->update([
                'due_date' => $request->due_date,
            ]);

            return successResponse('Due date updated to ' . $request->due_date, $taskData);
        } catch (\Exception $e) {
            return errorResponse($e);
        }
    }
}
