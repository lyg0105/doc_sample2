//ALL selected
function select_allcheck(str) {
	$("."+str).each(function(i,e){
		if($(e).prop("checked")){
			$(e).prop("checked",false);
		}else{
			$(e).prop("checked",true);
		}
	});
}

function goDelete(table,column,seqs,url,ret_url){
	//seqs 는 @ 로 구분된다.
	var msg="정말로 삭제 하시겠습니까?";
	if (confirm(msg)){
		$.ajax({
			type:"post",
			url:url,
			dataType:'json',
			data: {"co_seq":seqs,"table":table,"column":column},
			error:function(data){
				alert("에러");
			},
			success:function(data){
				alert(data['msg']);
				if(data['result']=='true'){
					location.reload();
				}
			}
		});
	}
}

function gopage(nowpage){
	$("#form #now_page").val(nowpage);
	$("#form").attr("method","get");
	//$("#form #flag").val("list");
	$("#form").submit();
}


function goToMode(flag,primary_key,seq){
	// 폼이 없으면 화면에 폼을 생성한다.
	if( $("#form").length == 0) {
		$('body').append('<form id="form" name="form" method="GET"  >');
	}
	// 키 필드가 없으면 화면에 키필드를 생성한다.
	if( $("#form #"+primary_key ).length == 0) {
		$('#form').append('<input type="hidden" name='+primary_key+' id='+primary_key+' value="'+seq+'" />');
	}else{
		$("#form #"+primary_key ).val(seq);
	}
	// flag 필드가 없으면 화면에 키필드를 생성한다.
	if($("#form #flag").length==0){
		$('#form').append('<input type="hidden" name="flag" id="flag" value="'+flag+'" />');
	}else{
		$("#form #flag").val(flag);
	}


	$("#form").submit();
}

//receive_seq는 @로 구분 배열
function go_push_array(push_data){
	if(push_data==undefined){push_data={};}
	var tmp_data={};
	tmp_data['url']='';
	tmp_data['push_hp']=[];
	tmp_data['push_gub']='';
	tmp_data['push_gub2']='';
	tmp_data['push_title']='';
	tmp_data['push_sub_title']='';
	tmp_data['push_memo']='';
	tmp_data['push_url']='';
	tmp_data['comfirm_msg']='';
	tmp_data['no_refresh']='';
	for(var key in push_data){
		tmp_data[key]=push_data[key];
	}

	var is_val=true;

	if(tmp_data['url']==''){
		alert("주소정보가 없습니다.");
		is_val=false;
	}else if(tmp_data['push_hp'].length==0){
		alert("수신자 정보가 없습니다.");
		is_val=false;
	}

	if(is_val){
		if(tmp_data['comfirm_msg']!=undefined&&tmp_data['comfirm_msg']!=""){
			is_val=confirm(tmp_data['comfirm_msg']);
		}else{
			is_val=true;
		}
	}

	var go_data={};
	for(var key in tmp_data){
		if(key!='url'&&key!='confirm_msg'&&key!='no_refresh'){
			go_data[key]=tmp_data[key];
		}
	}

	if(is_val){
		$.ajax({
			type:"post",
			url: tmp_data['url'],
			dataType:'json',
			data: go_data,
			error: function(data){
				alert("에러");
			},
			success:function(data){
				var json_obj=data;
				if(tmp_data['confirm_msg']!=''){
					alert(json_obj['msg']);
				}
				if(json_obj['error']!=undefined){
					if(json_obj['error'].length>0){
						var error_msg_arr=[];
						for(var i=0;i<json_obj['error'].length;i++){
							var tmp_hp=json_obj['error'][i]['row_num'];
							var tmp_msg=json_obj['error'][i]['msg'];
							error_msg_arr.push(tmp_hp+":"+tmp_msg);
						}
						var error_msg_str=error_msg_arr.join("\n");
						alert(error_msg_str);
					}
				}
				if(json_obj['result']=="true"||json_obj['result']==true){
					if(tmp_data['no_refresh']!=''){
						location.reload();
					}
				}else{

				}
			}
		});
	}
}

