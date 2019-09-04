<?php
namespace App\Lib\Web\Paging;

class WebPaging
{
    public $now_page=1;
    public $block_size=10;
    public $start_num=1;
    public $end_num=10;
    public $total_rec=0;
    public $num_per_page=10;
    public $st_limit=0;
    public $before_block=0;
    public $next_block=0;
    public $is_tot_view=true;

    public $befor_img1='public/img/admin/board/btn_before1.gif';
    public $befor_img2='public/img/admin/board/btn_before2.gif';
    public $after_img1='public/img/admin/board/btn_after1.gif';
    public $after_img2='public/img/admin/board/btn_after2.gif';

    public function __construct($p_conf)
    {
        $this->set_conf($p_conf);
    }
    public function set_conf($p_conf){
        /*
         $p_conf,now_page,block_size,tot,num_per_page,befor_img1,befor_img2,after_img1,after_img2
         */
        // 1 - 현재 페이지 설정
        $this->now_page = ( isset($p_conf['now_page'] ) ? $p_conf['now_page'] : 1 );
        if(empty($this->now_page)){$this->now_page='1';}
        // 2 - 블럭크기 설정
        $this->block_size = ( isset($p_conf['block_size'] ) ? $p_conf['block_size'] : $this->block_size );
        // 3 - 각 블럭의 start 페이지 값을 설정한다
        if($this->now_page % $this->block_size == 0){
            $this->start_num = $this->now_page - $this->block_size + 1;    // 현재 페이지가 블럭의 마지막 페이지 일 경우 해당 블럭의 시작 페이지 번호를 정한다
        }else{
            $this->start_num = floor($this->now_page/$this->block_size)*$this->block_size + 1; // 현재페이지가 블럭의 마지막 페이지가 아닐경우 시작 페이지를 지정한다
        }
        // 4 - 각 블럭의 end 페이지 값을 설정한다
        $this->end_num = $this->start_num + $this->block_size - 1;
        // 5 -- 총 개수
        $this->total_rec =isset($p_conf['tot'] ) ? $p_conf['tot'] : 0;
        // 6 - 한페이지당 보여줄 레코드 수 설정
        $this->num_per_page =isset($p_conf['num_per_page'] ) ? $p_conf['num_per_page'] : 0;
        // 7 - 불러오기 쿼리문에서 시작레코드 숫자 지정 ex(  limit($st_limit, $num_per_page)
        if($this->now_page == 1){
            $this->st_limit = 0;
        }else{
            if(empty($this->now_page)){$this->now_page=1;}
            $this->st_limit = ($this->now_page * $this->num_per_page) - $this->num_per_page;
        }
        // 8 - 이전 블럭 설정
        $this->before_block=$this->start_num - 1;
        // 9 - 다음 블럭 설정
        $this->next_block=$this->end_num + 1;

        if(isset($p_conf['befor_img1'])){
            $this->befor_img1=$p_conf['befor_img1'];
        }
        if(isset($p_conf['befor_img2'])){
            $this->befor_img2=$p_conf['befor_img2'];
        }
        if(isset($p_conf['after_img1'])){
            $this->after_img1=$p_conf['after_img1'];
        }
        if(isset($p_conf['after_img2'])){
            $this->after_img2=$p_conf['after_img2'];
        }
        if(isset($p_conf['is_tot_view'])){
            $this->is_tot_view=$p_conf['is_tot_view'];
        }
    }

    public function get_index_num($a){
        $idx_num=$this->total_rec-(($this->now_page-1)*$this->num_per_page)-$a;
        return $idx_num;
    }

    public function get_print_str(){
        if($this->total_rec == 0){return '';}

        $print_str=
        "<div class='pageing'>
            <div class='page_bar'>
            <a href='javascript:' id='page01' onclick='gopage(1);'><img src='".$this->befor_img1."' alt='처음페이지' /></a>
            <a href='javascript:' id='page02' onclick='gopage(".(($this->now_page>1)?$this->now_page-1:1).");'><img src='".$this->befor_img2."' alt='이전페이지' /></a>";

        if($this->start_num > 1){
            $print_str.= "<a href='javascript:' onclick='gopage(".$this->before_block.");' onFocus='blur()'>이전</a>";
        }

        // 12 - 페이지 링크
        for($i=$this->start_num; $i<=$this->end_num; $i++ ){
            if( ceil($this->total_rec/$this->num_per_page) >= $i ){
                $now_page_class="";
                $page_print_num=$i;
                if($this->now_page==$i){
                    $now_page_class="class='on'";
                    $page_print_num="<strong>".$page_print_num."</strong>";
                }
                $print_str.= "<a href='javascript:' id='page_num' onclick='gopage(".$i.");' ".$now_page_class." >".$page_print_num."</a>";
            }
        }

        // 전체 페이지 개수를 구한다.
        $total_page= ceil( $this->total_rec/$this->num_per_page ) < 1 ? 1 : ceil( $this->total_rec/$this->num_per_page );
        // 13 - 다음 블럭 링크
        if($this->end_num * $this->num_per_page <= $this->total_rec){
            $print_str.= "<a href='javascript:' onclick='gopage(".$this->next_block.");' >다음</a>";
        }

        $print_str.= "<a href='javascript:' id='page03' onclick='gopage(".(($this->now_page<$total_page)?$this->now_page+1:$this->now_page).");'><img src='".$this->after_img2."' alt='다음페이지' /></a>";
        $print_str.= "<a href='javascript:' id='page04' onclick='gopage(".$total_page.");'><img src='".$this->after_img1."' alt='마지막페이지' /></a>";

        $sel_arr=array($this->num_per_page,10,30,20,50,100,200,300);
        $sel_arr=array_unique($sel_arr);
        sort($sel_arr);

        $print_str.= "<select id='num_per_page' name='num_per_page'  onchange='gopage(1);' style='width:45px;height:27px;border:1px solid #ccc;margin-left:-5px;' >";
        foreach($sel_arr as $val){
            $selected_str=($val==$this->num_per_page)?"selected='selected'":'';
            $print_str.= "<option value='".$val."' ".$selected_str." >".$val."</option>";
        }
        $print_str.= "</select>";
            if(!empty($this->is_tot_view)){
                //$print_str.= "<span style='color:green;vertical-align:baseline;' >총 ".$this->total_rec." 건</span>";
            }
            $print_str.="</div>";
        $print_str.= "</div>";

        return $print_str;
    }

    public function print_page(){
        if($this->total_rec == 0){return '';}
        echo $this->get_print_str();
    }
}
