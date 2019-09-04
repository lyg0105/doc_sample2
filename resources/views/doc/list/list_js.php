<script>
var form_func=new LygWriteForm();
var lygGridTable=null;
window.xColumnObj={};

$(function(){
    get_Xcolumn_by_ajax();
});
function get_Xcolumn_by_ajax(){
    form_func.requestAjax(
        {
            'url':'/api/common/xcolumn',
            'form_data':{'list_sort':'Doc/List',"_token":"<?=csrf_token()?>"},
            'is_confirm':false,
            'callBackfunc':function(data){
                init_table_render(data['data']);
            }
        }
    );
}
function init_table_render(opt_data){
    opt_data['grid_table_id']='#list_table_div';
    window.xColumnObj=opt_data;
    lygGridTable=new LygGridTable(opt_data);
    var tmp_grid_opt_data={
        'gridTable_obj':lygGridTable,
        'is_grid':false,
        'is_not_grid':false,
        'go_add_new_row':function(){
            lygGridTable.addTrRow({},{});
        },
        'go_delete_of_current_tr':deleteTrByAjax
    };
    new LygGridEvent(tmp_grid_opt_data);
    addTrRowByAjax();
}

function addTrRowByAjax(){
    var form_json_data={
        "_token":"<?=csrf_token()?>"
    };
    var url_data=form_func.getUrlParams();
    for(var key in url_data){
        $("#"+key).val(url_data[key]);
        form_json_data[key]=url_data[key];
    }
    var num_per_page=url_data['num_per_page']?url_data['num_per_page']:10;
    form_func.requestAjax(
        {
            'url':'/api/doc/list',
            'form_data':form_json_data,
            'is_confirm':false,
            'callBackfunc':function(data){
                if(data['result']=='true'){
                    if(data['data']['info_arr'].length>0){
                        var start_idx_num=data['data']['start_index'];
                        var tot_num=data['data']['tot'];
                        for(var i=0;i<data['data']['info_arr'].length;i++){
                            var idx_num=start_idx_num-i;
                            lygGridTable.addTrRow(data['data']['info_arr'][i],{'idx_num':idx_num,'is_focus':false});
                        }

                        var lygPaging=new LygPaging({
                            'now_page':$("#now_page").val(),
                            'total_rec':tot_num,
                            'num_per_page':num_per_page
                        });
                        $("#paging_div").append(lygPaging.get_print_str());
                    }
                }
            }
        }
    );
}

function go_save_action(){
    var is_val=true;
    if($(".chk_box:checked").length==0){
        alert("선택이 없습니다.");
        is_val=false;
    }
    if(is_val){
        var form_json_data={
            "_token":"<?=csrf_token()?>"
        };
        form_json_data['table']=window.xColumnObj.table;
        form_json_data['is_update']='1';
        form_json_data=form_func.get_form_data_by_check_box(form_json_data,{'pri_col_arr':window.xColumnObj.x_pri_col_arr});
        form_json_data['is_update_arr']=[];
        $(".chk_box:checked").each(function(idx,ele){
            var tmp_is_update="0";
            if($(ele).val()!=""){
                tmp_is_update="1";
            }
            form_json_data['is_update_arr'].push(tmp_is_update);
        });

        form_func.requestAjax(
            {
                'url':'/api/common/write',
                'form_data':form_json_data,
                'is_confirm':true,
                'confirm_msg':'선택 수정/저장 하시겠습니까?',
                'callBackfunc':function(data){
                    alert(data['msg']);
                    if(data['result']=='true'){
                        //location.reload();
                    }
                }
            }
        );
    }
}

function deleteTrByAjax(){
    var is_val=true;
    if($(".chk_box:checked").length==0){
        alert("선택이 없습니다.");
        is_val=false;
    }

    if(is_val){
        var form_json_data={
            "_token":"<?=csrf_token()?>"
        };
        form_json_data['table']=window.xColumnObj.table;
        $(".chk_box:checked").each(function(idx,ele){
            var par_obj=$(ele).parent().parent().parent();
            for(var i=0;i<window.xColumnObj.x_pri_col_arr.length;i++){
                var key_str=window.xColumnObj.x_pri_col_arr[i];
                if(form_json_data[key_str]==undefined){form_json_data[key_str]=[];}
                form_json_data[key_str].push($(par_obj).find('.'+key_str).val());
            }
        });
        form_func.requestAjax(
            {
                'url':'/api/common/delete',
                'form_data':form_json_data,
                'is_confirm':true,
                'confirm_msg':'선택 삭제 하시겠습니까?',
                'callBackfunc':function(data){
                    alert(data['msg']);
                    if(data['result']=='true'){
                        $(".chk_box:checked").each(function(idx,ele){
                            $(ele).parent().parent().parent().remove();
                        });
                    }
                }
            }
        );
    }
}
</script>
