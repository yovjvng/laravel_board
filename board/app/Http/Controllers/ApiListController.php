<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Boards;


class ApiListController extends Controller
{
    function getlist($id) {
        $board = Boards::find($id);
        return response()->json($board, 200);
    }

    function postlist(Request $req) {
        // 유효성 체크 필요

        // $messages = [
        //     'title.required' => '제목은 필수 입력 항목입니다.',
        //     'content.required' => '본문은 필수 입력 항목입니다.',
        //     'content.min' => '본문은 최소 :min 글자 이상이 필요합니다.',
        // ];
    
        // $validator = Validator::make($req->all(), $rules, $messages);


        $boards = new Boards([
            'title' => $req->title
            ,'content' => $req->content
        ]);
        // 유효성 체크
        $validator = Validator::make($req->only('title','content'), [
            'title' => 'required|between:3,30'
            ,'content' => 'required|max:1000' 
        ]);

        if ($validator->fails()) {    
            return response()->json($validator->messages(), 200);
        }

        $boards->save();

        $arr['errorcode'] = '0';
        $arr['msg'] = 'success';
        $arr['data'] = $boards->only('id','title');

        return $arr;
        // return response()->json($boards, 200);
    }

    function putlist(Request $req, $id) {
        $arrData = [
            'code' => '0'
            ,'msg' => ''
        ];
        
        $data = $req->only('title', 'content');
        $data['id'] = $id;

        // 유효성 검사
        $validator = Validator::make($data, [
            'id' => 'required|integer|exists:boards'
            ,'title' => 'required|between:3,30'
            ,'content' => 'required|max:2000'
        ]);

        if ($validator->fails()) {
            $arrData['code'] = 'E01';
            $arrData['msg'] = 'Validate Error';
            $arrData['errmsg'] = $validator->errors()->all();
            return $arrData;
        } else {
        // 업데이트 처리
            $boards = Boards::findOrFail($id);
            $boards->title = $req->title; // 기존 데이트를 리퀘스트 타이틀로 바꿔준다.
            $boards->content = $req->content;
            $boards->save();
            $arrData['code'] = '0';
            $arrData['msg'] = 'success';
        }

        return $arrData;
    }

    function deletelist($id) {
        $arrData = [
            'code' => '0'
            ,'msg' => ''
        ];
        $data['id'] = $id;
        $validator = Validator::make($data, [
            'id' => 'required|integer|exists:boards,id'
        ]);
        
        if ($validator->fails()) {
            $arrData['code'] = 'E01';
            $arrData['msg'] = 'Error';
            $arrData['errmsg'] = 'id not found';
        }else {
                // 업데이트 처리
                $boards = Boards::find($id);
                if($boards){
                    $boards->delete();
                    $arrData['code'] = '0';
                    $arrData['msg'] = 'success';
                } else {
                    $arrData['code'] ='E02';
                    $arrData['msg'] = 'Already Deleted';
                }
            }
        return $arrData;
    }
}
