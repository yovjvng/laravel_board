<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Boards;

class ApiListController extends Controller
{
    function getlist($id) {
        $board = Boards::find($id);
        return response()->json($board, 200);
    }

    function postlist(Request $req) {
        // 유효성 체크 필요

        $boards = new Boards([
            'title' => $req->title
            ,'content' => $req->content
        ]);
        $boards->save();

        $arr['errorcode'] = '0';
        $arr['msg'] = 'success';
        $arr['data'] = $boards->only('id','title');

        return $arr;
        // return response()->json($boards, 200);
    }

    function updatelist(Request $req, $id) {
        // 유효성 검사
        $arr = ['id' => $id];
        $request->merge($arr);
        $request->request->add($arr);


        $boards = Boards::findOrFail($id);
        $boards->title = $request->title;
        $boards->content = $request->content;
        $boards->save();


        return response()->json($boards, ['board' => $id]);
    }
}
