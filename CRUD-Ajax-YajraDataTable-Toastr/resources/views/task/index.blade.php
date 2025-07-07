@extends('master.app')
@section('custom-css')
@endsection

@section('main-body')
    <div class="container">
        <div class="card">
            <div class="card-header">Manage Tasks</div>
            <div class="card-body">
                {{ $dataTable->table(['class' => 'table table-hover table-borderless table-striped']) }}
            </div>
        </div>
    </div>
@endsection

@section('custom-js')
    {{ $dataTable->scripts(attributes: ['type' => 'module']) }}
@endsection
