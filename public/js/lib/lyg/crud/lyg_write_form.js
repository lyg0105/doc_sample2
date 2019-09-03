var LygWriteForm=function(opt_obj){

    /*
    tmp_opt_obj={
        'url':'',
        'form_data':{},
        'callBackfunc':function(data){},
        'is_confirm':true,
        'confirm_msg':''
    }
    */
    this.requestAjax=function(tmp_opt_obj){
        var is_val=true;
        var method="post";
        var url="";
        var form_data={};
        var callBackfunc=function(data){
            alert(data['msg']);
            if(data['result']=='true'){
                location.reload();
            }
        };
        var is_confirm=false;
        var confirm_msg="등록 하시겠습니까?";
        if(tmp_opt_obj["method"]!=undefined){method=tmp_opt_obj["method"];}
        if(tmp_opt_obj["url"]!=undefined){url=tmp_opt_obj["url"];}
        if(tmp_opt_obj["form_data"]!=undefined){form_data=tmp_opt_obj["form_data"];}
        if(tmp_opt_obj["callBackfunc"]!=undefined){callBackfunc=tmp_opt_obj["callBackfunc"];}
        if(tmp_opt_obj["is_confirm"]!=undefined){is_confirm=tmp_opt_obj["is_confirm"];}
        if(tmp_opt_obj["confirm_msg"]!=undefined){confirm_msg=tmp_opt_obj["confirm_msg"];}
        if(is_confirm){
            is_val=confirm(confirm_msg);
        }

        if(is_val){
            $.ajax({
        		type:method,
        		url:url,
        		dataType:'json',
        		data: form_data,
        		error:function(data){
        			console.log("lyg_write_form.js Error ",data);
        		},
        		success:callBackfunc
        	});
        }
    };

    this.get_form_data_by_check_box=function(form_json_data,opt_obj){
    	//체크된놈 값 모으기
        var this_obj=this;
    	$(".chk_box:checked").each(function(i,e){
    		var par_obj=$(e).parent().parent().parent();
    		form_json_data=this_obj.get_form_data_by_tr_obj(form_json_data,par_obj,opt_obj);
    	});
    	return form_json_data;
    };
    this.get_form_data_by_tr_obj=function(form_json_data,tr_obj,opt_obj){
    	if(opt_obj==undefined){opt_obj={};}

    	var par_obj=tr_obj;
    	var col_val_arr={};
    	var is_pri_update=false;
    	if(opt_obj['is_pri_update']!=undefined){
    		is_pri_update=opt_obj['is_pri_update'];
    	}

    	//row_input 데이터 모으기
    	col_val_arr['input_row_num']=$(par_obj).find(".row_num").val();
    	$(par_obj).find(".row_input").each(function(idx,ele){
    		var key_str=$(ele).attr("class").split(" ")[0];
    		col_val_arr[key_str]=$(ele).val();
    	});

    	//키값모으기
    	var pri_col_arr=opt_obj['pri_col_arr'];
    	var chk_box_str=$(par_obj).find(".chk_box").val();
    	var key_val_arr=chk_box_str.split(",");
    	if(is_pri_update==false){
    		for(var i=0;i<pri_col_arr.length;i++){
    			if(key_val_arr[i]!=undefined){
    				col_val_arr[pri_col_arr[i]]=key_val_arr[i];
    			}
    		}
    	}else{
    		//키값도 수정하려면 w_val_arr 넣는다.
    		if(form_json_data['w_key_arr']==undefined){form_json_data['w_val_arr']=pri_col_arr;}
    		if(form_json_data['w_val_arr']==undefined){form_json_data['w_val_arr']=[];}
    		form_json_data['w_val_arr'].push(key_val_arr);
    	}

    	for(var key_str in col_val_arr){
    		if(form_json_data[key_str]==undefined){
    			form_json_data[key_str]=[];
    		}

    		form_json_data[key_str].push(col_val_arr[key_str]);
    	}

    	return form_json_data;
    };
};
