@extends('layouts.app')

@section('title', '메인페이지')

@section('sidebar')
    @parent
    @section('sidebar_add')
    <a class="btn_o_m" href="/api/logout" >로그아웃</a>
    @endsection
@endsection
@section('content')
<p>ID: {{ $id }}</p>
<p>token: {{ $token }}</p>
<p>iss: {{ $token_data->iss }}</p>
<p>aud: {{ $token_data->aud }}</p>
<p>iat: {{ $token_data->iat }}</p>
<p>exp: {{ $token_data->exp }}</p>
@endsection
