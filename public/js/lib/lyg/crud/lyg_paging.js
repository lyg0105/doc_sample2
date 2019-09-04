var LygPaging=function(opt_obj){
    this.opt_obj={
        'now_page':1,
        'block_size':10,
        'start_num':1,
        'end_num':10,
        'total_rec':0,
        'num_per_page':10,
        'st_limit':0,
        'before_block':0,
        'next_block':0,
        'is_tot_view':true,

        'gopage_func_name':'gopage',
        'befor_img1':'/img/home/board/btn_before1.gif',
        'befor_img2':'/img/home/board/btn_before2.gif',
        'after_img1':'/img/home/board/btn_after1.gif',
        'after_img2':'/img/home/board/btn_after2.gif'
    };
    this.init=function(){
        if(opt_obj==undefined){opt_obj={};}
        for(var key in opt_obj){
            this.opt_obj[key]=opt_obj[key];
        }
        for(var key in this.opt_obj){
            if(key!='is_tot_view'
                &&key!='befor_img1'
                &&key!='befor_img2'
                &&key!='after_img1'
                &&key!='after_img2'
                &&key!='gopage_func_name'){
                this.opt_obj[key]=parseInt(this.opt_obj[key]);
            }
        }

        this.calculatePaging();
    };
    this.calculatePaging=function(){
        // 1 - 현재 페이지 설정
        if(this.opt_obj.now_page==0||this.opt_obj.now_page==""||isNaN(this.opt_obj.now_page)){this.opt_obj.now_page=1;}
        // 3 - 각 블럭의 start 페이지 값을 설정한다
        if(this.opt_obj.now_page % this.opt_obj.block_size == 0){
            this.opt_obj.start_num = this.opt_obj.now_page - this.opt_obj.block_size + 1;    // 현재 페이지가 블럭의 마지막 페이지 일 경우 해당 블럭의 시작 페이지 번호를 정한다
        }else{
            this.opt_obj.start_num = Math.floor(this.opt_obj.now_page/this.opt_obj.block_size)*this.opt_obj.block_size + 1; // 현재페이지가 블럭의 마지막 페이지가 아닐경우 시작 페이지를 지정한다
        }
        // 4 - 각 블럭의 end 페이지 값을 설정한다
        this.opt_obj.end_num = this.opt_obj.start_num + this.opt_obj.block_size - 1;
        // 5 -- 총 개수

        // 7 - 불러오기 쿼리문에서 시작레코드 숫자 지정 ex(  limit($st_limit, $num_per_page)
        if(this.opt_obj.now_page == 1){
            this.opt_obj.st_limit = 0;
        }else{
            this.opt_obj.st_limit = (this.opt_obj.now_page * this.opt_obj.num_per_page) - this.opt_obj.num_per_page;
        }
        // 8 - 이전 블럭 설정
        this.opt_obj.before_block=this.opt_obj.start_num - 1;
    };
    this.get_print_str=function(){
        if(this.opt_obj.total_rec == 0){return '';}

        var print_str=
        "<div class='pageing'>"+
            "<div class='page_bar'>"+
            "<a href='javascript:' onclick='"+this.opt_obj['gopage_func_name']+"(1);'><img src='"+this.opt_obj.befor_img1+"' alt='처음페이지' /></a>"+
            "<a href='javascript:' onclick='"+this.opt_obj['gopage_func_name']+"("+((this.opt_obj.now_page>1)?this.opt_obj.now_page-1:1)+");'><img src='"+this.opt_obj.befor_img2+"' alt='이전페이지' /></a>";

        if(this.opt_obj.start_num > 1){
            print_str+= "<a href='javascript:' onclick='"+this.opt_obj['gopage_func_name']+"("+this.opt_obj.before_block+");' onFocus='blur()'>이전</a>";
        }

        // 12 - 페이지 링크
        for(var i=this.opt_obj.start_num; i<=this.opt_obj.end_num; i++ ){
            if( Math.ceil(this.opt_obj.total_rec/this.opt_obj.num_per_page) >= i ){
                var now_page_class="";
                var page_print_num=i;
                if(this.opt_obj.now_page==i){
                    now_page_class="class='on'";
                    page_print_num="<strong>"+page_print_num+"</strong>";
                }
                print_str+= "<a href='javascript:' id='page_num' onclick='"+this.opt_obj['gopage_func_name']+"("+i+");' "+now_page_class+" >"+page_print_num+"</a>";
            }
        }

        // 전체 페이지 개수를 구한다.
        var total_page= Math.ceil( this.opt_obj.total_rec/this.opt_obj.num_per_page ) < 1 ? 1 : Math.ceil( this.opt_obj.total_rec/this.opt_obj.num_per_page );
        // 13 - 다음 블럭 링크
        if(this.opt_obj.end_num * this.opt_obj.num_per_page <= this.opt_obj.total_rec){
            print_str+= "<a href='javascript:' onclick='"+this.opt_obj['gopage_func_name']+"("+this.opt_obj.next_block+");' >다음</a>";
        }

        print_str+= "<a href='javascript:' onclick='"+this.opt_obj['gopage_func_name']+"("+((this.opt_obj.now_page<total_page)?this.opt_obj.now_page+1:this.opt_obj.now_page)+");'><img src='"+this.opt_obj.after_img2+"' alt='다음페이지' /></a>";
        print_str+= "<a href='javascript:' onclick='"+this.opt_obj['gopage_func_name']+"("+total_page+");'><img src='"+this.opt_obj.after_img1+"' alt='마지막페이지' /></a>";

        var sel_arr=[this.opt_obj.num_per_page,10,20,30,50,100,200,300];
        var unique_sel_arr=[];
        $.each(sel_arr, function(i,el){
            if($.inArray(el, unique_sel_arr) === -1){unique_sel_arr.push(parseInt(el));}
        });
        sel_arr=unique_sel_arr;

        print_str+= "<select id='num_per_page' name='num_per_page'  onchange='"+this.opt_obj['gopage_func_name']+"(1);' style='width:45px;height:27px;border:1px solid #ccc;margin-left:-5px;' >";
        for(var i=0;i<sel_arr.length;i++){
            var val_str=sel_arr[i];
            var selected_str=(val_str==this.opt_obj.num_per_page)?"selected='selected'":'';
            print_str+= "<option value='"+val_str+"' "+selected_str+" >"+val_str+"</option>";
        }
        print_str+= "</select>";
            if(this.opt_obj.is_tot_view){
                print_str+= "<span style='color:green;vertical-align:baseline;' >총 "+this.opt_obj.total_rec+" 건</span>";
            }
            print_str+="</div>";
        print_str+= "</div>";

        return print_str;
    };
    this.init();
};
