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

    this.getUrlParams=function(){
        var params = {};
        var url_str=decodeURI(location.href);
        var url_split_arr=url_str.split("?");
        if(url_split_arr.length==1){return params;}
        var query_arr=url_split_arr[1].split("&");
        for(var i=0;i<query_arr.length;i++){
            var key_val_arr=query_arr[i].split("=");
            var key=key_val_arr[0];
            var val=key_val_arr[1];
            if(key.indexOf("[")!=-1){
                var key_arr=key.split("[");
                var key_main=key_arr[0];
                var key_sub=key_arr[1].split("]")[0];
                if(params[key_main]==undefined){params[key_main]={};}
                params[key_main][key_sub]=key_val_arr[1];
            }else{
                params[key_val_arr[0]]=key_val_arr[1];
            }
        }
        return params;
    }

    this.getFormDataToJson=function(opt_obj){
        var form=opt_obj["form"];
        this.setSearchInputColumnToName();
        var form_json_data=$(form).serialize();
        $(".sc_input").attr("name","");
        return form_json_data;
    };

    this.setSearchInputColumnToName=function(){
        $(".sc_input").each(function(idx,ele){
            if($(ele).val()!=""){
                var sc_key=$(ele).attr("search_key");
                sc_key=sc_key.replace("sc_","");
                $(ele).attr("name","sc["+sc_key+"]");
            }
        });
    };

    this.setHtmlFormDataByUrlData=function(){
        var url_data=this.getUrlParams();
        for(var key in url_data){
            list_opt_arr[key]=url_data[key];
        }
        for(var key in list_opt_arr){
            if(key=="sc"){
                var sc_arr=list_opt_arr[key];
                for(var sc_key in sc_arr){
                    var sc_val=sc_arr[sc_key];
                    $(".sc_input").each(function(idx,ele){
                        if($(ele).attr("search_key").replace("sc_","")==sc_key){
                            $(ele).val(sc_val);
                        }
                    });
                }
            }else{
                $("#"+key).val(list_opt_arr[key]);
            }
        }
    };

    this.gopage=function(nowpage){
        this.setSearchInputColumnToName();
        $("#form #now_page").val(nowpage);
        $("#form").attr("method","get");
        //$("#form #flag").val("list");
        $("#form").submit();
    }
};
