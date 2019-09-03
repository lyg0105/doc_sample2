@extends('layouts.app')

@section('title', 'DOCLIST')

@section('sidebar')
    @parent

@endsection

@section('content')
    @component('x_templete.crud.grid.v1.list')
        <h3>리스트</h3>
        <?php include (str_replace('\\','/',base_path())."/resources/views/doc/list/list_js.php"); ?>
    @endcomponent
@endsection