function goUpdate_Common_infos(table,column,seqs,up_column,up_values,url,ret_url,msg){
	//seqs 는 @ 로 구분된다.  up_column,up_values 도 마찬가지
	var is_val=false;
	if(msg!=undefined){
		is_val=confirm(msg);
	}else{
		is_val=true;
	}
	if(is_val){
		$.ajax({
			type:"post",
			url: url,
			dataType:'json',
			data: {"co_seq":seqs,"table":table,"column":column,"up_column":up_column,"up_val":up_values},
			error: function(data){
				alert("oUpdate에러");
			},
			success:function(data){
				 if(data == true||data == 'true'){
					if(msg!=undefined){
						alert("변경되었습니다.");
					}
					location.href=ret_url;
				}else{
					if(msg!=undefined){
						alert(data);
						alert("오류입니다.");
					}
					location.href=ret_url;
				}
			}
		});
	}
}

function openPopup(url,width,height,pop_name,auto_size){
	if(pop_name==undefined){pop_name="popup";}
	var _width = width;
	var _Height = height;
	if(_width =="" ) _width = 640;
	if(_Height =="" ) _Height = 640;

	var win_w=(window.innerWidth || document.documentElement.clientWidth);
	var win_h=(window.innerHeight || document.documentElement.clientHeight);
	if(auto_size!=undefined){
		if(auto_size){
			if(_width>win_w){
				_width=win_w;
			}
			if(_Height>win_h){
				_Height=win_h;
			}
		}
	}

	var my_win=window.open(url,pop_name, 'top=' + (win_h/2 - _Height/2)+', left=' + (win_w/2 - _width/2)+', width=' + _width +', height=' + _Height + ', directories=yes, scrollbars=1, status=no, resizable=yes');
	my_win.focus();
}

//날짜 선택 폼
function date_picker(element,format){
	$(element).datepicker( {
		dateFormat: format,
		changeMonth: true,
		changeYear:true,
		showMonthAfterYear: true ,
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		minDate: '-100y',
		yearRange: 'c-100:c+10',
		showAnim: "slide",
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월']
		 //closeText: '닫기',
		 //showButtonPanel: true
	});
}
function date_picker_btn(element,format,btn_root){
	$(element).datepicker( {
		showOn: "button",
		buttonImage: btn_root,
		buttonImageOnly: true,
		buttonText: "Select date",
		dateFormat: format,
		changeMonth: true,
		changeYear:true,
		showMonthAfterYear: true ,
		dayNamesMin: ['일', '월', '화', '수', '목', '금', '토'],
		minDate: '-100y',
		yearRange: 'c-100:c+10',
		showAnim: "toggle",
		monthNamesShort: ['1월','2월','3월','4월','5월','6월','7월','8월','9월','10월','11월','12월']
		 //closeText: '닫기',
		 //showButtonPanel: true
	});
}

function str_replace(search,replace,content){
	while(content.indexOf(search)!=-1){
		content=content.replace(search,replace);
	}
	return content;
}

