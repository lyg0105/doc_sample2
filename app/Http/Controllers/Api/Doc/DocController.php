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
        $s_list_opt=[
            'now_page'=>$this->request->input('now_page'),
            'num_per_page'=>$this->request->input('num_per_page'),
            'sc'=>$this->request->input('sc')
        ];
        $doc_list_arr=array();
        $doc_list_arr=$this->doc_model->getList($s_list_opt);
        $result_arr=['data'=>$doc_list_arr];
        return parent::response($result_arr);
    }
}
