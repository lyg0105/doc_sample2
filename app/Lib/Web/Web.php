<?php
namespace App\Lib\Web;

class Web
{
	public static function data($method_str,$name_str,$default_str,$opt_arr=array()){
		$method_str=strtolower($method_str);
		//is_array
		$data_str='';
		if(strstr($method_str,'get')!==false){
			//get
			$data_str=isset($_GET[$name_str])?$_GET[$name_str]:$default_str;
		}else{
			//post
			$data_str=isset($_POST[$name_str])?$_POST[$name_str]:$default_str;
		}

		$check_method_str='checkHtml';
		if($method_str=='get'||$method_str=='post'){
			$check_method_str='CheckHtmlStr';
		}

		$data_str=Web::$check_method_str($data_str);
		foreach ($opt_arr as $key=>$val){
			if($key=="is_number"){
				if(!empty($val)){
					$data_str=str_replace(",","",$data_str);//,를 지우는 함수
					if(empty($data_str)){
						$data_str="0";
					}else{
						$data_str=number_format($data_str,0,".","");//오른쪽에서 0번째(끝에)를 소수점으로 넣는 함수
					}
				}
			}
		}

		return $data_str;
	}

	public static function checkHtml($in_str){
		$replace_str_arr=array(
			'\\','\'','<script','<?','<meta',' 1=1 ','</script','?>'
		);
		$return_str=str_ireplace($replace_str_arr,'',$in_str);
		trim($return_str);
		return $return_str;
	}

	//사용자 입력값에대한 보안
	public static function CheckHtmlStr($contents){
		if(get_magic_quotes_gpc()){
			$contents=stripslashes($contents);
		}else{
			$contents=addslashes($contents);
		}
		$contents=htmlspecialchars($contents);
		$contents=str_replace('&amp;','&',$contents);
		//$contents=htmlentities($contents);  //htmlentities 이것은 한글을 깨지개해서 뺀다.
		$contents=strip_tags($contents);
		return $contents;
	}
	//MySQL 접속을 열 때
	public static function CheckMysqlStr($contents){
		$contents=mysql_real_escape_string($contents);
		$contents=Web::CheckHtmlStr($contents);
		return $contents;
	}

	public static function alert($msg,$loc,$is_exit=true){
		if(!empty($msg)){
			echo "<script>alert('".$msg."');</script>";
		}
		if(!empty($loc)){
			echo "<script>location.href='".$loc."';</script>";
		}
		if($is_exit){
			exit(0);
		}
	}

	public static function split_tel_number($tel_str){
		$tel_arr=array("","","");
		$tel_str=preg_replace("/[^0-9]*/s","",$tel_str);//숫자만 추출
		if(strlen($tel_str)==11){
			$tel_arr[0]=substr($tel_str,0,3);
			$tel_arr[1]=substr($tel_str,3,4);
			$tel_arr[2]=substr($tel_str,7,4);
		}else if(strlen($tel_str)==10){
			$tel_arr[0]=substr($tel_str,0,3);
			$tel_arr[1]=substr($tel_str,3,3);
			$tel_arr[2]=substr($tel_str,6,4);
		}else if(strlen($tel_str)==9){
			$tel_arr[0]=substr($tel_str,0,2);
			$tel_arr[1]=substr($tel_str,2,3);
			$tel_arr[2]=substr($tel_str,5,4);
		}else if(strlen($tel_str)==7){
			$tel_arr[0]="043";
			$tel_arr[1]=substr($tel_str,0,3);
			$tel_arr[2]=substr($tel_str,3,4);
		}

		return $tel_arr;
	}

	//사업자번호 포맷
	public static function get_format_of_busin($busin_str){
		$busin_str=str_replace("-","",$busin_str);
		$busin_str=trim($busin_str);
		$busin_str=preg_replace("/[^0-9]*/s","",$busin_str);//숫자만 추출
		$result_str="";
		if(strlen($busin_str)>=10){
			$result_str=substr($busin_str,0,3)."-".substr($busin_str,3,2)."-".substr($busin_str,5,6);
		}

		return $result_str;
	}

	//date_foramt
	public static function date_form($date,$format="Y-m-d",$defalut=""){
		$date_str=$defalut;
		$is_val=true;
		if(!empty($date)&&$date!="0000-00-00 00:00:00"&&$date!="0000-00-00"&&$date!="false"&&$date!="FALSE"){

		}else{
			$is_val=false;
		}
		if(preg_match('/^(\d{4})-(\d{2})-(\d{2})$/',substr($date,0,10),$match) && checkdate($match[2],$match[3],$match[1])){

		}else{
			$is_val=false;
		}
		if($is_val){
			$date = date_create($date);
			$date_str=date_format($date,$format);
		}
		return $date_str;
	}

	//확장자체크
	public static function check_file_extension($ext){
		$DenyArr = Array('exe','html', 'htm', 'php','avi','dll');
		$ext = strtolower($ext);
		$is_val=true;
		if(in_array($ext,$DenyArr)){
			$is_val=false;
		}
		return $is_val;
	}

	public static function get_now_folder_src($now_path,$except_str){
		$now_folder_src=str_replace('\\','/',$now_path);
		$now_folder_src=str_replace($except_str,'',$now_folder_src);
		$now_folder_arr=explode('/',$now_folder_src);
		$now_folder_src=str_replace(end($now_folder_arr),'',$now_folder_src);

		return $now_folder_src;
	}
	public static function isImageFileByExt($ext){
		$ext_arr=array("bib","jpeg","png","bmp","gif","jpe","jpg","tif","tiff","jfif","bmp");
		$result=false;
		$ext=strtolower($ext);
		if(in_array($ext,$ext_arr)!==false){
			$result=true;
		}
		return $result;
	}
	public static function isSecure(){
		return
		(!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
		|| $_SERVER['SERVER_PORT'] == 443;
	}

	public static function get_client_ip_address(){
		$ipaddress = '';
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ipaddress = $_SERVER['HTTP_CLIENT_IP'];
		}else if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}else if(!empty($_SERVER['HTTP_X_FORWARDED'])){
			$ipaddress = $_SERVER['HTTP_X_FORWARDED'];
		}else if(!empty($_SERVER['HTTP_FORWARDED_FOR'])){
			$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
		}else if(!empty($_SERVER['HTTP_FORWARDED'])){
			$ipaddress = $_SERVER['HTTP_FORWARDED'];
		}else if(!empty($_SERVER['REMOTE_ADDR'])){
			$ipaddress = $_SERVER['REMOTE_ADDR'];
		}else{
			$ipaddress = 'UNKNOWN';
		}
		return $ipaddress;
	}
}
?>
