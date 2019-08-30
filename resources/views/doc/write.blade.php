@extends('layouts.app')

@section('title', 'DOCLIST')

@section('sidebar')
    @parent
@endsection
@section('content')
    Write 영역
    @component('x_templete.crud.write.v1.write')
        <script>
        var x_column_list_arr={
            "id":{"name":"아이디","type":"int","length":"","width":"100","pri":"1"},
            "order_num":{"name":"순번","type":"int","length":"","width":"100","pri":"1"}
        };
        var x_pri_col_arr=["id"];
        $(function(){
            var tmp_opt_obj={
                "write_table_id":"#write_table_div",
                "x_column_list_arr":x_column_list_arr,
                "x_pri_col_arr":x_pri_col_arr
            };
            var lygWTable=new LygWriteTable(tmp_opt_obj);
            lygWTable.addTrRow({},{});
        });

        var form_func=new LygWriteForm();
        function go_add_action(){
            var form_json_data={
                "table":"doc_list",
                "is_update":"",
                "_token":"{{ csrf_token() }}"
            };
            form_json_data=form_func.get_form_data_by_check_box(form_json_data,{"pri_col_arr":x_pri_col_arr});
            form_func.requestAjax(
                {
                    'url':'/api/doc/write',
                    'form_data':form_json_data,
                    'is_confirm':true,
                    'confirm_msg':'등록 하시겠습니까?',
                    'callBackfunc':function(data){
                        alert(data['msg']);
                    }
                }
            );
        }
        </script>
    @endcomponent
@endsection