//쿠키 생성
//쿠키이름 : 쿠키이름을 영문으로 넣어주세요.
//쿠키값 : 쿠키의 값을 문자열로 넣어주세요.
//만료일 : 쿠키의 만료일을 숫자로 넣어주세요
//예) 만료일이 1 이면 하루동안 지속되는 쿠키입니다.
//예) 만료일이 -1 이면 쿠키가 삭제됩니다.
function setCookie(cName, cValue, cDay){
  var expire = new Date();
  expire.setDate(expire.getDate() + cDay);
  cookies = cName + '=' + encodeURI(cValue) + '; path=/ '; // 한글 깨짐을 막기위해 escape(cValue)를 합니다.
  if(typeof cDay != 'undefined') cookies += ';expires=' + expire.toGMTString() + ';';
  document.cookie = cookies;
}
//쿠키 가져오기
function getCookie(cName){
  cName = cName + '=';
  var cookieData = document.cookie;
  var start = cookieData.indexOf(cName);
  var cValue = '';
  if(start != -1){
	  start += cName.length;
	  var end = cookieData.indexOf(';', start);
	  if(end == -1)end = cookieData.length;
	  cValue = cookieData.substring(start, end);
  }
  return  decodeURI(cValue);
}
//콤마찍기
function comma(str){
	str=String(str);
	if(str==""){str="0";}
	var is_minus=false;
	var is_decimal=false;
	var un_decimal_str="";
	//set Minus
	var tmp_str_arr=str.split("-");
	if(tmp_str_arr.length==2){
		if(tmp_str_arr[0]==""){
			str=tmp_str_arr[1];
			is_minus=true;
		}
	}
	//set Decimal
	tmp_str_arr=str.split(".");
	if(tmp_str_arr.length==2){
		//if(tmp_str_arr[1]!=""){
			is_decimal=true;
			str=tmp_str_arr[0];
			un_decimal_str=tmp_str_arr[1];
			un_decimal_str=un_decimal_str.substr(0,1);
		//}
	}
	//check number
	str=Number(str);
	if(str==Number.NaN){
		str="0";
	}

	//set comma
	str = String(str);
	str=str.replace(/(\d)(?=(?:\d{3})+(?!\d))/g,'$1,');

	if(is_minus){
		str="-"+str;
	}

	if(is_decimal){
		str=str+"."+un_decimal_str;
	}
	return str;
}
//콤마풀기
function uncomma(str) {
	str = String(str);
	str=str.replace(/[^0-9.-]/gi,"");
	if(str==""){str="0";}

	var tmp_str_arr=str.split("-");
	if(tmp_str_arr.length==2){
		if(tmp_str_arr[0]!=""){
			str=tmp_str_arr[1];
		}else{
			str="-"+tmp_str_arr[1];
		}
	}

	tmp_str_arr=str.split(".");
	if(tmp_str_arr.length==2){
		if(tmp_str_arr[1]==""){
			str=tmp_str_arr[0];
		}
	}

	return str.replace(/[^0-9.-]/gi,"");
}

function get_frameWindow_by_name(f_name){
	var win=null;
	for(var i=0;i<window.frames.length;i++){
		if(window.frames[i].name==f_name){
			win=window.frames[i];
		}
	}
	return win;
}
//openLayerPopup({'url':'target_popup.html','width':1024,'height':420,'pop_name':'write','title':'화물등록','title_location':'top'});
//{'is_overflow':true}
//parent.$("#layerpop_div_"+pop_name).remove();
function openLayerPopup(opt_json){
	var url_str=opt_json['url'];
	var width=opt_json['width'];
	var height=opt_json['height'];
	var win_w=(window.innerWidth || document.documentElement.clientWidth);
	var win_h=(window.innerHeight || document.documentElement.clientHeight);
	if(opt_json['auto_size']!=undefined){
		if(opt_json['auto_size']){
			if(width>win_w){
				width=win_w;
			}
			if(height>win_h){
				height=win_h;
			}
		}
	}
	var x=(win_w/2 - width/2);
	if(opt_json['x']!=undefined){
		x=opt_json['x'];
	}
	var y=(win_h/2 - height/2);
	if(opt_json['y']!=undefined){
		y=opt_json['y'];
	}
	var pop_name='';
	if(opt_json['pop_name']!=undefined){
		pop_name=opt_json['pop_name'];
	}
	var title='LayerPopup';
	if(opt_json['title']!=undefined){
		title=opt_json['title'];
	}
	var title_location='top';//top,bottom,none
	if(opt_json['title_location']!=undefined){
		title_location=opt_json['title_location'];
	}
	var frame_overflow_css="overflow:auto;";
	var layer_content_max_height=height+4;
	if(opt_json['is_overflow']!=undefined){
		if(opt_json['is_overflow']==false){
			frame_overflow_css="overflow:hidden;";
			layer_content_max_height=height;
		}
	}

	if($("#layerpop_div_"+pop_name).length==0){
		var layer_w=width;
		var layer_h=height;
		var layer_style="style='overflow:hidden;width:"+layer_w+"px;min-height:"+layer_h+"px;top:"+y+"px;left:"+x+"px;padding:0px;margin:0px;border:1px solid #000;display:block;position:fixed;z-index:999; background:#fff;'";
		var layer_top_style="style='line-height:14px;cursor:move;position:relative;background:#4c4c4c;text-align:center;height:21px;color:#fff;font-size:16px;line-height: 18px;'";
		var x_bnt_style="style='position:absolute;right:1px;display:inline-block;background:#c12583;color:#fff;padding: 1px 7px 1px 7px;border-radius: 4px;cursor: pointer;'";
		var title_bar_str=
			"<div class='layerpop_top' "+layer_top_style+" >" +
				'<span class="layer_title" style="font-size:13px;">'+title+'</span>'+
				"<a class='layer_close_btn' onclick='$(this).parent().parent().remove();' "+x_bnt_style+" ><b>X</b></a>"+
			"</div>";
		var append_div=
			"<div class='layerpop_div' id='layerpop_div_"+pop_name+"' "+layer_style+" >"+
				"<div class='layerpop_content' style='max-height:"+layer_content_max_height+"px;"+frame_overflow_css+"' >"+
					"<iframe src='"+url_str+"' name='"+pop_name+"' width='"+width+"px' height='"+height+"px' scrolling='no' frameborder='0' style='padding:0px;margin:0px;' ></iframe>"+
				"</div>"+
			"</div>";
		$("body").prepend(append_div);

		var last_obj=$("#layerpop_div_"+pop_name);

		if(title_location=='top'){
			$(last_obj).prepend(title_bar_str);
		}else if(title_location=='bottom'){
			$(last_obj).append(title_bar_str);
		}else{

		}

		var agt = isBrowserCheck();
		var is_drag=true;
		if (agt.indexOf('msie')!=-1){
			var ver=agt.replace('msie','');
			ver=parseInt(ver);
			if(ver<9){
				is_drag=false;
			}
		}
		$(".layerpop_div").css("z-index", 1);
		$(last_obj).css("z-index", 2);
		if(is_drag){
			$(last_obj).draggable({
				start: function ( e, ui ) {
					$( ".ui-draggable" ).not( ui.helper.css( "z-index", "2" ) )
						.css( "z-index", "1" );
					$(e).css("z-index", "2");
				}
			});
//			$(last_obj).resizable({
//				resize:function(event,ui){
//					console.log(ui);
//					var tmp_w=ui.size.width;
//					var tmp_h=ui.size.height;
//					$(last_obj).find("iframe").eq(0).css("width",tmp_w+"px");
//					$(last_obj).find("iframe").eq(0).css("height",tmp_h+"px");
//					$(last_obj).find(".layerpop_content").css("max-height",tmp_h+"px");
//				}
//			});
		}
	}else{
		$("#layerpop_div_"+pop_name).find("iframe").attr("src",url_str);
	}
}

