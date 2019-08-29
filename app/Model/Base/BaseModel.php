<?php
namespace App\Model\Base;

use App\Model\Base\Model;
use App\Lib\Web\Web;

class BaseModel
{
    protected $connection = 'mysql';
    protected $table = '';
    protected $primaryKey=['id'];
    public $incrementing=false;
    protected $keyType='array';

    public $db_main=null;
    public $x_column_arr=array();
    public $write_except_col_arr=array();
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

    function __construct(Model $db_main){
        $this->db_main=$db_main;
    }

    public function connectToServer($server_num){
        if($server_num=='2'){
            $db_host=env('DB_HOST2', '');
            $db_user=env('DB_USERNAME2', '');
            $db_pass=env('DB_PASSWORD2', '');
            $db_name=env('DB_DATABASE2', '');
            $db_charset='utf-8';
            $db_port=env('DB_PORT2', '');
            $this->db_main->connect($db_host,$db_user,$db_pass,$db_name,$db_charset,$db_port);
        }else{
            $db_host=env('DB_HOST', '');
            $db_user=env('DB_USERNAME', '');
            $db_pass=env('DB_PASSWORD', '');
            $db_name=env('DB_DATABASE', '');
            $db_charset='utf-8';
            $db_port=env('DB_PORT', '');
            $this->db_main->connect($db_host,$db_user,$db_pass,$db_name,$db_charset,$db_port);
        }
    }

    public function getList($s_list_opt=[]){
        $this->set_list_opt($s_list_opt);

        $tmp_w=$this->getWhereArr();
        $sql_opt=array('t'=>$this->table,'w'=>$tmp_w,'g'=>'*');
        $info_arr=$this->db_main->get_info_arr($sql_opt, $debug=false);
        return $info_arr;
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
