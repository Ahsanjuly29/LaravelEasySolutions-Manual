@extends('master.app')
@section('custom-css')
@endsection

@section('main-body')
    <div class="container">
        <div class="card">
            <div class="card-header">Edit Tasks</div>
            <div class="card-body">
                <form id="form">
                    @method('PUT')
                    <div class="row">
                        <div class="col-sm-4 mt-1 ">
                            <input type="text" class="form-control" value="{{ $data->name ?? '' }}" placeholder="Enter Name"
                                name="name">
                        </div>
                        <div class="col-sm-4 mt-1 ">
                            <input type="text" class="form-control" value="{{ $data->description ?? '' }}"
                                placeholder="Enter Description" name="description">
                        </div>
                        <div class="col-sm-4 mt-1 ">
                            <input type="date" name="due_date" class="form-control"
                                value="{{ !empty($data->due_date) ? date('Y-m-d', strtotime($data->due_date)) : '' }}"
                                placeholder="Enter due Date">
                        </div>
                        <div class="col-sm-4 mt-1 ">
                            <select class="form-select" name="status" id="status">
                                <option value="PENDING" {{ $data->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                <option value="IN_PROGRESS" {{ $data->status == 'IN_PROGRESS' ? 'selected' : '' }}>IN
                                    PROGRESS</option>
                                <option value="COMPLETED" {{ $data->status == 'COMPLETED' ? 'selected' : '' }}>COMPLETED
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-2 mt-1">
                            <button class="btn btn-sm btn-primary form-control" id="submitBtn">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    <script type="text/javascript">
        $(document).ready(function() {
            $('#submitBtn').click(function(event) {
                event.preventDefault();

                var param = {
                    type: 'PUT',
                    bearer: "{{ session('loginToken' . auth()->user()->id) }}",
                    url: "{{ route('api-task.update', $data->id) }}",
                    dataType: 'JSON',
                    data: $('#form').serialize(),
                    time: 3000
                }

                ajaxCall(param); // Submit form Using Ajax
            });

            success = function(data) {
                toastr.success(data.data.name + "has been added successfully");
                window.location.replace('/task');
            }
        });
    </script>
@endsection
