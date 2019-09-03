<?php
namespace App\Values\XColumn;

class XColumnArr
{
    public $x_column_list_arr=[];//[ 'jasa_title'=>['name'=>'회사상호','type'=>'varchar','length'=>'30','pri'=>'','width'=>'100'], ]
    public $x_column_list_orig_arr=[];
    public $x_pri_col_arr=[];
    public $x_basic_col_arr=[];// ['key1','key2']
    public $x_view_col_arr=[];// ['key1','key2']
    public $is_number_col_arr=[];// ['key1','key2']
    public $is_tel_col_arr=[];// ['key1','key2']
    public $is_busin_col_arr=[];// ['key1','key2']
    public $is_date_col_arr=[];// ['key1','key2']
    public $select_col_arr=[];//'key'=>[ ['value'=>'1','text'=>'Y'],['value'=>'0','text'=>'N'] ]

    public $list_sort='';

    public function __construct($opt_obj)
    {
        $this->list_sort=isset($opt_obj['list_sort'])?$opt_obj['list_sort']:'';
        if(!empty($this->list_sort)){
            $now_path=str_replace('\\','/',app_path()).'/Values/XColumn/';
            $tmp_file_path=$now_path.$this->list_sort.'.php';
            if(file_exists($tmp_file_path)){
                include $tmp_file_path;
            }
        }
    }

    public function getData(){
        return
            [
                'x_column_list_arr'=>$this->x_column_list_arr,
                'x_column_list_orig_arr'=>$this->x_column_list_orig_arr,
                'x_pri_col_arr'=>$this->x_pri_col_arr,
                'x_basic_col_arr'=>$this->x_basic_col_arr,
                'x_view_col_arr'=>$this->x_view_col_arr,
                'is_number_col_arr'=>$this->is_number_col_arr,
                'is_tel_col_arr'=>$this->is_tel_col_arr,
                'is_busin_col_arr'=>$this->is_busin_col_arr,
                'is_date_col_arr'=>$this->is_date_col_arr,
                'select_col_arr'=>$this->select_col_arr,
                'list_sort'=>$this->list_sort,
            ];
    }
}
