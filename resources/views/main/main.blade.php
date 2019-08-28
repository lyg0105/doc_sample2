<h1>메인페이지</h1>
<p>ID: {{ $id }}</p>
<p>token: {{ $token }}</p>
<p>iss: {{ $token_data->iss }}</p>
<p>aud: {{ $token_data->aud }}</p>
<p>iat: {{ $token_data->iat }}</p>
<p>exp: {{ $token_data->exp }}</p>
<a href="/api/logout" >로그아웃</a>
