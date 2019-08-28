<?php
namespace App\Http\Controllers\My;

use App\Http\Controllers\Controller;
use App\Services\MyService;
use Illuminate\Http\Request;

class HelloController extends Controller
{
    private $myService;
    private $request;

    public function __construct(MyService $myService,Request $request)
    {
        $this->myService = $myService;
        $this->request = $request;
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function index()
    {
        $rs_str=$this->myService->hello();
        $rs_str.=$this->myService->world();
        $name = $this->request->input('name');
        $rs_str.=' '.$name;
        return $rs_str;
    }
}
