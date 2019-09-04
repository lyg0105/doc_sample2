var LygGridEvent=function(opt_obj){
    this.g_div=null;//grid_div_obj
    this.opt_obj={
        gridTable_obj:null,
        is_grid:true,
        is_not_grid:false,
        is_not_init_focus:false,
        now_row_num:0,
        pop_id:'',

        go_active_tr_change:null,
        go_grid_change:null,
        go_detail_popup:null,
        go_add_new_row:null,
        go_open_add_popup:null,
        go_delete_of_current_tr:null,
        go_window_close:null,
        go_esc_action:null,
        go_enter_view:null,
        go_change_row_input:null,
        go_click_chk_box:null,
        go_click_row_input:null,
        go_change_tr_row_data:null
    };

    this.init=function(){
        if(opt_obj==undefined){return false;}
        for(var key in opt_obj){
            this.opt_obj[key]=opt_obj[key];
        }
        if(this.opt_obj.gridTable_obj){
            this.opt_obj.gridTable_obj.opt_obj.grid_event_obj=this;
        }
        this.g_div=$(this.opt_obj.gridTable_obj.opt_obj.grid_table_id);

        this.setCheckAll();
        this.setCheckFirstRow();
        this.key_event_init();
    };

    this.setCheckFirstRow=function(){
        var this_obj=this;
        if(!this.opt_obj.is_not_init_focus){
            $(this.g_div).find(".chk_all").focus();
        }
    };

    this.setCheckAll=function(){
        var this_obj=this;
        $(this.g_div).find(".chk_all").click(function(){
            $(this_obj.g_div).find(".chk_box").prop("checked",$(this).prop('checked'));
            this_obj.set_active_tr_by_check_box();
        });
    };

    this.key_event_init=function(){
        var this_obj=this;
        $(document).keydown(function(e){
            var key_code_arr=[112,113,114,115,116,117,118,38,40,27,13,39,37];
            if(this_obj.str_in_array(e.keyCode,key_code_arr)!=-1){
                this_obj.key_down_manage(e);
                if(e.keyCode!=37&&e.keyCode!=39){
                    return false;
                }
            }
            if($(e.target).hasClass("row_input")){
                $(e.target).parent().parent().parent().find(".chk_box").prop("checked",true);
                this_obj.set_active_tr_by_check_box();
            }
        });
        $(document).keyup(function(e){
            if($(e.target).hasClass("search_input")&&e.keyCode==13){this_obj.gopage(1);}//검색

            if($(e.target).hasClass("row_input")){
                if(this_obj.opt_obj.go_change_row_input){
                    this_obj.opt_obj.go_change_row_input(e.target);
                }
            }
        });
        $(document).on('click','.chk_box',function(e){
            this_obj.set_active_tr_by_check_box();
            if(this_obj.opt_obj.go_click_chk_box){
                this_obj.opt_obj.go_click_chk_box(e);
            }
        });
        $(document).on('click','.row_v_input',function(e){
            $(this_obj.g_div).find(".chk_box").prop("checked",false);
            var tmp_obj=$(e.target).parent().parent().parent().find(".chk_box");
            $(tmp_obj).prop("checked",!$(tmp_obj).prop("checked"));
            this_obj.set_active_tr_by_check_box();

            if(this_obj.opt_obj.go_click_row_input){
                this_obj.opt_obj.go_click_row_input(e);
            }
        });
        $(document).on('click','.view_div_area',function(e){
            $(this_obj.g_div).find(".chk_box").prop("checked",false);
            var tmp_obj=$(this).parent().parent().parent().find(".chk_box");
            $(tmp_obj).prop("checked",!$(tmp_obj).prop("checked"));
            this_obj.set_active_tr_by_check_box();

            if(this_obj.opt_obj.go_click_row_input){
                this_obj.opt_obj.go_click_row_input(e);
            }
        });
        $(document).on('focusout','.row_input',function(e){
            if(this_obj.opt_obj.is_grid){
                var par_obj=$(e.target).parent().parent().parent();
                var is_go_save=true;
                var target_obj=e.target;
                setTimeout(function(){
                    if($(':focus').hasClass("row_input")){
                        var tmp_row_num=$(':focus').parent().parent().parent().find(".row_num").val();
                        if(tmp_row_num==$(par_obj).find(".row_num").val()){
                            //is_go_save=false;
                        }
                    }
                    if(is_go_save){
                        if(this_obj.opt_obj.go_change_tr_row_data){
                            this_obj.opt_obj.go_change_tr_row_data(par_obj);
                        }
                    }
                },100);
            }
        });
        $(document).on('focus','.row_input',function(e){
            if(this_obj.opt_obj.is_grid){
                this_obj.opt_obj.now_row_num=$(e.target).parent().parent().parent().find(".row_num").val();
            }
        });
    };

    this.key_down_manage=function(e_obj){
        var keyCode=e_obj.keyCode;
        switch(keyCode){
            case 112://F1 도움말

                break;
            case 113://F2 입력
                if(this.opt_obj.go_open_add_popup){
                    this.opt_obj.go_open_add_popup(false);
                }

                break;
            case 114://F3 수정
                if(this.opt_obj.go_open_add_popup){
                    this.opt_obj.go_open_add_popup(true);
                }
                break;
            case 115://F4 삭제
                if(this.opt_obj.go_delete_of_current_tr){
                    this.opt_obj.go_delete_of_current_tr();
                }
                break;
            case 116://F5 조회
                location.reload();
                break;
            case 117://F6 작성출력

                break;
            case 118://F7 그리드
                this.set_grid_display();
                break;
            case 38://위
                this.set_active_tr_up_down(keyCode);
                break;
            case 40://아래
                this.set_active_tr_up_down(keyCode);
                break;
            case 37://왼쪽
                this.set_next_input_left_right(keyCode);
                break;
            case 39://오른쪽
                this.set_next_input_left_right(keyCode);
                break;
            case 27://ESC
                if(this.opt_obj.is_grid==undefined){this.opt_obj.is_grid=false;}
                if(this.opt_obj.is_grid==false){
                    if($(this.g_div).find(".chk_box:checked").length==0){
                        if(this.opt_obj.go_window_close){
                            this.opt_obj.go_window_close();
                        }
                    }
                }
                if(this.opt_obj.is_grid){
                    if($(this.g_div).find(".chk_box:checked").length==1){
                        $(this.g_div).find(".chk_box:checked").prop("checked",false);
                        this.set_active_tr_by_check_box();
                    }
                    $(":focus").blur();
                    this.opt_obj.is_grid=false;
                }
                if($(this.g_div).find(".active_tr").length>0){
                    $(this.g_div).find(".chk_box:checked").prop("checked",false);
                    $(this.g_div).find(".active_tr").removeClass("active_tr");
                }
                if(this.opt_obj.go_esc_action){this.opt_obj.go_esc_action();}
                break;
            case 13://엔터
                if(this.opt_obj.is_grid==undefined){this.opt_obj.is_grid=false;}

                if(this.opt_obj.is_grid==false&&$(this.g_div).find(".chk_box:checked").length==1){
                    if(this.opt_obj.go_enter_view){
                        this.opt_obj.go_enter_view(e_obj);
                    }
                }else if(this.opt_obj.is_grid){
                    this.set_next_input_left_right(keyCode);
                }
                break;
        }
    }

    this.set_active_tr_up_down=function(keyCode){
        if($(".lyg_auto_complete_wrap").length>0){
            return false;
        }
        if($(this.g_div).find(".chk_box:checked").length==0){
            this.set_active_tr($(this.g_div).find(".tr_body .tr_row").eq(0));
            if(keyCode==40){
                //신규등록
                if($(this.g_div).find(".tr_body .tr_row").length==0){
                    if(this.opt_obj.is_grid){
                        if(this.opt_obj.go_add_new_row){
                            $(this.g_div).find('.chk_all').focus();
                            $(this.g_div).find('.chk_box:checked').prop("checked",false);
                            this.opt_obj.go_add_new_row();
                        }
                    }
                }
            }
        }else{
            var focus_obj=this.get_focus_obj_info();
            var now_i=focus_obj['now_row_i'];
            if(keyCode==38){
                now_i--;
                if(now_i<0){now_i=0;}
                this.set_active_tr($(this.g_div).find(".tr_body .tr_row").eq(now_i),false,focus_obj['row_input_i']);
            }else if(keyCode==40){
                now_i++;
                if(now_i>$(this.g_div).find(".tr_body .tr_row").length-1){
                    //신규등록
                    if(this.opt_obj.is_grid){
                        if(this.opt_obj.go_add_new_row){
                            $(this.g_div).find('.chk_all').focus();
                            $(this.g_div).find('.chk_box:checked').prop("checked",false);
                            this.opt_obj.go_add_new_row();
                        }
                    }
                }else{
                    this.set_active_tr($(this.g_div).find(".tr_body .tr_row").eq(now_i),false,focus_obj['row_input_i']);
                }
            }
        }
        this.set_active_tr_by_check_box();
    };

    this.set_next_input_left_right=function(keyCode){
        var this_obj=this;
        if($(".lyg_auto_complete_wrap").length>0){
            return false;
        }
        if(!this.opt_obj.is_grid){
            return false;
        }
        //포커스 위치가 input이라면 포커스 이동처리
        if($(':focus').hasClass("row_input")){
            var focus_obj=this.get_focus_obj_info();

            var par_obj=focus_obj['par_obj'];
            var key_str=focus_obj['key_str'];
            var sel_st_end=focus_obj['sel_st_end'];
            var row_num=focus_obj['row_num'];

            var now_i=focus_obj['row_input_i'];
            var tot_i_length=focus_obj['input_tot'];

            var now_row_i=focus_obj['now_row_i'];
            var row_tot=focus_obj['row_tot'];
            if(keyCode==13){
                //상세정보 선택 있는지 확인
                if(this.opt_obj.go_detail_popup){
                    var tmp_opt_data={'par_obj':par_obj,'row_num':row_num,'key_str':key_str};
                    var tmp_rs=this.opt_obj.go_detail_popup(tmp_opt_data);
                    if(tmp_rs['result']=='true'){
                        //있으면 멈춤
                        return false;
                    }
                }
            }
            if(keyCode==39||keyCode==13){
                var tmp_is_next_go=false;
                if(keyCode==13){
                    tmp_is_next_go=true;
                }else{
                    if(sel_st_end['end']==$(':focus').val().length){
                        tmp_is_next_go=true;
                    }
                }
                if(tmp_is_next_go){
                    //오른쪽
                    if(now_i+1>tot_i_length-1){
                        if(now_row_i+1>$(this.g_div).find(".tr_body .tr_row").length-1){
                            //신규등록
                            if(this.opt_obj.go_add_new_row){
                                if(this.opt_obj.is_grid){
                                    $(this.g_div).find('.chk_all').focus();
                                    $(this.g_div).find('.chk_box:checked').prop("checked",false);
                                    this.opt_obj.go_add_new_row();
                                }
                            }
                        }else{
                            //다음줄
                            if(now_row_i+1<=row_tot){
                                this.set_active_tr($(this.g_div).find(".tr_body .tr_row").eq(now_row_i+1));
                            }
                        }
                    }else{
                        //다음칸 포커스
                        //$(par_obj).find(".row_v_input").eq(now_i+1).select();
                        var tmp_now_i=now_i+1;
                        this.set_focus_input_by_tr_obj(par_obj,tmp_now_i);
                    }
                }
            }else if(keyCode==37){
                //왼쪽
                if(sel_st_end['start']==0&&sel_st_end['end']==0){
                    if(now_i-1<0){
                        //이전줄
                        if(now_row_i-1>=0){
                            this.set_active_tr($(this.g_div).find(".tr_body .tr_row").eq(now_row_i-1));
                        }
                    }else{
                        //이전칸 포커스
                        //$(par_obj).find(".row_v_input").eq(now_i-1).select();
                        var tmp_now_i=now_i-1;
                        this.set_focus_input_by_tr_obj(par_obj,tmp_now_i);
                    }
                }
            }
            this.set_active_tr_by_check_box();
        }
    }

    this.set_active_tr=function(tr_obj,is_only,now_input_i){
        if(is_only==undefined){is_only=false;}
        if(now_input_i==undefined){now_input_i=0;}
        if(is_only==false){
            $(this.g_div).find(".chk_box:checked").prop("checked",false);
        }

        if($(tr_obj).find(".chk_box").prop("checked")){
            $(tr_obj).find(".chk_box").prop("checked",false);
        }else{
            $(tr_obj).find(".chk_box").prop("checked",true);
        }
        this.set_focus_input_by_tr_obj(tr_obj,now_input_i);
        this.set_active_tr_by_check_box();
    }

    this.get_focus_obj_info=function(){
        var this_obj=this;
        var return_json={
            'par_obj':null,
            'key_str':'',
            'sel_st_end':{},
            'row_num':'',
            'now_row_i':0,
            'row_tot':0,
            'row_input_i':0,
            'input_tot':0
        };
        if($(':focus').hasClass("row_input")){
            var par_obj=$(':focus').parent().parent().parent();
            var key_str=$(':focus').attr("class").split(" ")[0];
            var sel_st_end=this_obj.getInputSelection(document.activeElement);
            var row_num=$(par_obj).find(".row_num").val();

            var now_i=0;
            var tot_i_length=0;
            $(par_obj).find(".row_v_input").each(function(i,e){
                if(!$(e).hasClass("pri_val")){
                    tot_i_length++;
                }
            });

            var tmp_i=0;
            $(par_obj).find(".row_v_input").each(function(i,e){
                if(!$(e).hasClass("pri_val")){
                    if($(e).attr("class").split(" ")[0]==key_str){
                        now_i=tmp_i;
                    }
                    tmp_i++;
                }
            });

            var now_row_i=0;
            $(this_obj.g_div).find(".row_num").each(function(i,e){
                if(row_num==$(e).val()){
                    now_row_i=i;
                }
            });

            return_json={
                'par_obj':par_obj,
                'key_str':key_str,
                'sel_st_end':sel_st_end,
                'row_num':row_num,
                'now_row_i':now_row_i,
                'row_tot':$(this_obj.g_div).find(".tr_body .tr_row").length,
                'row_input_i':now_i,
                'input_tot':tot_i_length
            };
        }
        if(!this.opt_obj.is_grid){
            var now_row_i=0;
            $(this.g_div).find(".tr_body .tr_row").each(function(i,e){
                if($(e).find(".chk_box").prop("checked")){
                    now_row_i=i;
                }
            });
            return_json['now_row_i']=now_row_i;
            return_json['row_tot']=$(this.g_div).find(".tr_body .tr_row").length;
        }

        return return_json;
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
            if(this_obj.opt_obj.is_grid){
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
        var this_obj=this;
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

    /**
     * Return an object with the selection range or cursor position (if both have the same value)
     * @param {DOMElement} el A dom element of a textarea or input text.
     * @return {Object} reference Object with 2 properties (start and end) with the identifier of the location of the cursor and selected text.
     **/
    this.getInputSelection=function(el){
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

    this.str_in_array=function(search_str,str_arr){
        var is_match=-1;
        for(var i=0;i<str_arr.length;i++){
            if(str_arr[i]==search_str){
                is_match=true;
            }
        }
        return is_match;
    };

    this.gopage=function(now_page){
        $("#now_page").val(now_page);
        $("#from").submit();
    };

    this.init();
};
