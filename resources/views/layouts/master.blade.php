<!DOCTYPE html>
<html>

<head>
  <title>@yield('title')</title>

  @stack('links')

</head>

<body>
  <div id="wrapper">
    @include('layouts.menu')

    <div id="page-wrapper" class="gray-bg">
      @include('layouts.top')
      <div class="wrapper wrapper-content">
        @if(session('msg'))
        <div class="alert alert-{{ session('class') }}">
          <b>{{ session('msg') }}</b>
        </div>
        @endif

        @yield('content')

      </div>
      @include('layouts.footer')
    </div>
    @include('layouts.right-side-bar')
  </div>

  @stack('scripts')
</body>
</html>
