<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Admin - Login</title>

    <link href="{{ asset('admin/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/font-awesome/css/font-awesome.css') }}" rel="stylesheet">

    <link href="{{ asset('admin/css/animate.css') }}" rel="stylesheet">
    <link href="{{ asset('admin/css/style.css') }}" rel="stylesheet">

</head>

<body class="gray-bg">
  <div class="container">
    <div class="">
      <div class="col-lg-4 col-lg-offset-4 animated fadeInDown">
          <div>
              <div class="text-center">
                  <h1 class="logo-name"><img src="{{ asset('img/acqio-login.png') }}" alt="" /></h1>
              </div>
              <h3 class="text-center">Financeiro Acqio</h3>
              <p class="text-center">Plataforma de gestão Financeiro Acqio.</p>
              <div class="row">
                @if(session('erro'))
                <div class="alert alert-{{ session('class') }}">
                  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                  {{ session('erro') }}
                </div>
                @endif
              </div>
              <form class="form-horizontal" role="form" method="POST" action="{{ url('/logar') }}">
                  {{ csrf_field() }}
                  <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                      <label for="">E-MAIL</label>
                      <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}">
                      @if ($errors->has('email'))
                          <span class="help-block">
                              <strong>{{ $errors->first('email') }}</strong>
                          </span>
                      @endif
                  </div>
                  <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                      <label for="">SENHA</label>
                      <input id="password" type="password" class="form-control" name="password">
                      @if ($errors->has('password'))
                          <span class="help-block">
                              <strong>{{ $errors->first('password') }}</strong>
                          </span>
                      @endif
                  </div>
                  <div class="form-group">
                      <button type="submit" class="btn btn-primary block full-width m-b">Login</button>
                  </div>
                  <div class="text-center">
                      <a href="#" ><small>Esqueceu sua senha?</small></a>
                  </div>
              </form>
          </div>
      </div>
    </div>
  </div>

    <!-- Mainly scripts -->
    <script src="{{ asset('admin/js/jquery-2.1.1.js') }}"></script>
    <script src="{{ asset('admin/js/bootstrap.min.js') }}"></script>

</body>

</html>
