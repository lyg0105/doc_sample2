<?php
namespace App\Http\Controllers\Api\Common;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Base\BaseModel;
use App\Services\Common\InsertOrUpdateService;

class CommonApiController extends Controller
{
    private $request;
    private $baseModel;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->doc_model=resolve('App\Model\Base\Model');
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