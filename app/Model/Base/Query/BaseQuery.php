<?php
namespace App\Model\Base\Query;

use App\Lib\Web\Web;
use App\Lib\Web\Paging\WebPaging;

class BaseQuery
{
    public $table = '';
    public $primaryKey=['id'];
    public $baseModel=null;
    public $x_column_arr=array();
    public $s_list_opt=[
        'now_page'=>1,
        'num_per_page'=>20,
        'order_id'=>'',
        'order_type'=>'',
        's_date_type'=>'',
        's_start_date'=>'',
        's_end_date'=>'',
        'sc'=>[],

        'table'=>'',
        'debug'=>false,
        'get_col'=>'*',
    ];

    function __construct($baseModel){
        $this->baseModel=$baseModel;
    }

    public function getList(){
        $where_row=$this->getWhereArr();
        $sql_opt=array('t'=>$this->table,'w'=>$where_row,'g'=>'COUNT(*) AS tot','o'=>1);
        $count_info=$this->baseModel->db_main->get_info_arr($sql_opt, $debug=false);
        $p_conf=[
            'now_page'=>$this->s_list_opt['now_page'],
            'num_per_page'=>$this->s_list_opt['num_per_page'],
            'tot'=>$count_info['tot']
        ];
        $webPaging=new WebPaging($p_conf);
        if(!empty($this->s_list_opt['order_id'])){
            $where_row[]='ORDER BY '.$this->s_list_opt['order_id'];
            if(!empty($this->s_list_opt['order_type'])){
                $where_row[]=' '.$this->s_list_opt['order_type'];
            }
        }
        $where_row[]=' LIMIT '.$webPaging->st_limit.', '.$webPaging->num_per_page;

        $get_col=$this->s_list_opt['get_col'];

        $sql_opt=array('t'=>$this->table,'w'=>$where_row,'g'=>$get_col);
        $info_arr=$this->baseModel->db_main->get_info_arr($sql_opt, $debug=false);

        return ['info_arr'=>$info_arr,'tot'=>$count_info['tot'],'start_index'=>$webPaging->get_index_num(0)];
    }

    public function getWhereArr(){
        $where_row=array();

        if(!empty($this->s_list_opt['sc'])){
            foreach($this->s_list_opt['sc'] as $k => $v){
                if(!empty($v)){
                    $v=Web::CheckHtmlStr($v);
                    if(isset($this->x_column_arr[$k])){
                        if($v=='empty'){
                            $where_row[]=" AND IFNULL($k,'') = ''";
                        }else{
                            $where_row[]=" AND $k LIKE '%".$v."%'";
                        }
                    }
                }
            }
        }

        return $where_row;
    }

    public function set_list_opt($s_list_opt=[]){
        $this->s_list_opt['table']=$this->table;
        foreach($this->s_list_opt as $key=>$val){
            if(isset($s_list_opt[$key])){
                $this->s_list_opt[$key]=$s_list_opt[$key];
            }
        }
    }
}
