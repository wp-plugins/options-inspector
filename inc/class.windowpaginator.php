<?php
class WindowPaginator {
    var $total;
    var $window_size;
    var $current_page_number;
    var $window_begin;
    var $window_end;
    var $base;
    var $q;
    function WindowPaginator($total, $window_size, $current_page_number, $linkbase, $querystring){
        $this->total = ceil($total / 20);
        $this->window_size = $window_size;
        $this->current_page_number = $current_page_number;
        $this->base = $linkbase;
        $this->q = $querystring;
        $window_size_divide_2 = $this->window_size / 2;
        $former_half_size = floor($window_size_divide_2);
        $latter_half_size = ceil($window_size_divide_2);
        if ($this->total <= $this->window_size){
            $this->window_begin = 1;
            $this->window_end = $this->total;
        }else if ($this->current_page_number <= $latter_half_size){
            $this->window_begin = 1;
            $this->window_end = $this->window_size;
        }else if($this->current_page_number > ($this->total - $latter_half_size)){
            $this->window_begin = $this->total - $this->window_size + 1;
            $this->window_end = $this->total;
        }else{
            $this->window_begin = $this->current_page_number - $former_half_size;
            $this->window_end = $this->current_page_number + $latter_half_size - 1;
        }
    }
    function print_page_navigator(){
        //var_dump($this->window_begin,$this->window_end,$this->current_page_number);
        $pagelink = '<a title="%d" href="' . $this->base . $this->q . '=%d">%d</a>';
        $currpage = '<span class="current">%d</span>';
        echo '<div class="oi-pagenavi">';
        echo 'Page ', $this->current_page_number, ' of ', $this->total, ' Pages';
        if ($this->in_window(1) || $this->window_begin - 1 == 1){
            echo 1 == $this->current_page_number ?
                 str_replace('%d','1',$currpage):
                 str_replace('%d','1',$pagelink);
        }else{
            $first =  str_replace('%d','1',$pagelink);
            echo str_replace('>1<', '>&laquo;First<', $first);
        }
        if ($this->in_window(2)){
            echo 2 == $this->current_page_number ?
                 str_replace('%d','2',$currpage):
                 str_replace('%d','2',$pagelink);
        }else{
            echo '<span>...</span>';
        }
        for($i = $this->window_begin; $i <= $this->window_end; $i++){
            if($i <= 2) continue;
            if($i >= $this->total - 1) continue;
            echo $i == $this->current_page_number ?
                 str_replace('%d', $i, $currpage):
                 str_replace('%d', $i, $pagelink);
        }
        if ($this->in_window($this->total - 1)){
            $total_minus_1 = $this->total - 1;
            echo $total_minus_1 == $this->current_page_number ?
                 str_replace('%d',$total_minus_1,$currpage):
                 str_replace('%d',$total_minus_1,$pagelink);
        }else{
            echo '<span>...</span>';
        }
        if ($this->in_window($this->total)){
            echo $this->total == $this->current_page_number ?
                 str_replace('%d', $this->total, $currpage):
                 str_replace('%d', $this->total, $pagelink);
        }else{
            $last = str_replace('%d', $this->total, $pagelink);
            echo str_replace(">{$this->total}<", ">Last&raquo;<", $last);
        }
        echo '</div>';
    }
    function in_window($idx){
        if ($idx >= $this->window_begin && $idx <= $this->window_end){
            return true;
        }
        return false;
    }
}
?>
