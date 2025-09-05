@extends('admin.master.app')

@section('custom-css')
@endsection


@section('main-content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-10 md-offset-2 col-sm-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="dataTable"
                            data-url="{{ route('api-company-data.index') }}" data-columns='["name", "mobile", "address"]'>
                            <thead>
                                <tr>
                                    <th colspan="2">
                                        <button type="button" class="btn btn-sm btn-outline-primary create-btn w-100"
                                            data-url="{{ route('api-company-data.store') }}" data-toggle="modal"
                                            data-target="form-modal">
                                            <i class="fas fa-plus"></i>
                                            Create
                                        </button>
                                    </th>
                                    <th colspan="2">
                                        <button class="btn btn-outline-success mr-1" onclick="exportToExcel()">
                                            <i class="fas fa-download"></i> Excel
                                        </button>
                                        <button class="btn btn-outline-danger mx-1" onclick="exportToPDF()">
                                            <i class="fas fa-download"></i> PDF
                                        </button>
                                        <button class="btn btn-outline-primary ml-1" onclick="exportToWord()">
                                            <i class="fas fa-download"></i> Word
                                        </button>
                                    </th>
                                    <th>
                                        <input class="form-control" type="search" placeholder="Search" id="searchInput">
                                    </th>
                                </tr>
                                <tr>
                                    <th class="text-center" style="vertical-align: top;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value=""
                                                id="check_all_box" />
                                        </div>
                                    </th>
                                    <th>Name</th>
                                    <th>Mobile</th>
                                    <th>Address</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Rows will be loaded via AJAX -->
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="100" class="text-center">
                                        <div id="pagination-wrapper"></div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>

                        <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none" type="submit"
                            data-url="{{ route('api-company-data.destroy', 0) }}">
                            Delete all
                        </button>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="border border-white rounded h-100 p-2">
                    </div>
                </div>
            </div>
        </div>
    </section>

    @include('admin.company.form-modal')
@endsection

@section('custom-js')
@endsection
