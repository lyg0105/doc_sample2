<html>
    <head>
        <title>App Name - @yield('title')</title>
        @component('layouts.top_link')
            @yield('top_link_add')
        @endcomponent
    </head>
    <body>
        <div class="body_wrap">
            <div id="header">
            @section('sidebar')
                @component('layouts.sidebar')
                    @yield('sidebar_add')
                @endcomponent
            @show
            </div>

            <div id="contents">
                @yield('content')
            </div>
        </div>
    </body>
</html>
