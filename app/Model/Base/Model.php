<?php
namespace App\Model\Base;

use App\Lib\Web\Web;

class Model{
	public $mysqli=null;
	public $conn_info=array(
		'db_host'=>'',
		'db_user'=>'',
		'db_pass'=>'',
		'db_name'=>'',
		'db_charset'=>'',
		'db_port'=>''
	);
	function __construct(){
		//$this->connect(DB_HOST,DB_USER,DB_PASS,DB_NAME,DB_CHARSET);	//config.php 에서 정의돈 내용
	}

	function connect($db_host,$db_user,$db_pass,$db_name,$db_charset='utf-8',$db_port='3306'){
		$conn_result=array('result'=>'true','msg'=>'success');
		$this->conn_info=array(
			'db_host'=>$db_host,
			'db_user'=>$db_user,
			'db_pass'=>$db_pass,
			'db_name'=>$db_name,
			'db_charset'=>$db_charset,
			'db_port'=>$db_port
		);
		$this->mysqli=new \mysqli ($db_host,$db_user,$db_pass,$db_name,$db_port);
		if($this->mysqli->connect_error){
			$error_str=$this->mysqli->connect_error;
			$conn_result=array('result'=>'false','msg'=>$error_str);
			return $conn_result;
		}
		$this->mysqli->set_charset ($db_charset);

		return $conn_result;
	}
	/*
	$where_arr=array();
	$where_arr[]="AND cargo_date='2018-07-08'";
	$sql_opt=array('t'=>'cargo','w'=>$where_arr,'g'=>'*','o'=>0);
	$info_arr=get_info_arr($sql_opt, $debug = null);
	*/
	function get_info_arr($sql_opt, $debug = null){
		$info_arr = array ();
		if (empty ( $sql_opt ['t'] )) {
			return $info_arr;
		}
		$get_col = '*';
		if (! empty ( $sql_opt ['g'] )) {
			$get_col = $sql_opt ['g'];
		}
		$where_arr = array ();
		if (! empty ( $sql_opt ['w'] )) {
			$where_arr = $sql_opt ['w'];
		}

		$sql = 'SELECT ' . $get_col . ' FROM ' . $sql_opt ['t'] . ' WHERE 1=1';
		if (! empty ( $where_arr )) {
			foreach ( $where_arr as $row ) {
				$sql .= ' ' . $row . ' ';
			}
		}

		if (! empty ( $debug )) {
			echo '<br />debug[' . $debug . ']<br />';
			echo $sql;
		}
		$result = $this->excute ( $sql );

		if (! empty ( $debug )) {
			echo '<br />';
			if (! $result) {
				echo $this->mysqli->error;
				echo '<br />';
			}
		}

		if($result){
			while ( $info = mysqli_fetch_assoc ( $result ) ) {
				foreach($info as $key=>$val){
					$info[$key]=Web::checkHtml($val);
				}
				$info_arr [] = $info;
			}
		}
		if (! empty ( $sql_opt ['o'] )) {
			if (! empty ( $info_arr )) {
				$info_arr = $info_arr [0];
			}
		}

		return $info_arr;
	}//info_arr

	function get_error(){
		return $this->mysqli->error;
	}

	function excute($sql) {
		$result = mysqli_query ($this->mysqli, $sql);
		return $result;
	}

	function close(){
		mysqli_close($this->mysqli);
		$this->mysqli=null;
	}
	public function insert($sql_opt,$debug=false){
		$table=$sql_opt['table'];
		$col_val_arr=$sql_opt['col_val_arr'];
		$pri_col_val_arr=isset($sql_opt['pri_col_val_arr'])?$sql_opt['pri_col_val_arr']:array();
		$is_save_log=true;
		if(isset($sql_opt['is_save_log'])){
			$is_save_log=$sql_opt['is_save_log'];
		}
		//등록
		$col_arr=array();
		$val_arr=array();

		foreach($col_val_arr as $key=>$val){
			$col_arr[]=$key;
			$val_str="'".$val."'";
			if($val=='null'&&$val!='0'){
				$val_str='null';
			}
			$val_arr[]=$val_str;
		}

		$col_str=implode(",",$col_arr);
		$val_str=implode(",",$val_arr);

		$sql="INSERT INTO ".$table."(".$col_str.") VALUES(".$val_str.")";
		$result=$this->excute($sql);

		if(!empty($debug)){
			echo 'debug('.$debug.'):'.$sql;
			echo '<br />';
			echo $this->get_error();
			echo '<br />';
		}

		return $result;
	}

