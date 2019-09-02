<?php
namespace App\Model\Base\Func;

use App\Model\Base\BaseModel;

class DBFunc
{
    public static function getDlitInfo($opt_obj)
    {
        $baseModel=$opt_obj['baseModel'];
        $dlit_code=$opt_obj['dlit_code'];
        $sql_opt=array('t'=>'dlit','w'=>array("AND dlit_code='".$dlit_code."' AND dlit_ingyn='Y'"),'o'=>1);
        $dlit_info=$baseModel->db_main->get_info_arr($sql_opt, $debug = 0);

        return $dlit_info;
    }

    public static function getBaseModelByDlitInfo($opt_obj)
    {
        $dlit_info=$opt_obj['dlit_info'];
        $result_arr=array('result'=>'false','data'=>'','msg'=>'실패');

        if(empty($dlit_info)){
            $result_arr=array('result'=>'false','data'=>'','msg'=>'dlit_info 없음');
            return $result_arr;
        }

        $tmp_db=new BaseModel();

        $db_host=!empty($dlit_info['dlit_ip'])?$dlit_info['dlit_ip']:env('DB_HOST', '');
        $db_user=!empty($dlit_info['dlit_id'])?$dlit_info['dlit_id']:env('DB_USERNAME', '');
        $db_pass=!empty($dlit_info['dlit_pwd'])?$dlit_info['dlit_pwd']:env('DB_PASSWORD', '');
        $db_name=!empty($dlit_info['dlit_dbname'])?$dlit_info['dlit_dbname']:env('DB_DATABASE', '');
        $db_charset='utf-8';
        $db_port=!empty($dlit_info['dlit_port'])?$dlit_info['dlit_port']:env('DB_PORT', '');

        $tmp_rs=$tmp_db->db_main->connect($db_host,$db_user,$db_pass,$db_name,$db_charset,$db_port);
        if(!$tmp_rs){
            $result_arr=array('result'=>'false','data'=>'','msg'=>'접속오류:');
            return $result_arr;
        }

        $result_arr=array('result'=>'true','data'=>$tmp_db,'msg'=>'성공');
        return $result_arr;
    }

    public static function hasTable($opt_obj)
    {
        $baseModel=$opt_obj['baseModel'];
        $db_name=isset($opt_obj['db_name'])?$opt_obj['db_name']:"cdisd";
        $dlit_code=$opt_obj['table_name'];

        //테이블 정보 얻기
        $tmp_w=array("AND TABLE_SCHEMA='$db_name' AND TABLE_NAME='$table_name' LIMIT 1");
        $sql_opt=array('t'=>'information_schema.tables','w'=>$tmp_w,'o'=>1);
        $table_info=$baseModel->db_main->get_info_arr($sql_opt, $debug = null);

        $is_exist_table=false;
        if(!empty($table_info)){
            $is_exist_table=true;
        }
        return $is_exist_table;
    }
    public static function getCreateSqlOfTable($opt_obj)
    {
        $baseModel=isset($opt_obj['baseModel'])?$opt_obj['baseModel']:null;
        $db_name=isset($opt_obj['db_name'])?$opt_obj['db_name']:"cdisd";
        $from_table_name=isset($opt_obj['from_table_name'])?$opt_obj['from_table_name']:"";
        $table_name=isset($opt_obj['table_name'])?$opt_obj['table_name']:"";
        if(empty($baseModel)||empty($db_name)||empty($table_name)||empty($from_table_name)){
            $result_arr=array('result'=>'false','data'=>'','msg'=>'필수입력 정보가 없습니다.');
            return $result_arr;
        }

        //테이블 정보 얻기
        $tmp_w=array("AND TABLE_SCHEMA='$db_name' AND TABLE_NAME='$from_table_name' LIMIT 1");
        $sql_opt=array('t'=>'information_schema.tables','w'=>$tmp_w,'o'=>1);
        $table_info=$baseModel->db_main->get_info_arr($sql_opt, $debug =0);

        if(empty($table_info)){
            $result_arr=array('result'=>'false','data'=>'','msg'=>'테이블정보 없음.'.$db_conn->get_error());
            return $result_arr;
        }

        $tmp_col="COLUMN_NAME,COLUMN_TYPE,COLUMN_DEFAULT,COLUMN_KEY,COLUMN_COMMENT";
        $tmp_w=array("AND TABLE_SCHEMA='$db_name' AND TABLE_NAME='$from_table_name'");
        $sql_opt=array('t'=>'information_schema.columns','w'=>$tmp_w,'g'=>$tmp_col);
        $col_info_arr=$baseModel->db_main->get_info_arr($sql_opt, $debug = null);

        $pri_col_arr=array();
        $create_opt_arr=array();
        foreach($col_info_arr as $col_val){
            if($col_val['COLUMN_KEY']=='PRI'){
                $col_val['COLUMN_DEFAULT']='NOT NULL';
                $pri_col_arr[]=$col_val['COLUMN_NAME'];
            }else if(empty($col_val['COLUMN_DEFAULT'])||$col_val['COLUMN_DEFAULT']=='NULL'){
                $col_val['COLUMN_DEFAULT']='DEFAULT NULL';
            }else{
                $col_val['COLUMN_DEFAULT']='NOT NULL DEFAULT '.$col_val['COLUMN_DEFAULT'];
            }
            $create_opt_arr[]=$col_val['COLUMN_NAME']." ".$col_val['COLUMN_TYPE']." ".$col_val['COLUMN_DEFAULT']." COMMENT '".$col_val['COLUMN_COMMENT']."'";
        }

        if(!empty($pri_col_arr)){
            $pri_col_str=implode(",",$pri_col_arr);
            $create_opt_arr[]="PRIMARY KEY(".$pri_col_str.")";
        }
        $create_opt_str=implode(",",$create_opt_arr);//배열을 다시 문자열로 변환해 쿼리에 적용시킬수 있는 상태로 만듬.


        $create_sql="CREATE TABLE ".$table_name."(".$create_opt_str.")";
        $create_sql.="ENGINE=".$table_info['ENGINE']." CHARSET=utf8 COMMENT='".$table_info['TABLE_COMMENT']."'";

        $result_arr=array('result'=>'true','data'=>$create_sql,'msg'=>'성공.');
        return $result_arr;
    }
    public static function getAutoIncrementNum($opt_obj)
    {
        $baseModel=$opt_obj['baseModel'];
        $table=$opt_obj['table'];
        $auto_key=$opt_obj['auto_key'];
        $pri_col_val=$opt_obj['pri_col_val'];

        $auto_num=1;

        if($baseModel==null){
            return null;
        }

        $tmp_where=array();
        foreach($pri_col_val_arr as $key=>$val){
            if($key==$auto_key){continue;}
            $tmp_where[]="AND ".$key."='".$val."'";
        }
        $tmp_where[]=" ORDER BY ".$auto_key." DESC LIMIT 1";
        $sql_opt=array('t'=>$table,'w'=>$tmp_where,'g'=>$auto_key,'o'=>1);
        $last_info=$baseModel->db_main->get_info_arr($sql_opt, $debug =0);
        if(!empty($last_info)&&is_numeric($last_info[$auto_key])){
            $auto_num=$last_info[$auto_key]+1;
        }

        return $auto_num;
    }
    public static function getXColumnArrByTableName($opt_obj){
        $table=$opt_obj['table'];
        $baseModel=$opt_obj['baseModel'];

        $db_name='';
        $table_name='';
        $tmp_tb_arr=explode(".",$table);
        if(count($tmp_tb_arr)==1){
            $table_name=$table;
        }else if(count($tmp_tb_arr)==2){
            $db_name=$tmp_tb_arr[0];
            $table_name=$tmp_tb_arr[1];
        }
        if(empty($db_name)){
            $db_name=$baseModel->db_main->conn_info['db_name'];
        }
        //기본컬럼 불러오기
        $x_column_arr=array();
        $tmp_where=array("AND TABLE_NAME='".$table_name."'");
        if(!empty($db_name)){
            $tmp_where[]="AND TABLE_SCHEMA='".$db_name."'";
        }
        $tmp_col='COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,COLUMN_KEY,COLUMN_COMMENT';
        $sql_opt=array('t'=>'INFORMATION_SCHEMA.columns','w'=>$tmp_where,'g'=>$tmp_col);
        $col_name_arr=$baseModel->db_main->get_info_arr($sql_opt, $debug =0);
        foreach($col_name_arr as $col_info){
            $x_column_arr[$col_info['COLUMN_NAME']]=array(
                'name'=>!empty($col_info['COLUMN_COMMENT'])?$col_info['COLUMN_COMMENT']:$col_info['COLUMN_NAME'],
                'type'=>$col_info['DATA_TYPE'],
                'length'=>$col_info['CHARACTER_MAXIMUM_LENGTH'],
                'pri'=>$col_info['COLUMN_KEY'],
                'width'=>'100'
            );
        }

        return $x_column_arr;
    }

