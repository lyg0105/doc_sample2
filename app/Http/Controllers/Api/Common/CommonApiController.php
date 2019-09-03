<?php
namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Base\BaseModel;
use App\Services\Common\InsertOrUpdateService;
use App\Values\XColumn\XColumnArr;

class CommonApiController extends Controller
{
    public $request;
    public $baseModel;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->baseModel=resolve('App\Model\Base\BaseModel');
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
    public function xcolumn()
    {
        $sort=$this->request->input('list_sort');
        $this->xColumnArr_obj=new XColumnArr(['list_sort'=>$sort]);
        $result_arr=['data'=>$this->xColumnArr_obj->getData()];
        return parent::response($result_arr);
    }
    public function write()
    {
        $insertOrUpdateService =new InsertOrUpdateService();
        $opt_obj=[
            'baseModel'=>$this->baseModel,
            'request'=>$this->request
        ];
        $result_arr=$insertOrUpdateService->action($opt_obj);
        return parent::response($result_arr);
    }
}
