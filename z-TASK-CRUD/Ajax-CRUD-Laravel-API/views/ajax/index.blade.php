@extends('master.app')
@section('custom-css')
@endsection

@section('main-body')
    <div class="container">
        <div class="card">
            <div class="card-header">Manage Tasks</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th class="text-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="" id="check_all_box" />
                                    </div>
                                </th>
                                <th>Id</th>
                                <th>Status</th>
                                <th>Name</th>
                                <th>Assigned To</th>
                                <th>Created By</th>
                                <th>Due</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tasks as $task)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input checkitem" type="checkbox"
                                                value="{{ $task->id }}" name="id" />
                                        </div>
                                    </td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $task->status ?? '--' }}</td>
                                    <td>
                                        {{ $task->name ?? '--' }}
                                    </td>
                                    <td>
                                        {{ $task->assignedTo->name ?? '--' }}
                                    </td>
                                    <td>
                                        {{ $task->createdBy->name ?? '--' }}
                                    </td>
                                    <td>
                                        {{ date('D Y-m-d H:i', strtotime($task->due_date)) ?? '--' }}
                                    </td>
                                    <td>
                                        {{ date('D Y-m-d H:i', strtotime($task->created_at)) ?? '--' }}
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-sm btn-outline-primary me-1 edit-task"
                                                data-url="{{ route('api-task.edit', $task->id) }}"> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger ms-1 delete-btn"
                                                data-url="{{ route('api-task.destroy', $task->id) }}"
                                                data-id="{{ $task->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="13">
                                    <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none"
                                        type="submit" data-url="{{ route('api-task.destroy', 1) }}">
                                        Delete all
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="100">
                                    {!! $tasks->render() !!}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
            success = function(data) {
                if (data.message == 'Open Modal') {
                    openModal(data);
                } else {
                    toastr.success(data.message);
                    closeModal();
                }
            }
        });
    </script>
@endsection
