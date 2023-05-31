<?php
/********************************************** 
* 프로젝트명       : laravel_board
* 디렉토리         : Controllers
* 파일명           : BoardController.php
* 이력             : v001 0526 BJ.Park new
*                  v002 0530 BJ.Park 유효성 체크 추가
 **********************************************/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator; // v002 add
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
        // 로그인 체크
        if(auth()->guest()) {
            return redirect()->route('users.login');
        }


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
        // v002 update start
        // return view('write');
        return view('write'); // v002 del / v002 add
        // v002 update end
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $req
     * @return \Illuminate\Http\Response
     */
    public function store(Request $req)
    {

        // * v002 add start
        $req->validate([
            'title' => 'required|between:3,30' // 제목과 내용은 필수이므로 required, 3~30자 까지 설정
            ,'content' => 'required|max:1000' // 최대 1000자 까지 설정
        ]);
        // * v002 add end


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

        // * v002 add start
        // 유효성 검사
        $arr = ['id' => $id];
        // $arr = new Request($arr);
        $request->merge($arr); // merge()는 두개를 합친다는 뜻. 머지를 사용해서 request 안에 id값을 넣어준다.
        $request->request->add($arr);
        // * v002 add end


        // $request->validate([
        //     'id'        => 'required|integer' // add v002
        //     ,'title'    => 'required|between:3,30'
        //     ,'content'  => 'required|max:1000'
        // ]);

        // 유효성 검사 방법 2
        $validator = Validator::make(
            $request->only('id', 'title', 'content')
            ,[
                'id'        => 'required|integer'
                ,'title'    => 'required|between:3,30'
                ,'content'  => 'required|max:1000'
            ]
        );

        if($validator->fails()) {
            return redirect()
                ->back() // 이전 페이지로 돌아가는 메소드
                ->withErrors($validator)
                ->withInput($request->only('title', 'content')); // 받은 request객체를 세션에 등록하고 가져오는 메소드
        }


        // URL에 항당되는 view가 없으면 view로 이동, 없으면 redirect
        $boards = Boards::findOrFail($id);
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
