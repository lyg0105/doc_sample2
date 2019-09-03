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
        if($(this.opt_obj.grid_table_id).find("#tr_head").length==0){
            var append_str=
                "<div class='board_list tr_head_table' >"+
                    "<table style='width:auto;'>"+
                        "<thead class='tr_head'>"+

                        "</thead>"+
                    "</table>"+
                "</div>";
            $(this.opt_obj.grid_table_id).append(append_str);
        }
        if($(this.opt_obj.grid_table_id).find("#tr_body").length==0){
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
                        "<input type='text' class='sc_input search_input' id='sc_"+key+"' style='width:98%;' />"+
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
                        "<input type='checkbox' id='chk_all' />"+
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
        	'focus_key':'key_name',
        	'append_row_num':'',
        	'row_background':'#fff',
        	'font_color':'#fff',
        	'is_editable_select':true,
            'idx_num':''
        };
        for(var key in in_row_opt){
            row_opt[key]=in_row_opt[key];
        }
        this.tr_row_num++;

        var td_str_arr=[];
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

        for(var key in this.opt_obj.x_column_list_arr){
            var col_obj=this.opt_obj.x_column_list_arr[key];
            var tmp_td_str=
                "<td>"+
                    "<div class='column_td' style='width:"+col_obj['width']+"px;' >"+
                        "<input type='text' class='"+key+" row_input row_v_input' style='width:"+col_obj['width']+"px;resize:none;overflow:hidden;' />"+
                    "</div>"+
                "</td>";
            td_str_arr.push(tmp_td_str);
        }
        var td_str_str=td_str_arr.join("");

        var append_str=
            "<tr class='tr_row' >"+
                "<td>"+
                    "<div class='column_td' style='width:21px;' >"+
                        "<input type='checkbox' class='chk_box' value='"+key_val_str+"' />"+
                        "<input type='hidden' class='row_num' value='"+this.tr_row_num+"' />"+
                    "</div>"+
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
        }
    };

    this.init();
};
