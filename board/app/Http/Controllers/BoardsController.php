<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Boards;

class BoardsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $result = Boards::all();
        $result = Boards::select(['id','title', 'hits', 'created_at', 'updated_at'])->orderBy('hits', 'desc')->get();
        return view('list')->with('data', $result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('write');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $req
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {
        $boards = new Boards([ // insert문인 새로운 객체를 생성하는 것이기 때문에 new()를 쓴다.
            'title' => $req->input('title')
            ,'content' => $req->input('content')
        ]);
        $boards->save();
        return redirect('/boards');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $boards = Boards::find($id); // 기존 값 가지고 온다.
        $boards->hits++; // 조회수 올려준다.
        $boards->save(); // 업데이트 처리
        // save()는 insert를 먼저 실행한뒤 실패하면 update 실행

        return view('/detail')->with('data', Boards::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $boards = Boards::findOrFail($id);
        return view('/edit')->with('data', $boards);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // URL에 항당되는 view가 없으면 view로 이동, 없으면 redirect
        $boards = Boards::find($id);
        $boards->title = $request->title;
        $boards->content = $request->content;
        $boards->save();

        // return view('/detail')->with('data', Boards::findOrFail($id));

        // return redirect('/boards/'.$id);

        return redirect()->route('boards.show', ['board' => $id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Boards::findOrFail($id)->delete();
        // Boards::destroy($id);

        // boards::destroy($id);
        return redirect('/boards');
    }
}
