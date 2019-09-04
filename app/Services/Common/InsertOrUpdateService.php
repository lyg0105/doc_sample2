<?php
namespace App\Services\Common;

use App\Services\Common\ConvertRequest;
use App\Model\Base\Func\DBFunc;

class InsertOrUpdateService
{
    public function action($opt_obj)
    {
        $result_arr=['result'=>'true','data'=>'','msg'=>'성공','error'=>[]];
        if(!isset($opt_obj['is_transaction'])){$opt_obj['is_transaction']=true;}
        if(!isset($opt_obj['is_commit'])){$opt_obj['is_commit']=true;}
        if(!isset($opt_obj['is_return'])){$opt_obj['is_return']=false;}
        if(!isset($opt_obj['prev_func'])){$opt_obj['prev_func']='';}
        if(!isset($opt_obj['after_func'])){$opt_obj['after_func']='';}

        $baseModel=$opt_obj['baseModel'];
        $request=$opt_obj['request'];

        $table=$request->input('table');
        $is_update=$request->input('is_update');
        $is_update_arr=$request->input('is_update_arr');
        $input_row_num=$request->input('input_row_num');
        $is_return=$request->input('is_return');
        if(empty($is_return)){$is_return=$opt_obj['is_return'];}
        $is_default_val=$request->input('is_default_val');

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
        $last_pri_col=$tmp_rs['data']['last_pri_col'];
        $is_num_col_arr=$tmp_rs['data']['is_num_col_arr'];
        $date_col_arr=$tmp_rs['data']['date_col_arr'];
        $data_length=$tmp_rs['data']['data_length'];
        $w_col_val_arr=$tmp_rs['data']['w_col_val_arr'];

        //기본값 세팅 정보
        if(empty($is_update)){
            if(!empty($is_default_val)){
                $default_data_val_arr=array();
                for($i=0;$i<$data_length;$i++){
                    $default_data_val_arr[]='';
                }

                foreach($x_column_arr as $key=>$val){
                    if(!isset($post_data_arr[$key])){
                        $post_data_arr[$key]=$default_data_val_arr;
                    }
                }
            }
        }

        $error_info_arr=array();//array(array('row_num'=>'','msg'=>''))
        $return_info_arr=array();
        $pre_pri_col_val=array();//자동 키만들기 참조값

        $attempt_cnt=0;
        $success_cnt=0;
        $insert_cnt=0;
        $update_cnt=0;
        if($opt_obj['is_transaction']){
            $baseModel->db_main->excute("begin");
        }
        for($i=0;$i<$data_length;$i++){
            $tmp_col_val_arr=array();
            foreach($post_data_arr as $key=>$val){
                $tmp_col_val_arr[$key]=$val[$i];
            }

            $tmp_is_update=$is_update;
            if(!empty($is_update_arr)){
                if(isset($is_update_arr[$i])){
                    $tmp_is_update=$is_update_arr[$i];
                }
            }

            //이미 있는지 확인하여 있으면 수정으로
            if(empty($tmp_is_update)){
                if(!empty($post_data_arr[$last_pri_col][$i])){
                    $tmp_w=array();
                    foreach($pri_col_arr as $key){
                        $tmp_w[]="AND ".$key."='".$post_data_arr[$key][$i]."'";
                    }
                    $tmp_get='COUNT(*) AS tot';
                    $sql_opt=array('t'=>$table,'w'=>$tmp_w,'g'=>$tmp_get,'o'=>1);
                    $tmp_info=$baseModel->db_main->get_info_arr($sql_opt, $debug = false);
                    if(!empty($tmp_info['tot'])){
                        $tmp_is_update='1';
                    }
                }
            }

            $data_arr=array(
                'is_num_col_arr'=>$is_num_col_arr,
                'date_col_arr'=>$date_col_arr,
                'pri_col_arr'=>$pri_col_arr,
                'last_pri_col'=>$last_pri_col,
                'x_column_arr'=>$x_column_arr,
                'post_data_arr'=>$tmp_col_val_arr
            );
            $tmp_rs=ConvertRequest::getDefaultRequestColValArr($data_arr);
            if($tmp_rs['result']=='false'){
                return $tmp_rs;
            }
            $tmp_col_val_arr=$tmp_rs['data'];
            foreach($tmp_col_val_arr as $key=>$val){
                $post_data_arr[$key][$i]=$val;
            }

            $attempt_cnt++;
            $pre_info=null;
            $pri_col_val_arr=array();
            $data_col_val_arr=array();
            $result=null;

            $tmp_rs=$this->getDetailPriData(
            [
                'baseModel'=>$baseModel,
                'table'=>$table,
                'tmp_is_update'=>$tmp_is_update,
                'post_data_arr'=>$post_data_arr,
                'pri_col_arr'=>$pri_col_arr,
                'last_pri_col'=>$last_pri_col,
                'pre_pri_col_val'=>$pre_pri_col_val,
                'i'=>$i
            ]);
            $post_data_arr=$tmp_rs['data']['post_data_arr'];
            $pri_col_val_arr=$tmp_rs['data']['pri_col_val_arr'];
            $data_col_val_arr=$tmp_rs['data']['data_col_val_arr'];
            $pre_pri_col_val=$tmp_rs['data']['pre_pri_col_val'];
            $pre_info=$tmp_rs['data']['pre_info'];
            if(!empty($opt_obj['prev_func'])){
               if(function_exists($opt_obj['prev_func'])){
                    $opt_arr=array(
                         'table'=>$table,
                         'is_update'=>$tmp_is_update,
                         'pri_col_arr'=>$pri_col_arr,
                         'x_column_arr'=>$x_column_arr,
                         'pre_info'=>$pre_info,
                         'baseModel'=>$baseModel,
                    );
                    $tmp_rs=call_user_func($opt_obj['prev_func'],$data_col_val_arr,$pri_col_val_arr,$i,$opt_arr);
                    if($tmp_rs['result']!='true'){
                         $tmp_row_num=$i;
                         if(!empty($input_row_num)){$tmp_row_num=$input_row_num[$i];}
                         $error_info_arr[]=array('row_num'=>$tmp_row_num,'msg'=>$attempt_cnt.'번째 작업 중 에러입니다.'.$tmp_rs['msg']);
                         if($opt_obj['is_transaction']){
                              $baseModel->db_main->excute("rollback");
                              $baseModel->db_main->excute("begin");
                         }
                         continue;
                    }
                    $data_col_val_arr=$tmp_rs['data']['col_val_arr'];
                }
            }

            $sql_opt=array(
               'table'=>$table,
               'col_val_arr'=>$data_col_val_arr,
               'pri_col_val_arr'=>$pri_col_val_arr
           );
           if(empty($tmp_is_update)){
               $result=$baseModel->db_main->insert($sql_opt,$debug=false);//등록
           }else{
               $result=$baseModel->db_main->update($sql_opt,$debug=false);//수정
           }

           if($result){
               $last_info=null;
              if(!empty($pri_col_val_arr)){
                   $tmp_w=array();
                   foreach($pri_col_val_arr as $key=>$val){
                        $tmp_w[]="AND {$key}='{$val}'";
                   }
                   $sql_opt=array('t'=>$table,'w'=>$tmp_w,'o'=>1);
                   $last_info=$baseModel->db_main->get_info_arr($sql_opt, $debug = 0);
              }

              if(!empty($opt_obj['after_func'])){
                   if(function_exists($opt_obj['after_func'])){
                        $opt_arr=array(
                             'table'=>$table,
                             'is_update'=>$tmp_is_update,
                             'pri_col_arr'=>$pri_col_arr,
                             'x_column_arr'=>$x_column_arr,
                             'pre_info'=>$pre_info,
                             'db_conn'=>$db,
                             's_dlit'=>$s_dlit,
                             'last_info'=>$last_info
                        );
                        $tmp_rs=call_user_func($opt_obj['after_func'],$data_col_val_arr,$pri_col_val_arr,$i,$opt_arr);
                        if($tmp_rs['result']!='true'){
                             $tmp_row_num=$i;
                             if(!empty($input_row_num)){$tmp_row_num=$input_row_num[$i];}
                             $error_info_arr[]=array('row_num'=>$tmp_row_num,'msg'=>$attempt_cnt.'번째 후작업 중 에러입니다.'.$tmp_rs['msg']);
                             if($opt_obj['is_transaction']){
                                  $baseModel->db_main->excute("rollback");
                                  $baseModel->db_main->excute("begin");
                             }
                             continue;
                        }
                        $data_col_val_arr=$tmp_rs['data']['col_val_arr'];
                   }
              }

              $success_cnt++;

              if(!empty($is_return)){
                   $return_col_val_arr=array();
                   foreach($post_data_arr as $key=>$val){
                        $return_col_val_arr[$key]=$val[$i];
                   }
                   if(!empty($input_row_num)){
                        if(!isset($return_col_val_arr['input_row_num'])){$return_col_val_arr['input_row_num']=array();}
                        $return_col_val_arr['input_row_num']=$input_row_num[$i];
                   }
                   $return_info_arr[]=$return_col_val_arr;
              }

              if(!empty($tmp_is_update)){
                   $update_cnt++;
              }else{
                   $insert_cnt++;
              }
           }else{
               $tmp_row_num=$i;
               if(!empty($input_row_num)){$tmp_row_num=$input_row_num[$i];}
               $error_info_arr[]=array('row_num'=>$tmp_row_num,'msg'=>$attempt_cnt.'번째 작업 중 에러입니다.'.$baseModel->db_main->get_error());
           }
       }

       if($attempt_cnt==$success_cnt){
          if($opt_obj['is_transaction']){
               if($opt_obj['is_commit']){
                    $baseModel->db_main->excute("commit");
               }
          }
          $suc_msg=$success_cnt."개 등록 되었습니다.";
          if(!empty($is_update)){
               $suc_msg=$success_cnt."개 수정 되었습니다.";
          }
          $result_arr=array('result'=>'true','data'=>$return_info_arr,'msg'=>$suc_msg);
     }else{
          if($opt_obj['is_transaction']){
               $baseModel->db_main->excute("rollback");
          }
          $tmp_msg=$attempt_cnt.'개 중 '.($attempt_cnt-$success_cnt).'개 오류 입니다.';
          if($attempt_cnt==1&&count($error_info_arr)==1){
               $tmp_msg.="에러:".$error_info_arr[0]['msg'];
          }
          $result_arr=array('result'=>'false','data'=>'','msg'=>$tmp_msg,'error'=>$error_info_arr);
     }

        return $result_arr;
    }

