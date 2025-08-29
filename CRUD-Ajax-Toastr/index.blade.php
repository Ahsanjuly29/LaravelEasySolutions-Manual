@extends('admin.master.app')

@section('custom-css')
@endsection

@section('main-content')
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">

            @include('admin.company.form-modal')
            <div class="row">
                <div class="col-sm-8">
                    <button type="button" class="nav-link border border-primary w-100 btn btn-primary create-btn"
                        data-url="{{ route('api-company-data.store') }}" data-toggle="modal" data-target="form-modal">
                        Create
                    </button>

                    <table class="table table-striped" data-url="{{ route('api-company-data.index') }}">
                        <thead>
                            <th class="text-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="" id="check_all_box" />
                                </div>
                            </th>
                            <th>Name</th>
                            <th>Mobile</th>
                            <th>
                                Address
                            </th>
                            <th>Action</th>
                        </thead>

                        <tbody>
                            @foreach ($allData as $item)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <input class="form-check-input checkitem" type="checkbox"
                                                value="{{ $item->id }}" name="id" />
                                        </div>
                                    </td>
                                    <td>{{ $item->name ?? '' }}</td>
                                    <td>{{ $item->mobile ?? '' }}</td>
                                    <td>
                                        <address>{{ $item->address ?? '' }}</address>
                                    </td>
                                    <td>
                                        <div class="d-flex">
                                            <button class="btn btn-sm btn-outline-primary mr-1 edit-btn"
                                                data-url="{{ route('api-company-data.edit', $item->id) }}"> Edit
                                            </button>
                                            <button class="btn btn-sm btn-outline-danger ml-1 delete-btn"
                                                data-url="{{ route('api-company-data.destroy', $item->id) }}"
                                                data-id="{{ $item->id }}">
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="13">
                                    <button id="multiple_delete_btn" class="btn btn-xs btn-outline-danger mr-2 d-none"
                                        type="submit" data-url="{{ route('api-company-data.destroy', 0) }}">
                                        Delete all
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="100">
                                    {!! $allData->render() !!}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('custom-js')
@endsection
