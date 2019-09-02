<?php
namespace App\Services\Common;

use Illuminate\Http\Request;

class ConvertRequest
{
    public $request=null;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public static function getRequestDataByXcolumnArr($opt_obj){
        $x_column_arr=$opt_obj['x_column_arr'];

        $result_arr=array('result'=>'false','data'=>'','msg'=>'키정보가 없습니다.');

        $is_num_col_arr=array();
        $pri_col_arr=array();
        $date_col_arr=array();
        foreach($x_column_arr as $key=>$val){
            $val['type']=strtolower($val['type']);
            if(strstr($val['type'],'(')!==false){
                $val['type']=explode($val['type'],'(')[0];
            }
            if(in_array($val['type'],array('double','float','int','decimal'))){
                $is_num_col_arr[]=$key;
            }else if(in_array($val['type'],array('date','datetime'))){
                $date_col_arr[]=$key;
            }
            if(!empty($val['pri'])){
                $pri_col_arr[]=$key;
            }
        }

        if(empty($pri_col_arr)){
            $result_arr=array('result'=>'false','data'=>'','msg'=>'키정보가 없습니다.');
            return $result_arr;
        }
        $last_pri_col=end($pri_col_arr);//마지막 키

        $post_data_arr=array();
        $data_length=0;
        foreach($_POST as $key=>$val){
            if(!in_array($key,array('table','is_update'))){
                if(isset($x_column_arr[$key])){
                    $post_data_arr[$key]=$val;
                    $data_length=count($val);
                }
            }
        }

        //프라이머리키값만 있는 배열
        $w_col_val_arr=isset($_POST['w_col_val_arr'])?$_POST['w_col_val_arr']:array();

        $return_data_arr=array(
            'post_data_arr'=>$post_data_arr,
            'pri_col_arr'=>$pri_col_arr,
            'last_pri_col'=>$last_pri_col,
            'is_num_col_arr'=>$is_num_col_arr,
            'date_col_arr'=>$date_col_arr,
            'data_length'=>$data_length,
            'w_col_val_arr'=>$w_col_val_arr
        );
        $result_arr=array('result'=>'true','data'=>$return_data_arr,'msg'=>'성공');
        return $result_arr;
    }

    public static function getDefaultRequestColValArr($data_arr){
        $pri_col_arr=$data_arr['pri_col_arr'];
        $last_pri_col=$data_arr['last_pri_col'];
        $col_val_arr=$data_arr['post_data_arr'];
        $is_num_col_arr=$data_arr['is_num_col_arr'];
        $date_col_arr=$data_arr['date_col_arr'];
        $x_column_arr=$data_arr['x_column_arr'];

        $result_arr=array('result'=>'false','data'=>'','msg'=>'데이터 없음.');

        foreach($col_val_arr as $key=>$val){
            $val_str=$val;

            //max_length 맞추기
            if(!empty($x_column_arr[$key]['length'])&&$x_column_arr[$key]['length']!=1){
                $val_str=mb_substr($val_str,0,$x_column_arr[$key]['length'],"UTF-8");
            }

            if(in_array($key,$is_num_col_arr)){
                if(empty($val_str)){$val_str='0';}
                $val_str= preg_replace("/[^0-9.-]*/s", "",$val_str);
                $val_str=str_replace(',','',$val_str);
                if(!is_numeric($val_str)){
                    $result_arr=array('result'=>'false','data'=>$col_val_arr,'msg'=>$x_column_arr[$key]['name'].' 은 숫자만 입력해 주세요.');
                    return $result_arr;
                }
            }
            if(in_array($key,$date_col_arr)){
                if(empty($val_str)){
                    if(in_array($key,$pri_col_arr)){
                        $val_str=date('Y-m-d');
                    }else{
                        $val_str='0000-00-00';
                        if(strstr($key,'create_date')!==false||strstr($key,'update_date')!==false){
                            $val_str=date('Y-m-d H:i:s');
                        }
                    }
                }
            }

            $col_val_arr[$key]=$val_str;
        }
        $result_arr=array('result'=>'true','data'=>$col_val_arr,'msg'=>'성공');
        return $result_arr;
    }
}
