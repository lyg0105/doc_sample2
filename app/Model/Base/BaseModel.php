<?php
namespace App\Model\Base;

use App\Model\Base\Model;
use App\Lib\Web\Web;
use App\Model\Base\Query\BaseQuery;

class BaseModel
{
    protected $connection = 'mysql';
    protected $table = '';
    protected $primaryKey=['id'];
    public $incrementing=false;
    protected $keyType='array';

    public $db_main=null;


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
        $baseQuery=new BaseQuery($this);
        $baseQuery->table=$this->table;
        $baseQuery->set_list_opt($s_list_opt);
        $list_data=$baseQuery->getList();

        return $list_data;
    }


}
