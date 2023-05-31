<h2>Header</h2>

{{-- 로그인 중 --}}
@auth
    <div><a href="{{route('users.logout')}}">LOGOUT</a></div>
    <div><a href="{{route('users.useredit')}}">회원정보수정</a></div>
@endauth

{{-- 비로그인 상태 --}}
@guest
    <div><a href="{{'users.login'}}">LOGIN</a></div>
@endguest
<hr>