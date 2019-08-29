<?php
namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Base\BaseModel;

class CommonApiController extends Controller
{
    private $request;
    private $baseModel;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->baseModel=new BaseModel();
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function list()
    {
        $data_list_arr=$this->baseModel->getList();

        return parent::response(['data'=>$data_list_arr]);
    }
}