    public function getDetailPriData($opt_obj){
        $baseModel=$opt_obj['baseModel'];
        $table=$opt_obj['table'];
        $tmp_is_update=$opt_obj['tmp_is_update'];
        $post_data_arr=$opt_obj['post_data_arr'];
        $pri_col_arr=$opt_obj['pri_col_arr'];
        $last_pri_col=$opt_obj['last_pri_col'];
        $pre_pri_col_val=$opt_obj['pre_pri_col_val'];
        $data_col_val_arr=array();
        $i=$opt_obj['i'];
        $pre_info=null;

        if(empty($tmp_is_update)){
           //키값이 없으면 만들기
           $tmp_pri_col_val_arr=array();
           if(empty($post_data_arr[$last_pri_col][$i])){
                $tmp_pre_val_arr=array();
                foreach($pri_col_arr as $key){
                     if($key!=$last_pri_col){
                          $tmp_pre_val_arr[]=$post_data_arr[$key][$i];
                          $tmp_pri_col_val_arr[$key]=$post_data_arr[$key][$i];
                     }
                }
                $tmp_pre_val_str=implode(",",$tmp_pre_val_arr);
                if(empty($tmp_pre_val_str)){$tmp_pre_val_str='empty_key';}
                if(isset($pre_pri_col_val[$tmp_pre_val_str])){
                     $pre_pri_col_val[$tmp_pre_val_str]++;
                }else{
                     $pre_pri_col_val[$tmp_pre_val_str]=DBfunc::getAutoIncrementNum(['baseModel'=>$baseModel,'table'=>$table,'auto_key'=>$last_pri_col,'pri_col_val'=>$tmp_pri_col_val_arr]);
                }
                $post_data_arr[$last_pri_col][$i]=$pre_pri_col_val[$tmp_pre_val_str];
           }
           foreach($post_data_arr as $key=>$val){
               $data_col_val_arr[$key]=$val[$i];
           }
           foreach($pri_col_arr as $key){
                $pri_col_val_arr[$key]=$post_data_arr[$key][$i];
           }
       }else{
           //수정 (키컬럼 제외)
           $pri_where_arr=array();
           foreach($post_data_arr as $key=>$val){
               $data_col_val_arr[$key]=$val[$i];
           }
           foreach($pri_col_arr as $key){
                $pri_col_val_arr[$key]=$post_data_arr[$key][$i];
           }

           if(!empty($w_key_arr)&&!empty($w_val_arr)){
                $pri_col_val_arr=array();
                $a=0;
                foreach($w_key_arr as $key){
                     $pri_col_val_arr[$key]=$w_val_arr[$i][$a];
                     $a++;
                }
           }

           if(!empty($pri_col_val_arr)){
                $tmp_w=array();
                foreach($pri_col_val_arr as $key=>$val){
                     $tmp_w[]="AND {$key}='{$val}'";
                }
                $sql_opt=array('t'=>$table,'w'=>$tmp_w,'o'=>1);
                $pre_info=$baseModel->db_main->get_info_arr($sql_opt, $debug = null);
           }
       }
       $data_arr=[
           'post_data_arr'=>$post_data_arr,
           'pri_col_val_arr'=>$pri_col_val_arr,
           'data_col_val_arr'=>$data_col_val_arr,
           'pre_pri_col_val'=>$pre_pri_col_val,
           'pre_info'=>$pre_info,
       ];

       $result_arr=['result'=>'true','data'=>$data_arr,'msg'=>'성공'];
       return $result_arr;
    }
}