    /*
    $tmp_rs=DBfunc::get_detail_by_x_column_arr($x_column_arr);
    $pri_col_arr=$tmp_rs['pri_col_arr'];
    $is_date_col_arr=$tmp_rs['is_date_col_arr'];
    $is_number_col_arr=$tmp_rs['is_number_col_arr'];
     */
    public static function getDetailByXColumnArr($x_column_arr){
        $pri_col_arr=array();
        $is_date_col_arr=array();
        $is_number_col_arr=array();
        $is_time_col_arr=array();
        foreach($x_column_arr as $key=>$val){
            if(!empty($val['pri'])){
                $pri_col_arr[]=$key;
            }
            $val['type']=strtolower($val['type']);
            if(strstr($val['type'],'(')!==false){
                $val['type']=explode($val['type'],'(')[0];
            }
            if(in_array($val['type'],array('date','datetime'))){
                $is_date_col_arr[]=$key;
            }
            if(in_array($val['type'],array('double','float','int','decimal'))){
                $is_number_col_arr[]=$key;
            }
            if($val['type']=='time'){
                $is_time_col_arr[]=$key;
            }
        }

        $result_data=array(
            'pri_col_arr'=>$pri_col_arr,
            'is_date_col_arr'=>$is_date_col_arr,
            'is_number_col_arr'=>$is_number_col_arr,
            'is_time_col_arr'=>$is_time_col_arr
        );

        return $result_data;
    }

    public static function getPriValStr($opt_arr){
        $pri_col_arr=$opt_arr['pri_col_arr'];
        $info=$opt_arr['info'];
        $pri_val_arr=array();
        foreach($pri_col_arr as $key){
            $pri_val_arr[]=$info[$key];
        }
        $pri_val_str=implode(",",$pri_val_arr);
        return $pri_val_str;
    }

    public static function printXColumnArr($x_column_arr){
        foreach($x_column_arr as $key=>$val){
            echo "'$key'=>array('name'=>'{$val['name']}','type'=>'{$val['type']}','length'=>'{$val['length']}','pri'=>'{$val['pri']}','width'=>'100'),";
            echo "<br />";
        }
    }
}