function start_progress_img(img_url){
	$("#div_ajax_load_image").remove();
	 var width = 0;
	 var height = 0;
	 var left = 0;
	 var top = 0;

	 width = 100;
	 height = 100;
	 top = ( $(window).height() - height ) / 2 + $(window).scrollTop();
	 left = ( $(window).width() - width ) / 2 + $(window).scrollLeft();

	var img_style_str='style="position:absolute;top:' + top + 'px; left:' + left + 'px; width:' + width + 'px; height:' + height + 'px; z-index:9999;filter:alpha(opacity=50); opacity:alpha*0.5; margin:auto; padding:0; "';
	$('body').append('<div id="div_ajax_load_image" '+img_style_str+'><img src="'+img_url+'" style="width:'+width+'px;height:'+height+'px;" onclick="$(this).remove();" ></div>');
	if($("#div_ajax_load_image").length != 0) {
		$("#div_ajax_load_image").css({
			"top": top+"px",
			"left": left+"px"
		});
		$("#div_ajax_load_image").show();
	}
}
function stop_progress_img(){
	$("#div_ajax_load_image").remove();
}

function str_in_array(search_str,str_arr){
	var is_match=-1;
	for(var i=0;i<str_arr.length;i++){
		if(str_arr[i]==search_str){
			is_match=true;
		}
	}
	return is_match;
}

function strip_tag(str){
	return str.replace(/(<([^>]+)>)/ig,"");
}

