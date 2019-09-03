@extends('layouts.app')

@section('title', 'DOCLIST')

@section('sidebar')
    @parent
@endsection
@section('content')
    Write 영역
    @component('x_templete.crud.write.v1.write.write')
        <?php include (str_replace('\\','/',base_path())."/resources/views/doc/write/write_js.blade.php"); ?>
    @endcomponent
@endsection
