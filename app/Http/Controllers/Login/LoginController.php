<?php
namespace App\Http\Controllers\Login;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Login\JWTService;

class LoginController extends Controller
{
    private $request;
    private $jwtService;

    public function __construct(Request $request,JWTService $jwtService)
    {
        $this->request = $request;
        $this->jwtService=$jwtService;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        return view('login/login');
    }

    public function login(){
        $this->request->session()->put('id', 'lyg');
        $jwt_token=$this->jwtService->ecodeToken();
        $this->request->session()->put('token',$jwt_token);
        return redirect('main');
    }

    public function logout(){
        $this->request->session()->put('id', 'lyg');

        $this->request->session()->forget(['id']);
        $this->request->session()->flush();

        return redirect('login');
    }
}