function autoHypenPhone(str){//02번호, 업체번호, 자동 하이픈 넣기
	str = str.replace(/[^0-9]/g, '');
	var tmp = '';
	var num02=0;
	var num02_1=0;
	if(str.substr(0,2)=="02"){
		num02=1;
	}else{
		num02=0;
	}
	if( str.length < 4-num02){
		return str;
	}else if(str.length < 7){
		tmp += str.substr(0, 3-num02);
		tmp += '-';
		tmp += str.substr(3-num02);
	}else if(str.length < 11){
		if(str.substr(0,2)=="02" && str.length>=10){
			num02_1=1;
			str=str.substr(0,11-num02);
		}else{
			num02_1=0;
		}
		tmp += str.substr(0, 3-num02);
		tmp += '-';
		tmp += str.substr(3-num02, 3+num02_1);
		tmp += '-';
		tmp += str.substr(6-num02+num02_1);

 		if(str.length==8){
			tmp='';
			tmp += str.substr(0, 4);
			tmp += '-';
			tmp += str.substr(4);
		}
	}else{
		if(str.substr(0,2)=="02" && str.length>=10){
			num02_1=1;
			str=str.substr(0,11-num02);
		}else{
			num02_1=0;
		}
		str=str.substr(0,11-num02);
		tmp += str.substr(0, 3-num02);
		tmp += '-';
		tmp += str.substr(3-num02, 4);
		tmp += '-';
		tmp += str.substr(7-num02);
	}
	return tmp;
}

function autoHypenbusin_num(str){//사업자 번호 '-'없이 숫자만 입력하라고 요청.
	str = str.replace(/[^0-9]/g, '');
	var tmp = '';
	if( str.length < 4){
		return str;
	}else if(str.length < 7){
		tmp += str.substr(0, 3);
		tmp += '-';
		tmp += str.substr(3);
	}else if(str.length < 11){
		tmp += str.substr(0, 3);
		tmp += '-';
		tmp += str.substr(3, 2);
		tmp += '-';
		tmp += str.substr(5);
	}else{
		str=str.substr(0,10);
		tmp += str.substr(0, 3);
		tmp += '-';
		tmp += str.substr(3, 2);
		tmp += '-';
		tmp += str.substr(5);
	}
	if(str.length ==10){
		var busin_check="137137135";
		var busin_last_num=0;
		var check_sum=0;
		var no=tmp.replace(/-/g,'');
		for(var i=0; i<9; i++) {
			var multiply_check = Number(busin_check.charAt(i)) * Number(no.charAt(i));
			if(i < 8) {
				check_sum += multiply_check;
			}else{
				check_sum += Number(String(multiply_check).charAt(0)) + Number(String(multiply_check).charAt(1));
				busin_last_num = (10 - (check_sum % 10)) % 10; // (10 - (체크섬 % 10)) % 10
			}
		}
		if(busin_last_num!=no.charAt(9)){
			//alert("사업자 번호가 유효하지 않습니다.");
		}
	}
	return tmp;
}
function isAlpha(str) {
	var pattern = /[a-zA-Z]+/;
	return (pattern.test(str)) ? true : false;
}

/**
 * Return an object with the selection range or cursor position (if both have the same value)
 * @param {DOMElement} el A dom element of a textarea or input text.
 * @return {Object} reference Object with 2 properties (start and end) with the identifier of the location of the cursor and selected text.
 **/
function getInputSelection(el){
	var start = 0, end = 0, normalizedValue, range, textInputRange, len, endRange;

	if(el.nodeName=="SELECT"){
		return {
			start: start,
			end: end
		};
	}

	if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
		start = el.selectionStart;
		end = el.selectionEnd;
	} else {
		range = document.selection.createRange();

		if (range && range.parentElement() == el) {
			len = el.value.length;
			normalizedValue = el.value.replace(/\r\n/g, "\n");

			// Create a working TextRange that lives only in the input
			textInputRange = el.createTextRange();
			textInputRange.moveToBookmark(range.getBookmark());

			// Check if the start and end of the selection are at the very end
			// of the input, since moveStart/moveEnd doesn't return what we want
			// in those cases
			endRange = el.createTextRange();
			endRange.collapse(false);

			if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
				start = end = len;
			} else {
				start = -textInputRange.moveStart("character", -len);
				start += normalizedValue.slice(0, start).split("\n").length - 1;

				if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
					end = len;
				} else {
					end = -textInputRange.moveEnd("character", -len);
					end += normalizedValue.slice(0, end).split("\n").length - 1;
				}
			}
		}
	}

	return {
		start: start,
		end: end
	};
}

