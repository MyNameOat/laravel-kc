<!DOCTYPE html>
<html>

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>KC SERVICE | Login</title>

    <link href="/css/bootstrap.min.css" rel="stylesheet">
    <link href="/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="/css/animate.css" rel="stylesheet">
    <link href="/css/style.css" rel="stylesheet">



</head>

<body class="gray-bg">

<div class="middle-box text-center loginscreen animated fadeInDown">
    <div>
        <div>

            <h1 class="logo-name">KC</h1>

        </div>
        <p>เข้าสู่ระบบบริหารจัดการบริษัท KC METALSHEET</p>
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group {{ $errors->has('username') ? ' has-error' : '' }}">
                <input type="text" name="username" value="{{ old('username') }}" class="form-control" placeholder="ชื่อผู้ใช้งาน">
                <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                      </span>
            </div>
            <div class="form-group {{ $errors->has('password') ? ' has-error' : '' }}">
                <input name="password" type="password" class="form-control" placeholder="รหัสผ่าน">
                <span class="help-block">
                        <strong>{{ $errors->first('username') }}</strong>
                      </span>
            </div>
            <button type="submit" class="btn btn-primary block full-width m-b">เข้าสู่ระบบ</button>
        </form>
    </div>
</div>

<!-- Mainly scripts -->
<script src="/js/jquery-2.1.1.js"></script>
<script src="/js/bootstrap.min.js"></script>

</body>

</html>
