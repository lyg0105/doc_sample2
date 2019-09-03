<script>
var form_func=new LygWriteForm();
window.xColumnObj={};

$(function(){
    get_Xcolumn_by_ajax();
});
function get_Xcolumn_by_ajax(){
    form_func.requestAjax(
        {
            'url':'/api/common/xcolumn',
            'form_data':{'list_sort':'Doc/Write',"_token":"<?=csrf_token()?>"},
            'is_confirm':false,
            'callBackfunc':function(data){
                init_table_render(data['data']);
            }
        }
    );


}
function init_table_render(opt_data){
    opt_data['grid_table_id']='#write_table_div';
    window.xColumnObj=opt_data;
    var lygGridTable=new LygGridTable(opt_data);
    lygGridTable.addTrRow({},{});
}

function go_add_action(){
    var form_json_data={
        "table":"doc_list",
        "is_update":"",
        "is_default_val":"1",
        "_token":"<?=csrf_token()?>"
    };
    $(".chk_box").prop("checked",true);
    form_json_data=form_func.get_form_data_by_check_box(form_json_data,{"pri_col_arr":window.xColumnObj.x_pri_col_arr});
    form_func.requestAjax(
        {
            'url':'/api/common/write',
            'form_data':form_json_data,
            'is_confirm':true,
            'confirm_msg':'등록 하시겠습니까?',
            'callBackfunc':function(data){
                alert(data['msg']);
                location.href="/doc/list";
            }
        }
    );
}
</script>
