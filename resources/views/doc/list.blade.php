@extends('layouts.app')

@section('title', 'DOCLIST')

@section('sidebar')
    @parent
    <h3>DOcList.</h3>
@endsection
@section('content')
    List 영역 doc_list_arr
    @foreach ($doc_list_arr as $info)
    <p>This is user {{ $info['id'] }}</p>
    @endforeach
@endsection
