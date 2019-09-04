<?php
namespace App\Services\Common;

use App\Services\Common\ConvertRequest;
use App\Model\Base\Func\DBFunc;
use App\Lib\Web\Web;

class DeleteService
{
    public function action($opt_obj)
    {
        $result_arr=['result'=>'true','data'=>'','msg'=>'성공','error'=>[]];

        if(!isset($opt_obj['is_transaction'])){$opt_obj['is_transaction']=true;}
        if(!isset($opt_obj['is_commit'])){$opt_obj['is_commit']=true;}
        if(!isset($opt_obj['prev_func'])){$opt_obj['prev_func']='';}
        if(!isset($opt_obj['after_func'])){$opt_obj['after_func']='';}

        $baseModel=$opt_obj['baseModel'];
        $request=$opt_obj['request'];

        $table=$request->input('table');

        if(empty($table)){
            $result_arr=['result'=>'true','data'=>'','msg'=>'테이블 정보가 없습니다.','error'=>[]];
            return $result_arr;
        }

        $x_column_arr=DBFunc::getXColumnArrByTableName(['table'=>$table,'baseModel'=>$baseModel]);
        $tmp_opt_obj=[
            'x_column_arr'=>$x_column_arr
        ];
        $tmp_rs=ConvertRequest::getRequestDataByXcolumnArr($tmp_opt_obj);
        $post_data_arr=$tmp_rs['data']['post_data_arr'];
        $pri_col_arr=$tmp_rs['data']['pri_col_arr'];
        $data_length=$tmp_rs['data']['data_length'];
        $w_col_val_arr=$tmp_rs['data']['w_col_val_arr'];

        $where_col_val_arr=[];
        $is_array_data=true;
        foreach($x_column_arr as $key=>$val){
            if(isset($post_data_arr[$key])){
                $where_col_val_arr[$key]=$post_data_arr[$key];
                if(!is_array($where_col_val_arr[$key])){
                    $is_array_data=false;
                }
            }
        }
        if(empty($where_col_val_arr)&&!empty($w_col_val_arr)){
            foreach($w_col_val_arr as $key=>$val){
                $where_col_val_arr[$key]=$val;
                $data_length=count($val);
                if(!is_array($where_col_val_arr[$key])){
                    $is_array_data=false;
                }
            }
        }
        if(!$is_array_data){
            $result_arr=['result'=>'false','data'=>'','msg'=>'데이터가 올바르지 않습니다.(배열아님).','error'=>[]];
            return $result_arr;
        }

        $attemp_cnt=0;
        $success_cnt=0;
        if($opt_obj['is_transaction']){
            $baseModel->db_main->excute('begin');
        }
        for($i=0;$i<$data_length;$i++){
            $attemp_cnt++;
            $pri_where_arr=array();
            $col_val_arr=array();
            foreach($where_col_val_arr as $key=>$val){
                $val[$i]=Web::CheckHtmlStr($val[$i]);
                $pri_where_arr[]="AND ".$key."='".$val[$i]."'";
                $col_val_arr[$key]=$val[$i];
            }

            if(empty($pri_where_arr)){
                if($opt_obj['is_transaction']){
                    $baseModel->db_main->excute('rollback');
                }
                $result_arr=['result'=>'false','data'=>'','msg'=>'키 정보가 없습니다..','error'=>[]];
                return $result_arr;
            }

            $sql_opt=array('t'=>$table,'w'=>$pri_where_arr,'g'=>'*','o'=>1);
            $pre_info=$baseModel->db_main->get_info_arr($sql_opt, $debug = 0);

            if(empty($pre_info)){
                if($opt_obj['is_transaction']){
                    $baseModel->db_main->excute('rollback');
                }
                $result_arr=['result'=>'false','data'=>'','msg'=>$i.'번째 정보가 없습니다.','error'=>[]];
                return $result_arr;
            }

            if(!empty($opt_obj['prev_func'])){
                if(function_exists($opt_obj['prev_func'])){
                    $tmp_opt_arr=array(
                        'table'=>$table,
                        'pri_col_arr'=>$pri_col_arr,
                        'x_column_arr'=>$x_column_arr
                    );
                    $tmp_rs=call_user_func($opt_obj['prev_func'],$col_val_arr,$i,$pre_info,$tmp_opt_arr);
                    if($tmp_rs['result']!='true'){
                        $result_arr=array('result'=>'false','data'=>'','msg'=>$i.'번째 삭제전처리 중 오류:'.$tmp_rs['msg']);
                        return $result_arr;
                    }
                }
            }

            $sql_opt=['table'=>$table,'pri_col_val_arr'=>$col_val_arr];
            $result=$baseModel->db_main->delete($sql_opt,$debug=false);
            if($result){
                if(!empty($opt_obj['after_func'])){
                    if(function_exists($opt_obj['after_func'])){
                        $tmp_opt_arr=array(
                            'table'=>$table,
                            'pri_col_arr'=>$pri_col_arr,
                            'x_column_arr'=>$x_column_arr
                        );
                        $tmp_rs=call_user_func($opt_obj['after_func'],$col_val_arr,$i,$pre_info,$tmp_opt_arr);
                        if($tmp_rs['result']!='true'){
                            $result_arr=array('result'=>'false','data'=>'','msg'=>$i.'번째 삭제후처리 중 오류:'.$tmp_rs['msg']);
                            return $result_arr;
                        }
                    }
                }
                $success_cnt++;
            }else{
                if($opt_obj['is_transaction']){
                    $baseModel->db_main->excute('rollback');
                }
                $result_arr=array('result'=>'false','data'=>'','msg'=>$attemp_cnt.' 번째 삭제 중 오류입니다.');
                return $result_arr;
            }
        }
        if($opt_obj['is_transaction']){
            if($opt_obj['is_commit']){
                $baseModel->db_main->excute('commit');
            }
        }
        $result_arr=array('result'=>'true','data'=>'','msg'=>$success_cnt.' 개 삭제되었습니다.');

        return $result_arr;
    }
}
