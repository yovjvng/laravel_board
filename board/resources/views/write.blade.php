<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Write</title>
</head>
<body>
    @include('layout.errorsvalidate')

    <form action="{{route('boards.store')}}" method="post">
        @csrf
        <label for="title">제목 : </label>
        <input type="text" name="title" id="title" value="{{old('title')}}">{{-- old() 기존 입력값이 남는 설정 --}} 
        <br>
        <label for="content">제목 : </label>
        <textarea name="content" id="content">{{old('content')}}</textarea>
        <br>
        <button type="submit">작성</button>
    </form>
</body>
</html>