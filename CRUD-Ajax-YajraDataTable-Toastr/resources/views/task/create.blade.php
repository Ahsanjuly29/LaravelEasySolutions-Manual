@extends('master.app')
@section('custom-css')
@endsection

@section('main-body')
    <div class="container">
        <div class="card">
            <div class="card-header">Create Tasks</div>
            <div class="card-body">
                <form id="form">
                    <div class="row">
                        <div class="col-sm-4 mt-1 ">
                            <input type="text" class="form-control" placeholder="Enter name" name="name">
                        </div>
                        <div class="col-sm-4 mt-1 ">
                            <input type="text" class="form-control" placeholder="Enter description" name="description">
                        </div>
                        <div class="col-sm-4 mt-1 ">
                            <input type="date" class="form-control" placeholder="Enter due_date" name="due_date">
                        </div>
                        <div class="col-sm-4 mt-1 ">
                            <select class="form-select" name="status" id="">
                                <option value="PENDING">PENDING</option>
                                <option value="IN_PROGRESS">IN PROGRESS</option>
                                <option value="COMPLETED">COMPLETED</option>
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
                    type: 'POST',
                    bearer: "{{ session('loginToken' . auth()->user()->id) }}",
                    url: "{{ route('api-task.store') }}",
                    dataType: 'JSON',
                    data: $('#form').serialize(),
                    time: 3000
                }
                ajaxCall(param); // Submit form Using Ajax
            });

            success = function(data) {
                // console.log(data);
                toastr.success(data.data.name + "has been added successfully");
                $('#form')[0].reset();
            }
        });
    </script>
@endsection
