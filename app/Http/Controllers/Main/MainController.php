<?php
namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Services\MyService;
use Illuminate\Http\Request;
use App\Services\Login\JWTService;

class MainController extends Controller
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
        $id = $this->request->session()->get('id');
        $token=$this->request->session()->get('token');
        $token_data=$this->jwtService->decodeToken($token);

        return view('main/main',['id'=>$id,'token'=>$token,'token_data'=>$token_data]);
    }
}
