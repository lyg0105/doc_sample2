var LygGridEvent=function(opt_obj){
    this.g_div=null;//grid_div_obj
    this.opt_obj={
        gridTable_obj:null,
        is_grid:true,
        is_not_grid:false,
        is_not_init_focus:false,
        pop_id:'',
        go_active_tr_change:null,
        go_grid_change:null
    };

    this.init=function(){
        if(opt_obj==undefined){return false;}
        for(var key in opt_obj){
            this.opt_obj[key]=opt_obj[key];
        }
        this.g_div=$(this.opt_obj.gridTable_obj.opt_obj.grid_table_id);

        this.setCheckAll();
        this.setCheckFirstRow();

    };

    this.setCheckFirstRow=function(){
        var this_obj=this;
        if(!this.opt_obj.is_not_init_focus){
            $(this.g_div).find(".chk_all").focus();
            setTimeout(function(){
                if($(this_obj.g_div).find(".chk_box").length>0){
                    $(this_obj.g_div).find(".chk_box").eq(0).prop("checked",true);
                    this_obj.set_active_tr_by_check_box();
                }
            },200);
        }
    };

    this.setCheckAll=function(){
        var this_obj=this;
        $(this.g_div).find(".chk_all").click(function(){
            $(this_obj.g_div).find(".chk_box").prop("checked",$(this).prop('checked'));
            this_obj.set_active_tr_by_check_box();
        });
    };

    this.set_active_tr_by_check_box=function(){
        if(this.opt_obj.is_grid){
            $(this.g_div).find(".active_tr").removeClass("active_tr");
            return false;
        }

        $(this.g_div).find(".chk_box").each(function(i,e){
            var par_obj=$(e).parent().parent().parent();
            if($(e).prop("checked")){
                if(!$(par_obj).hasClass("active_tr")){
                    $(par_obj).addClass("active_tr");
                }
            }else{
                $(par_obj).removeClass("active_tr");
            }
        });
        if(this.opt_obj.go_active_tr_change){
            this.opt_obj.go_active_tr_change();
        }
    };

    this.set_grid_display=function(){
        if(this.opt_obj.is_not_grid){return false;}
        this.opt_obj.is_grid=!this.opt_obj.is_grid;
        var this_obj=this;

        //readonly풀기
        $(this.g_div).find(".row_input").each(function(i,e){
            if(this.opt_obj.is_grid){
                if(!$(e).hasClass("pri_val")){
                    $(e).prop("readonly",false);
                }
            }else{
                $(e).prop("readonly",true);
            }
        });

        if(this.opt_obj.is_grid){
            $(this.g_div).find(".active_tr").removeClass("active_tr");
        }else{
            this.set_active_tr_by_check_box();
        }
        //그리드표시
        if(this.opt_obj.go_grid_change){
            this.opt_obj.go_grid_change();
        }
        //첫번째에 포커스
        if($(this.g_div).find(".chk_box:checked").length>0){
            var tmp_par_obj=$(this.g_div).find(".chk_box:checked").eq(0).parent().parent().parent();
            this.set_focus_input_by_tr_obj(tmp_par_obj);
        }else{
            $(this.g_div).find(".chk_all").focus();
        }
    };

    this.set_focus_input_by_tr_obj=function(tr_obj,now_i){
        if(now_i==undefined){now_i=-1;}
        if(!this.opt_obj.is_grid){return false;}

        var is_focused=false;
        var tr_obj_row_num=-1;
        if(now_i==-1){
            if($(':focus').hasClass("row_input")){
                var par_obj=$(':focus').parent().parent().parent();
                tr_obj_row_num=$(par_obj).find(".row_num").val();
                if($(par_obj).find(".row_num").val()==$(tr_obj).find(".row_num").val()){
                    is_focused=true;
                }
            }
            now_i=0;
        }

        var tmp_i=0;
        $(tr_obj).find(".row_v_input").each(function(i,e){
            if(!$(e).hasClass("pri_val")){
                if(is_focused==false){
                    var par_obj=$(e).parent().parent().parent();
                    var tmp_row_num=$(par_obj).find(".row_num").val();
                    if(tmp_i==now_i){
                        if(tr_obj_row_num!=tmp_row_num){
                            $(e).focus();
                            $(e).select();
                            is_focused=true;
                            var base_obj=$(e).parent().parent().parent().parent().parent().parent();
                            this_obj.scrollMoveToTarget({'base_obj':base_obj,'focus_obj':$(e)});
                        }
                    }
                }
                tmp_i++;
            }
        });
    }

    //scrollMoveToTarget({'base_obj':'#div','focus_obj':'#input'});
    this.scrollMoveToTarget=function(opt_obj){
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

    this.init();
};
