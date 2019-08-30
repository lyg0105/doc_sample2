@extends('layouts.app')

@section('title', 'DOCLIST')

@section('sidebar')
    @parent
    
@endsection

@section('content')
    List 영역
    @foreach ($doc_list_arr as $info)
    <p>This is user {{ $info['id'] }}</p>
    @endforeach
@endsection
