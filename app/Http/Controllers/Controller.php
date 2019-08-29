<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
//use Illuminate\Foundation\Bus\DispatchesJobs;
//use Illuminate\Foundation\Validation\ValidatesRequests;
//use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    //use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
    [
        'result'=>'true/false',
        'data'=>[
            ['name'=>'1','title'=>'test'],
            ['name'=>'2','title'=>'test2'],
        ],
        'msg'=>'strMessage',
        'error'=>[
            ['row_num'=>'1','msg'=>''],
        ]
    ]
    */
    protected function response($result_arr = [])
    {
        $response = [
            'result' => isset($result_arr['result']) ? $result_arr['result'] :'true',
            'data' => isset($result_arr['data']) ? $result_arr['data'] :'',
            'msg' => isset($result_arr['msg']) ? $result_arr['msg'] :'',
            'error' => isset($result_arr['error']) ? $result_arr['error'] :[],
        ];
        return \json_encode($response,JSON_UNESCAPED_UNICODE);
    }
}
