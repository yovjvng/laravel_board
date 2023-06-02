<?php
/********************************************** 
* 프로젝트명       : laravel_board
* 디렉토리         : Controllers
* 파일명           : UserController.php
* 이력             : v001 0526 BJ.Park new
 **********************************************/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    function login() {

        $arr['key'] = 'test';
        $arr['kim'] = 'park';
        Log::emergency('emergency', $arr);
        Log::alert('alert', $arr);
        Log::critical('critical', $arr);
        Log::error('error', $arr);
        Log::warning('warning', $arr);
        Log::notice('notice', $arr);
        Log::info('info', $arr);
        Log::debug('debug', $arr);

        return view('login');
    }

    function loginpost(Request $req) {

        // Log::debug('로그인 시작');
        // 유효성 체크
        $req->validate([
            'email'    => 'required|email|max:100'
            ,'password' => 'required|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        ]);

        // Log::debug('유효성 OK');

        // 유저정보 습득
        $user = User::where('email', $req->email)->first();
        if(!$user || !(Hash::check($req->password, $user->password))) {
            // Log::debug($req->password. ':' .$user->password);
            $error = '아이디와 비밀번호를 확인해 주세요.';
            return redirect()->back()->with('error', $error);
        }

        // 유저 인증작업
        Auth::login($user);
        if(Auth::check()) {
            session($user->only('id')); // 세션에 인증된 회원 pk 등록
            return redirect()->intended(route('boards.index')); // intended 사용 시 전에 있던 데이터 날리고 redirect
        } else {
            $error = '인증작업 에러';
            return redirect()->back()->with('error', $error);
        }

    }

    function registration() {
        return view('registration');
    }

    function registrationpost(Request $req) {
        // 유효성 체크
        $req->validate([
            'name'      => 'required|regex:/^[가-힣]+$/|min:2|max:30'
            ,'email'    => 'required|email|max:100'
            ,'password' => 'required_with:passwordchk|same:passwordchk|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        ]);

        $data['name'] = $req->name;
        $data['email'] = $req->email;
        $data['password'] = Hash::make($req->password);

        $user = User::create($data); // insert
        if(!$user) {
            $error = '시스템 에러가 발생하여, 회원가입에 실패했습니다.<br>잠시 후에 다시 회원가입을 시도해 주십시오';
            return redirect()
                ->route('users.registration')
                ->with('error', $error);
        }

        // 회원가입 완료 로그인 페이지로 이동
        return redirect()
            ->route('users.login')
            ->with('success', '회원가입을 완료했습니다.<br>가입하신 아이디와 비밀번호로 로그인 해 주십시오.');

    }

    function logout() {
        Session::flush(); // 세션 파기
        Auth::logout(); // 로그아웃
        return redirect()->route('users.login');
    }

    function withdraw() {
        $id = session('id'); // id 검증 필요
        $result = User::destroy($id); // 에러에 대한 처리 필요
        Session::flush(); // 세션 파기
        Auth::logout(); // 로그아웃
        return redirect()->route('users.login');
    }

    function useredit() {
        return view('useredit');
    }

    function usereditpost(Request $request){
        $validate = $request->validate([
            'password' => 'required|confirmed|min:8|max:20|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        ]);

        // 기존 패스워드 체크
        $same = Hash::check($validate['password'], Auth::user()->password);
        if ($same) {
            return redirect()->back()->with('message', '이전 비밀번호는 사용할 수 없습니다.');
            // with는 redirect에 붙어있을때 session에 저장한다.
        }

        $user = User::find(Auth::user()->id); // 기존 데이터 획득
        $user-> password = Hash::make($validate['password']);
        $user-> save();

        Auth::logout();
        return redirect()
            ->route('users.login')
            ->with('success', '수정을 완료했습니다.<br>변경하신 비밀번호로 로그인 해 주십시오.');



        // 동적으로 만드는 방법 -------------------------------------------

        // $arrKey = [];
        // // 유효성 체크를 하는 모든 항목 리스트
        // $chkList = [
        //     'name'      => 'required|regex:/^[가-힣]+$/|min:2|max:30'
        //     ,'email'    => 'required|email|max:100'
        //     ,'passwordnow' => 'regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        //     ,'password' => 'same:password_confirmation|regex:/^(?=.*[a-zA-Z])(?=.*[!@#$%^*-])(?=.*[0-9]).{8,20}$/'
        // ];
        // // 수정할 항목을 배열에 담는 처리
        // if($request->name !== $user->name) {
        //     $arrKey[] = 'name';
        // }
        // if($request->email !== $user->email) {
        //     $arrKey[] = 'email';
        // }
        // if(isset($request->password)) {
        //     $arrKey[] = 'password';
        // }
        // // 기존 패스워드 체크
        // $same = Hash::check($validate['passwordnow'], Auth::user()->password);
        // if ($same) {
        //     return redirect()->back()->with('message', '이전 비밀번호는 사용할 수 없습니다.');
        // }
        // // 유효성 체크할 항목 세팅하는 처리
        // $arrKey['password'] = $chkList['password'];
        // foreach($arrKey as $val) {
        //     $arrchk[$val] = $chkList[$val];
        // }

        // // 유효성 체크
        // $req->validate($arrchk);

        // // 수정할 데이터 셋팅
        // foreach ($arrKey as $val) {
        //     if($val === 'password') {
        //         $val = Hash::make($req->val);
        //         continue;
        //     }
        //     $user->$val = $req->$val;
        // }
        // $user->save();



    }



}
