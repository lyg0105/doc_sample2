<?php include_once str_replace('\\','/',base_path()).'/resources/views/x_templete/crud/grid/v1/js/index_js.php'; ?>
{{ $slot }}
<div class="btn_box">

</div>
<form id="form" action="" method="post"  >
    <input type="hidden" id="now_page" name="now_page" />

    <div id="list_table_div" >

    </div>
    <div id="paging_div">

    </div>
</form>
