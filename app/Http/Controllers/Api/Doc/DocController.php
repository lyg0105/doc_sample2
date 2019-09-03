<?php
namespace App\Http\Controllers\Api\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Doc\DocModel;
use app\Values\XColumn\XColumnArr;

class DocController extends Controller
{
    private $request;
    private $doc_model;
    public $xColumnArr_obj=null;

    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->doc_model=new DocModel(resolve('App\Model\Base\Model'));
        //$this->doc_model->connectToServer('2');
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function list()
    {
        $doc_list_arr=array();
        $doc_list_arr=$this->doc_model->getList();
        $result_arr=['data'=>$doc_list_arr];
        return parent::response($result_arr);
    }
    public function getXColumnArr($x_name='write')
    {
        $this->xColumnArr_obj=new XColumnArr('DOC/Write');
        $result_arr=['data'=>$this->xColumnArr_obj];
        return parent::response($result_arr);
    }
}
