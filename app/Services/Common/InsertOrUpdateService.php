<?php
namespace App\Services\Common;

class InsertOrUpdateService
{
    public function action($opt_obj){
        $baseModel=$opt_obj['baseModel'];
        $request=$opt_obj['request'];

        $table=$request->input('table');
        $order_num=$request->input('order_num');

        $result_arr=['result'=>'true','data'=>'','msg'=>'성공'.$order_num[0],'error'=>[]];

        return $result_arr;
    }
}
