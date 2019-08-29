@extends('layouts.app')

@section('title', '메인페이지')

@section('sidebar')
    @parent

    <p>This is appended to the master sidebar.</p>
@endsection
@section('content')
<p>ID: {{ $id }}</p>
<p>token: {{ $token }}</p>
<p>iss: {{ $token_data->iss }}</p>
<p>aud: {{ $token_data->aud }}</p>
<p>iat: {{ $token_data->iat }}</p>
<p>exp: {{ $token_data->exp }}</p>
<p><a href="/doc/list" >Doc리스트</a></p>
<p><a href="/api/logout" >로그아웃</a></p>
@endsection
