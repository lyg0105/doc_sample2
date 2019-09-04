var LygGridTable=function(opt_obj){
    this.table_obj=null;
    this.opt_obj={
        "grid_table_id":"",
        "x_column_list_arr":{},
        "x_column_list_orig_arr":{},
        "x_pri_col_arr":[],
        "x_basic_col_arr":[],
        "x_view_col_arr":[],
        "is_number_col_arr":[],
        "is_tel_col_arr":[],
        "is_busin_col_arr":[],
        "is_date_col_arr":[],
        "select_col_arr":{},

        "grid_event_obj":null
    };
    this.tr_row_num=0;
    this.init=function(){
        if(opt_obj==undefined){return false;}
        for(var key in opt_obj){
            this.opt_obj[key]=opt_obj[key];
        }
        this.createTable();
        this.setTheadSearch();
        this.setTheadName();
    };

    this.createTable=function(){
        if($(this.opt_obj.grid_table_id).find(".tr_head").length==0){
            var append_str=
                "<div class='board_list tr_head_table' >"+
                    "<table style='width:auto;'>"+
                        "<thead class='tr_head'>"+

                        "</thead>"+
                    "</table>"+
                "</div>";
            $(this.opt_obj.grid_table_id).append(append_str);
        }
        if($(this.opt_obj.grid_table_id).find(".tr_body").length==0){
            var append_str=
                "<div class='tr_body_table board_list' >"+
                    "<table style='width:auto;' >"+
                        "<tbody class='tr_body' >"+

                        "</tbody>"+
                    "</table>"+
                "</div>";
            $(this.opt_obj.grid_table_id).append(append_str);
        }
    };
    this.setTheadSearch=function(){
        var th_str_arr=[];
        for(var key in this.opt_obj.x_column_list_arr){
            var col_obj=this.opt_obj.x_column_list_arr[key];
            var tmp_th_str=
                "<th>"+
                    "<div class='column_td column_th' style='width:"+col_obj['width']+"px;' >"+
                        "<input type='text' class='sc_input search_input' search_key='sc_"+key+"' style='width:98%;' />"+
                    "</div>"+
                "</th>";
            th_str_arr.push(tmp_th_str);
        }
        th_str_str=th_str_arr.join("");
        var append_str=
            "<tr>"+
                "<td colspan='2'>"+
                    "<div class='column_td'>"+
                        "검색"+
                    "</div>"+
                "</td>"+
                th_str_str+
            "</tr>";
        $(this.opt_obj.grid_table_id).find(".tr_head").append(append_str);
    };
    this.setTheadName=function(){
        var th_str_arr=[];
        for(var key in this.opt_obj.x_column_list_arr){
            var col_obj=this.opt_obj.x_column_list_arr[key];
            var tmp_th_str=
                "<th>"+
                    "<div class='column_td column_th' style='width:"+col_obj['width']+"px;cursor:pointer;' >"+
                        col_obj["name"]+
                    "</div>"+
                "</th>";
            th_str_arr.push(tmp_th_str);
        }
        th_str_str=th_str_arr.join("");
        var append_str=
            "<tr>"+
                "<th>"+
                    "<div class='column_td column_th' style='width:21px;' >"+
                        "<input type='checkbox' class='chk_all' />"+
                    "</div>"+
                "</th>"+
                "<th>"+
                    "<div class='column_td column_th' style='width:30px;' >"+
                        "No."+
                    "</div>"+
                "</th>"+
                th_str_str+
            "</tr>";
        $(this.opt_obj.grid_table_id).find(".tr_head").append(append_str);
    };

    this.addTrRow=function(json_data,in_row_opt){
        var this_obj=this;
        if(json_data==undefined){json_data={};}
        if(in_row_opt==undefined){in_row_opt={};}
        var row_opt={
            'is_textarea':false,
            'is_focus':true,
            'focus_key':'',
            'append_row_num':'',
            'row_background':'#e2e2e8',
            'font_color':'',
            'is_editable_select':true,
            'idx_num':'',
            'view_val_arr':{},
            'select_col_arr':{},
            'custom_col_arr':{}
        };
        for(var key in in_row_opt){
            row_opt[key]=in_row_opt[key];
        }
        this.tr_row_num++;

        var key_val_str="";
        var key_val_arr=[];
        var is_has_key=true;
        for(var i=0;i<this.opt_obj.x_pri_col_arr.length;i++){
            var key=this.opt_obj.x_pri_col_arr[i];
            if(json_data[key]==undefined){
                is_has_key=false;
            }else{
                key_val_arr.push(json_data[key]);
            }
        }
        key_val_str=key_val_arr.join(",");

        var row_style="";
        if(row_opt['row_background']!=""){
            row_style="background:"+row_opt['row_background']+";";
        }

        var td_opt_obj=row_opt;
        td_opt_obj['json_data']=json_data;
        var td_input_rs=this.get_td_input_str_str(td_opt_obj);
        var hidden_input_str_str=td_input_rs["hidden_input_str_str"];
        var td_str_str=td_input_rs["td_str_str"];

        var append_str=
            "<tr class='tr_row' style='"+row_style+"' >"+
                "<td>"+
                    "<div class='column_td' style='width:21px;' >"+
                        "<input type='checkbox' class='chk_box' value='"+key_val_str+"' />"+
                        "<input type='hidden' class='row_num' value='"+this.tr_row_num+"' />"+
                    "</div>"+
                    hidden_input_str_str+
                "</td>"+
                "<td>"+
                    "<div class='column_td' style='width:30px;' >"+
                        "<span class='idx_num'>"+row_opt['idx_num']+"</span>"+
                    "</div>"+
                "</td>"+
                td_str_str+
            "</tr>";
        $(this.opt_obj.grid_table_id).find(".tr_body").append(append_str);

        var last_obj=null;
        $(this.opt_obj.grid_table_id).find(".tr_body").find(".tr_row").each(function(idx,ele){
            if($(ele).find(".row_num").val()==this_obj.tr_row_num){
                last_obj=ele;
            }
        });
        if(last_obj!=null){
            for(var key in json_data){
                $(last_obj).find("."+key).val(json_data[key]);
            }
            if(row_opt['is_focus']){
                $(this.opt_obj.grid_table_id).find(".chk_box").prop("checked",false);
                $(last_obj).find(".chk_box").prop("checked",true);
                if(this.opt_obj.grid_event_obj){
                    if(row_opt['focus_key']!=undefined&&row_opt['focus_key']!=""){
                        $(last_obj).find("."+row_opt['focus_key']).focus();
                        var base_obj=$(last_obj).parent().parent().parent();
                        this.opt_obj.grid_event_obj.scrollMoveToTarget({'base_obj':base_obj,'focus_obj':$(last_obj).find("."+row_opt['focus_key'])});
                    }else{
                        this.opt_obj.grid_event_obj.set_focus_input_by_tr_obj(last_obj);
                    }
                    this.opt_obj.grid_event_obj.set_active_tr_by_check_box();
                }
            }

            if(row_opt['is_textarea']==true){
                $(last_obj).find(".row_input").each(function(i,e){
                    if($(e).prop("nodeName")=='TEXTAREA'){
                        $(e).attr("maxlength","");
                        $(e).css("width","550px");
                        $(e).css("text-align","left");
                    }
                });
            }
            if(this.opt_obj.grid_event_obj.opt_obj.is_grid){
                $(last_obj).find(".row_input").each(function(i,e){
                    if(!$(e).hasClass("pri_val")){
                        $(e).prop("readonly",false);
                    }
                });
            }
            if(row_opt['font_color']!=undefined){
                $(last_obj).find(".row_v_input").css("color",row_opt['font_color']);
                $(last_obj).find(".view_div_area").css("color",row_opt['font_color']);
            }
            if(this.opt_obj.x_view_col_arr.length>0){
                for(var i=0;i<this.opt_obj.x_view_col_arr.length;i++){
                    var key_str=this.opt_obj.x_view_col_arr[i];
                    if($(last_obj).find('.view_div_'+key_str).length==1){
                        if(row_opt['view_val_arr'][key_str]!=undefined){
                            $(last_obj).find('.view_div_'+key_str).append(row_opt['view_val_arr'][key_str]);
                        }else if($(last_obj).find('.view_div_'+key_str).find('.view_div_'+key_str+'_span').length==0){
                            $(last_obj).find('.view_div_'+key_str).append('<span class=\"view_div_'+key_str+'_span\" >'+$(last_obj).find('.'+key_str).val()+'</span>');
                            $(last_obj).find('.'+key_str).val($(last_obj).find('.view_div_'+key_str+'_span').text());
                            $(last_obj).find('.'+key_str).attr('title',$(last_obj).find('.view_div_'+key_str+'_span').text());
                        }
                    }
                }
            }

            if(this.opt_obj.is_date_col_arr.length>0){
                for(var i=0;i<this.opt_obj.is_date_col_arr.length;i++){
                    var key_str=this.opt_obj.is_date_col_arr[i];
                    if(!$(last_obj).find("."+key_str).hasClass("is_date")){
                        $(last_obj).find("."+key_str).addClass("is_date");
                    }
                    if($(last_obj).find("."+key_str).length>0){
                        if($(last_obj).find("."+key_str).val().indexOf('0000-00-00')!=-1){
                            $(last_obj).find("."+key_str).val('');
                        }
                    }
                }
            }
            if(this.opt_obj.is_number_col_arr.length>0){
                for(var i=0;i<this.opt_obj.is_number_col_arr.length;i++){
                    var key_str=this.opt_obj.is_number_col_arr[i];
                    if(!$(last_obj).find("."+key_str).hasClass("is_number")){
                        $(last_obj).find("."+key_str).addClass("is_number");
                        var tmp_value=$(last_obj).find("."+key_str).val();
                        tmp_value=this.uncomma(tmp_value);
                        if(tmp_value!='0'&&tmp_value!=''){
                            tmp_value=tmp_value+'';
                            if(tmp_value.indexOf('.')!=-1){
                                tmp_value=parseFloat(tmp_value).toFixed(1);
                            }
                            $(last_obj).find("."+key_str).val(this.comma(tmp_value));
                        }
                        if($(last_obj).find('.view_div_'+key_str).length!=0){
                            $(last_obj).find('.view_div_'+key_str).addClass('is_number');
                            $(last_obj).find('.view_div_'+key_str).text(this.comma(tmp_value));
                        }
                    }
                }
            }
            if(this.opt_obj.is_tel_col_arr.length>0){
                for(var i=0;i<this.opt_obj.is_tel_col_arr.length;i++){
                    var key_str=this.opt_obj.is_tel_col_arr[i];
                    if(!$(last_obj).find("."+key_str).hasClass("is_tel")){
                        $(last_obj).find("."+key_str).addClass("is_tel");
                    }
                }
            }
            if(this.opt_obj.is_busin_col_arr.length>0){
                for(var i=0;i<this.opt_obj.is_busin_col_arr.length;i++){
                    var key_str=this.opt_obj.is_busin_col_arr[i];
                    if(!$(last_obj).find("."+key_str).hasClass("is_busin_num")){
                        $(last_obj).find("."+key_str).addClass("is_busin_num");
                    }
                }
            }


            if(row_opt['idx_num']==''){this.set_row_idx_num();}
        }

        return last_obj;
    };

    this.get_td_input_str_str=function(td_opt_obj){
        var json_data=td_opt_obj['json_data'];
        var row_hidden_input_col_arr=[];
        for(var i=0;i<this.opt_obj.x_pri_col_arr.length;i++){
            var tmp_k=this.opt_obj.x_pri_col_arr[i];
            if(this.str_in_array(tmp_k,row_hidden_input_col_arr)==-1){
                if(this.opt_obj.x_column_list_arr[tmp_k]==undefined){
                    row_hidden_input_col_arr.push(tmp_k);
                }
            }
        }
        for(var i=0;i<this.opt_obj.x_basic_col_arr.length;i++){
            var tmp_k=this.opt_obj.x_basic_col_arr[i];
            if(this.str_in_array(tmp_k,row_hidden_input_col_arr)==-1){
                if(this.opt_obj.x_column_list_arr[tmp_k]==undefined){
                    row_hidden_input_col_arr.push(tmp_k);
                }
            }
        }
        var hidden_input_str_arr=[];
        for(var i=0;i<row_hidden_input_col_arr.length;i++){
            var tmp_k=row_hidden_input_col_arr[i];
            var pri_class_str="";
            if(this.str_in_array(tmp_k,this.opt_obj.x_pri_col_arr)!=-1){
                pri_class_str=" pri_val";
            }
            var tmp_hidden_input="<div><input type='hidden' class='"+tmp_k+" row_input"+pri_class_str+"' value='' /></div>";
            hidden_input_str_arr.push(tmp_hidden_input);
        }
        var hidden_input_str_str=hidden_input_str_arr.join("");

        //textarea 여부
        var row_input_html_head="<input type='text' ";
        var row_input_html_tail="/>";
        if(td_opt_obj['is_textarea']==true){
            row_input_html_head="<textarea rows='1' ";
            row_input_html_tail="></textarea>";
        }
        var td_str_arr=[];

        for(var key in this.opt_obj.x_column_list_arr){
            var col_obj=this.opt_obj.x_column_list_arr[key];
            var pri_class_str="";
            if(this.str_in_array(key,this.opt_obj.x_pri_col_arr)!=-1){
                pri_class_str=" pri_val";
            }
            var max_length_str="";
            if(col_obj['length']!=""){
                max_length_str="maxlength='"+col_obj['length']+"'";
            }

            var input_style_str="width:"+col_obj['width']+"px;resize:none;overflow:hidden;";
            var input_class_str=key+" row_input row_v_input"+pri_class_str;

            var is_input_show=true;
            if(this.str_in_array(key,this.opt_obj.x_view_col_arr)!=-1){
                is_input_show=false;
            }

            var row_td_input_str="";
            if(is_input_show){
                row_td_input_str=row_input_html_head+ "class='"+input_class_str+"' readonly='readonly' "+max_length_str+" style='"+input_style_str+"' "+row_input_html_tail;
            }else{
                row_td_input_str="<div class='view_div_"+key+" view_div_area' ></div>"+
                                "<input type='hidden' class='"+key+" row_input view_div_input"+pri_class_str+"' />";
            }

            var tmp_td_str=
                "<td>"+
                    "<div class='column_td' style='width:"+col_obj['width']+"px;' >"+
                        row_td_input_str+
                    "</div>"+
                "</td>";
            td_str_arr.push(tmp_td_str);
        }
        var td_str_str=td_str_arr.join("");

        return {"td_str_str":td_str_str,"hidden_input_str_str":hidden_input_str_str};
    };

    this.set_row_idx_num=function(){
        $(this.opt_obj.grid_table_id).find(".idx_num").each(function(i,e){
            var par_obj=$(e).parent().parent().parent();
            var tmp_idx=i+1;
            $(e).text(tmp_idx);
        });
    };
    this.str_in_array=function(search_str,str_arr){
        var is_match=-1;
        for(var i=0;i<str_arr.length;i++){
            if(str_arr[i]==search_str){
                is_match=true;
            }
        }
        return is_match;
    };
    //콤마찍기
    this.comma=function(str){
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
    this.uncomma=function(str) {
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

    this.init();
};