function isBrowserCheck(){
	var agt = navigator.userAgent.toLowerCase();
	if (agt.indexOf("chrome") != -1) return 'chrome';
	if (agt.indexOf("opera") != -1) return 'opera';
	if (agt.indexOf("staroffice") != -1) return 'staroffice';
	if (agt.indexOf("webtv") != -1) return 'webtv';
	if (agt.indexOf("beonex") != -1) return 'beonex';
	if (agt.indexOf("chimera") != -1) return 'chimera';
	if (agt.indexOf("netpositive") != -1) return 'netpositive';
	if (agt.indexOf("phoenix") != -1) return 'phoenix';
	if (agt.indexOf("firefox") != -1) return 'firefox';
	if (agt.indexOf("safari") != -1) return 'safari';
	if (agt.indexOf("skipstone") != -1) return 'skipstone';
	if (agt.indexOf("netscape") != -1) return 'netscape';
	if (agt.indexOf("msie") != -1) { // 익스플로러 일 경우
		var rv = -1;
		if (navigator.appName == 'Microsoft Internet Explorer') {
			var ua = navigator.userAgent;
			var re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
			if (re.exec(ua) != null) rv = parseFloat(RegExp.$1);
		}
		return 'msie'+rv;
	}
	if (agt.indexOf("mozilla/5.0") != -1) return 'mozilla';
}

//scrollMoveToTarget({'base_obj':'#div','focus_obj':'#input'});
function scrollMoveToTarget(opt_obj){
	if(opt_obj==undefined){opt_obj={};}
	base_obj=opt_obj['base_obj'];
	focus_obj=opt_obj['focus_obj'];
	if(!base_obj||!focus_obj){
		console.log("기본 세팅값이 없습니다.",base_obj,focus_obj);
		return false;
	}
	var offset = $(focus_obj).position();//position
	var top_num=$(base_obj).scrollTop()+offset.top;
	var left_num=$(base_obj).scrollLeft()+offset.left;
	var scroll_w=$(base_obj)[0].scrollWidth;
	var target_w=$(focus_obj).innerWidth();
	var div_w=$(base_obj).innerWidth();
	var scroll_h=$(base_obj)[0].scrollHeight;
	var target_h=$(focus_obj).innerHeight();
	var div_h=$(base_obj).innerHeight();

	if((div_w-target_w)>left_num){
		$(base_obj).scrollLeft(0);
	}else{
		$(base_obj).scrollLeft(left_num);//+target_w
	}
	if((div_h-(target_h*2))>top_num){
		$(base_obj).scrollTop(0);
	}else{
		$(base_obj).scrollTop(top_num-div_h+(target_h*2));
	}
}

//date json
function get_date_json(d_obj){
	if(d_obj==undefined){
		return d_obj=new Date();
	}
	var rs_json={
		'Y':d_obj.getFullYear(),
		'm':d_obj.getMonth()+1,
		'd':d_obj.getDate(),
		'h':d_obj.getHours(),
		'i':d_obj.getMinutes(),
		's':d_obj.getSeconds(),
		'day':d_obj.getDay(),
		'date_obj':d_obj
	};
	rs_json['last_day']=(new Date(rs_json.Y,rs_json.m,0)).getDate();
	rs_json['t']=rs_json['last_day'];
	rs_json['first_day_of_week']=(new Date(rs_json.Y,rs_json.m-1,1)).getDay();

	return rs_json;
}

function get_change_date(d_obj,type,num){
	var d_json=this.get_date_json(d_obj);

	if(type=='year'){
		d_obj.setFullYear(d_json.Y+num);
	}else if(type=='month'){
		d_obj.setMonth(d_json.m+num-1);
	}else if(type=='day'){
		d_obj.setDate(d_json.d+num);
	}

	return d_obj;
}

function get_date_format(d_obj,format_str){
	if(typeof d_obj!="object"){
		return "";
	}
	var d_json=get_date_json(d_obj);
	var f_arr=[];
	for(var key in d_json){
		if(key!='day'){
			f_arr.push(key);
		}
	}
	var rs_str='';
	for(var i=0;i<format_str.length;i++){
		var tmp_c=format_str.charAt(i);
		if(str_in_array(tmp_c,f_arr)!=-1){
			tmp_c=get_digit_str(d_json[tmp_c]);
		}
		rs_str+=tmp_c;
	}

	return rs_str;
}
function get_digit_str(num_str){
	if(num_str<10){
		num_str='0'+num_str;
	}
	num_str=num_str+'';
	return num_str;
}
