﻿<?php

class Paginator{
    var $total;
    var $start;
    var $output;
    var $pag = array();
    var $default_count = 20;
    var $current_page;

    function __construct($total, $start)
    {
        $this->total = $total;
        $this->start = $start;
        $this->count = ( !empty($_GET['count'] ) ? $_GET['count'] : $this->default_count );
        $this->current_page = ( !empty($_GET['page'] ) ? $_GET['page'] : 1);
    }

    function paginate($page_url_params){
        $pag['total'] = $this->total;
        $pag['total_formatted'] = number_format($pag['total'], 0, ',', '.');
        $pag['start'] = $this->start;
        $pag['total_pages'] = ($pag['total'] % $this->count == 0) ? (int)($pag['total']/$this->count) : (int)($pag['total']/$this->count+1);
        $pag['count'] = $this->count;

        $range_min = (($this->current_page-5) > 0) ? $this->current_page-5 : 1;
        $range_max = (($range_min+7) > $pag['total_pages']) ? $pag['total_pages'] : $range_min+7;
        $pag['pages'] = range($range_min, $range_max);

        $this->output  = '<nav aria-label="Page navigation example">';
        $this->output .= '<ul class="pagination justify-content-center">';
        if ($pag['total_pages'] > 0){
            if ($this->current_page > 1){
                $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=1" title="' . __('go to page', 'lis') . ' 1">&laquo;&laquo;</a></li>';
                $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=' . ($this->current_page-1) . '" title="' .  __('go to page', 'lis') . ' ' . ($this->current_page-1) . '"> &laquo; </a></li>';
            }else{
                $this->output .= '<li class="page-item disabled"><a class="page-link" href="#">&laquo;&laquo;</a></li>';
                $this->output .= '<li class="page-item disabled"><a class="page-link" href="#"> &laquo; </a></li>';
            }

            for ($i=0; $i < count($pag['pages']); $i++){
                $for_page = $pag['pages'][$i];

                if ( $this->current_page != '' ){
                    if ($this->current_page != $for_page){
                        $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=' . $for_page . '" title="' . __('go to page','lis') . ' ' . $for_page . '">' . $for_page . '</a></li>';
                    }else{
                        $this->output .= '<li class="page-item active"><a class="page-link" href="#">' . $for_page .'</a></li>';
                    }
                }else{
                    if ($for_page != 1){
                        $this->output .= '<li><a class="page-link" href="' . $page_url_params . '&page=' . $for_page . '" title="' . __('go to page','lis') . ' ' . $for_page . '">' .$for_page . '</a></li>';
                    }else{
                        $this->output .= '<li> class="active"><a class="page-link" href="#">' . $for_page .'</a></li>';
                    }
                }
            }

            if ( $this->current_page != '' ){
                if ($this->current_page < $pag['total_pages']) {
                    $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=' . ($this->current_page+1) . '" title="' . __('go to page', 'lis') . ($this->current_page+1) . '"> &raquo; </a></li>';
                    $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=' . ($pag['total_pages']) . '" title="' . __('go to page', 'lis') . $pag['total_pages'] . '">&raquo;&raquo;</a></li>';
                }else{
                    $this->output .= '<li class="page-item disabled"><a class="page-link" href="#">&raquo;</a></li>';
                    $this->output .= '<li class="page-item disabled"><a class="page-link" href="#"> &raquo;&raquo; </a></li>';
                }
            } else {
                $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=2" title="' . __('go to page', 'lis') . ' 2"> &raquo; </a></li>';
                $this->output .= '<li class="page-item"><a class="page-link" href="' . $page_url_params . '&page=' . $pag['total_pages'] .'" title="' . __('go to page') . ' ' . $pag['total_pages'] . '">&raquo;&raquo;</a></li>';
            }

        }

        $this->output .= '</ul>';
        $this->output .= '</nav>';

    }


    function display_pages(){
        return $this->output;
    }
}
?>
