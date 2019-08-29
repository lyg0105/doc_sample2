<?php
namespace App\Http\Controllers\Home\Doc;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Model\Doc\DocModel;

class DocController extends Controller
{
    private $request;
    private $doc_model;

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
        return view('doc/list',['doc_list_arr'=>$doc_list_arr]);
    }
}
