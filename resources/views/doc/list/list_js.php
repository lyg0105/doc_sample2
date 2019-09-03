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
        'gridTable_obj':lygGridTable
    };
    new LygGridEvent(tmp_grid_opt_data);
    addTrRowByAjax();
}

function addTrRowByAjax(){
    var form_json_data={
        "_token":"<?=csrf_token()?>"
    };
    form_func.requestAjax(
        {
            'url':'/api/doc/list',
            'form_data':form_json_data,
            'is_confirm':false,
            'callBackfunc':function(data){
                if(data['result']=='true'){
                    if(data['data'].length>0){
                        for(var i=0;i<data['data'].length;i++){
                            var idx_num=i+1;
                            lygGridTable.addTrRow(data['data'][i],{'idx_num':idx_num});
                        }
                    }
                }
            }
        }
    );
}
</script>