	public function update($sql_opt,$debug=false){
		$table=$sql_opt['table'];
		$col_val_arr=$sql_opt['col_val_arr'];
		$pri_col_val_arr=isset($sql_opt['pri_col_val_arr'])?$sql_opt['pri_col_val_arr']:array();
		$where_row=isset($sql_opt['w'])?$sql_opt['w']:array();
		$is_save_log=true;
		if(isset($sql_opt['is_save_log'])){
			$is_save_log=$sql_opt['is_save_log'];
		}

		if(empty($pri_col_val_arr)&&empty($where_row)){
			return false;
		}

		//수정 (키컬럼 제외)
		$up_arr=array();
		$up_where_arr=array();
		foreach($pri_col_val_arr as $key=>$val){
			$up_where_arr[]="AND ".$key."='".$val."'";
		}
		foreach($where_row as $w_str){
			$up_where_arr[]=$w_str;
		}

		foreach($col_val_arr as $key=>$val){
			$val_str="'".$val."'";
			if($val=='null'&&$val!='0'){
				$val_str='null';
			}
			$up_arr[]=$key."=".$val_str;
		}

		$up_str=implode(",",$up_arr);
		$sql="UPDATE ".$table." SET ".$up_str." WHERE 1=1";
		foreach($up_where_arr as $w_val){
			$sql.=" ".$w_val;
		}
		$result=$this->excute($sql);

		if(!empty($debug)){
			echo 'debug('.$debug.'):'.$sql;
			echo '<br />';
			echo $this->get_error();
			echo '<br />';
		}

		return $result;
	}

	public function duplicate_update($sql_opt,$debug=false){
		$table=$sql_opt['table'];
		$col_val_arr=isset($sql_opt['col_val_arr'])?$sql_opt['col_val_arr']:array();
		$up_col_val_arr=isset($sql_opt['up_col_val_arr'])?$sql_opt['up_col_val_arr']:$col_val_arr;
		$is_save_log=true;
		if(isset($sql_opt['is_save_log'])){
			$is_save_log=$sql_opt['is_save_log'];
		}
		if(empty($up_col_val_arr)){
			$up_col_val_arr=$col_val_arr;
		}

		$col_arr=array();
		$val_arr=array();
		$up_arr=array();

		foreach($col_val_arr as $key=>$val){
			$col_arr[]=$key;
			$val_str="'".$val."'";
			if($val=='null'&&$val!='0'){
				$val_str='null';
			}
			$val_arr[]=$val_str;
		}
		foreach($up_col_val_arr as $key=>$val){
			$val_str="'".$val."'";
			if($val=='null'&&$val!='0'){
				$val_str='null';
			}
			$up_arr[]=$key."=".$val_str;
		}

		$col_str=implode(",",$col_arr);
		$val_str=implode(",",$val_arr);
		$up_str=implode(",",$up_arr);

		$sql="INSERT INTO ".$table."(".$col_str.") VALUES(".$val_str.") ON DUPLICATE KEY UPDATE ".$up_str;
		$result=$this->excute($sql);

		if(!empty($debug)){
			echo 'debug('.$debug.'):'.$sql;
			echo '<br />';
			echo $this->get_error();
			echo '<br />';
		}

		return $result;
	}

	public function delete($sql_opt,$debug=false){
		$table=$sql_opt['table'];
		$pri_col_val_arr=isset($sql_opt['pri_col_val_arr'])?$sql_opt['pri_col_val_arr']:array();
		$where_row=isset($sql_opt['w'])?$sql_opt['w']:array();
		$is_save_log=true;
		if(isset($sql_opt['is_save_log'])){
			$is_save_log=$sql_opt['is_save_log'];
		}

		if(empty($pri_col_val_arr)&&empty($where_row)){
			return $where_row;
		}

		//수정 (키컬럼 제외)
		$up_where_arr=array();
		foreach($pri_col_val_arr as $key=>$val){
			$up_where_arr[]="AND ".$key."='".$val."'";
		}
		foreach($where_row as $w_str){
			$up_where_arr[]=$w_str;
		}

		$sql="DELETE FROM ".$table." WHERE 1=1";
		foreach($up_where_arr as $w_val){
			$sql.=" ".$w_val;
		}
		$result=$this->excute($sql);

		if(!empty($debug)){
			echo 'debug('.$debug.'):'.$sql;
			echo '<br />';
			echo $this->get_error();
			echo '<br />';
		}

		return $result;
	}
}
