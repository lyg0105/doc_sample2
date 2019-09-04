<?php include_once str_replace('\\','/',base_path()).'/resources/views/x_templete/crud/grid/v1/js/index_js.php'; ?>
{{ $slot }}
<div class="btn_box">
    <a class="btn_o_s" onclick="go_add_action();" >등록</a>
    <a class="btn_x_s" href="/doc/list" >뒤로</a>
</div>
<form id="data_form" action="" method="post"  >
    <div id="write_table_div" >

    </div>
</form>
