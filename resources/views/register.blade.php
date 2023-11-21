
@extends('layout/layout-common')

@section('space-work')

<h1>
    Welcome to registeration
</h1>
@if($errors->any())
@foreach($errors->all() as $error)
<p style="color:red;">{{$error}}</p>

@endforeach
@endif

    @if(Session::has('success'))
        <div class="alert alert-success" role="alert">
            <strong>{{Session::get('success')}}</strong>
        </div>
    @endif

<form action="{{url('/register')}}" method="post">
@csrf
<input type="text" class="form-control" name="name" placeholder="Enter name"><br><br>

<input type="text" class="form-control" name="email" placeholder="Enter email"><br><br>

<input type="password" class="form-control" name="password" placeholder="Enter password"><br><br>

<input type="password" class="form-control" name="password_confirmation" placeholder="Confirm password"><br><br>

<input class="btn btn-warning" type="submit" name="register" >
</form>



@endsection

