@extends('layout.layout')

@section('title', 'useredit')

@section('contents')
    <h1>UserEdit</h1>
    @include('layout.errorsvalidate')
    <div>{!!session()->has('message') ? session('message') : ""!!}</div>
    <form action="{{route('users.useredit.post')}}" method="post">
        @csrf
        <label for="name">name : </label>
        <input type="text" name="name" id="name" value="{{Auth::user()->name}}" readonly>
        <br>
        <label for="email">Email : </label>
        <input type="text" name="email" id="email" value="{{Auth::us                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  er()->email}}" readonly>
        <br>
        <label for="password">password : </label>
        <input type="password" name="password" id="password">
        <br>
        <label for="password_confirmation">passwordcheck : </label>
        <input type="password" name="password_confirmation" id="password_confirmation">
        <br><br>
        <button type="submit">수정완료</button>
        <button type="button" onclick="location.href = '{{route('boards.index')}}'">Cancel</button>
    </form>
@endsection