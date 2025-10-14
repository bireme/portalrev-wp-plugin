<?php
/*
Template Name: CC Home
*/
global $cc_service_url, $cc_plugin_slug, $cc_plugin_title,$biblio_texts;

/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
require_once(CC_PLUGIN_PATH . '/lib/Paginator.php');
require_once(CC_PLUGIN_PATH . '/template/translations.php');

$cc_config = get_option('cc_config');
$cc_initial_filter = $cc_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
$country = isset($_GET['country']) ? sanitize_text_field($_GET['country']) : '';
$descriptor = isset($_GET['descriptor']) ? sanitize_text_field($_GET['descriptor_filter']) : '';
$user = isset($_GET['user']) ? sanitize_text_field($_GET['user']) : '';

$lang_dir = isset($lang_dir) ? $lang_dir : 'pt';

if ($search != ''){
    $old_query = str_replace('=', ':',  urldecode($search));
    $old_query = str_replace('pa', 'country_code', $old_query);
}
if ($country != ''){
    $old_query .= ' country_code:' . $country;
}
if ($user != ''){
    $old_query .= ' user:' . $country;
}

$query = ( isset($_GET['s']) ? sanitize_text_field($_GET['s']) : sanitize_text_field($_GET['q']) );

if(isset($old_query)){
    if ($old_query != ''){
        $query .= $old_query;
    }
}

$query = stripslashes($query);
$sanitize_user_filter = isset($_GET['filter']) ? sanitize_text_field($_GET['filter']) : '';
$user_filter = stripslashes($sanitize_user_filter);
$page = ( isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 1 );
$total = 0;
$count = 20;
$filter = '';

if ($user_filter != ''){
    if (substr($user_filter, 0, strlen($cc_initial_filter)) === $cc_initial_filter){
        $filter = $user_filter;
    }else{
        $filter = $cc_initial_filter . ' AND ' . $user_filter;
    }
}else{
    $filter = $cc_initial_filter;
}
$start = ($page * $count) - $count;

$cc_search = $cc_service_url . 'api/title/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang . "&count=20";
$cc_search .= '&sort=title_sort+asc';
//echo $cc_search;
if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $filters){
        preg_match('/([a-z_]+):(.+)/',$filters, $filter_parts);
        if ($filter_parts){
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

$filter_list = explode(";", $cc_config['available_filter']);

foreach ($filter_list as $filter_field){
    $cc_search.= "&facet.field=" . urlencode($filter_field);
}

$response = @file_get_contents($cc_search);
if ($response){
    $response_json = json_decode($response);
    // echo "<pre>"; print_r($response_json); echo "</pre>";
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $center_list = $response_json->diaServerResponse[0]->response->docs;
    $facet_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields;
    if ( array_key_exists('country', $facet_list)) {
        usort($facet_list['country'], function($a, $b) {
            return $b[0] <=> $a[0];
        });
    }
}
    
/*
$response = @file_get_contents($cc_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $center_list = $response_json->diaServerResponse[0]->response->docs;

    $type_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_type;
    $thematic_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_thematic;
    $country_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->country;
    //var_dump($response_json->diaServerResponse[0]->facet_counts->facet_fields);
    $descriptor_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->descriptor_filter;
    $language_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->language;
    $status_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->status;
    $indexed_database = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->indexed_database;
    $thematic_area_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->thematic_area_display;


    //$language_list[0] = "en^Portuguese|pt-br^Português|es^Portugués";

    usort($type_list, function($a, $b) use ($patterns, $type_translated) {
        $a[0] = strtolower($type_translated[$a[0]]);
        $a[0] = preg_replace(array_values($patterns), array_keys($patterns), $a[0]);
        $b[0] = strtolower($type_translated[$b[0]]);
        $b[0] = preg_replace(array_values($patterns), array_keys($patterns), $b[0]);
        return $a[0] <=> $b[0];
    });

    usort($thematic_list, function($a, $b) use ($patterns, $thematic_translated) {
        $a[0] = strtolower($thematic_translated[$a[0]]);
        $a[0] = preg_replace(array_values($patterns), array_keys($patterns), $a[0]);
        $b[0] = strtolower($thematic_translated[$b[0]]);
        $b[0] = preg_replace(array_values($patterns), array_keys($patterns), $b[0]);
        return $a[0] <=> $b[0];
    });

}*/

$page_url_params = '?q=' . urlencode($query)  . '&filter=' . urlencode($user_filter);



//echo 'total' . $total;
//echo 'start' . $start;
$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);
//var_dump($pages);

$home_url = isset($cc_config['home_url_' . $lang]) ? $cc_config['home_url_' . $lang] : $cc_config['home_url'];
$plugin_title = isset($cc_config['plugin_title_' . $lang]) ? $cc_config['plugin_title_' . $lang] : $cc_config['plugin_title'];


//echo $cc_search;

if ( function_exists( 'pll_the_languages' ) ) {
    $available_languages = pll_languages_list();
    $available_languages_name = pll_languages_list(array('fields' => 'name'));
    $default_language = pll_default_language();
}

?>

<?php include('header.php') ?>
    <nav>
        <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <!--<a href="<?php echo ($home_url != '') ? $home_url : real_site_url() ?>"><?php _e('Home','cc'); ?></a>-->
                <?php _e('Home','cc'); ?>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo real_site_url($cc_plugin_slug); ?>"><?php echo $plugin_title ?></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <?php
                    if ( isset($total) && strval($total) == 0) {
                       echo __('No results found','cc');
                   }elseif (strval($total) > 1) {
                       echo $total . ' ' . __('titles','cc');
                   }else{
                       echo $center_list[0]->title;
                   }
                ?>
            </li>
        </ol>
                </div>
    </nav>
    <!--
    <div class="row">
    <div class="col-12 cc-banner">
        <?php //dynamic_sidebar('cc-banner');?>
                </div>
                </div>-->
<section class="container" id="main_container">
    

	<div class="row">
    <div class="col-12 cc-banner">
        <?php //dynamic_sidebar('cc-banner');?>
                </div>
        <div class="col-12 col-md-7 col-lg-8">
        <?php
                if ( isset($total) && strval($total) == 0) {
                       echo '<BR><br><h5 class="text-center">' . __('No results found','cc') . '</h5>';
                   }
                   ?>
            <div class="row">

                <?php
                $pos =0;
                foreach ( $center_list as $resource) {
                    $pos++;
                    echo '<article class="col-lg-' . '12' . '">';
                    echo '<div class="box1">';
                    echo '<span class="badge text-bg-info">' . strval( intval($start) + $pos ) . '/' . $total . '</span>';
                    echo '<h3 class="box1Title">';
                    ?>
                                    

<?php 
                    ?>
                    
                    <a href='<?php echo real_site_url($cc_plugin_slug) ; ?>/detail/?id=<?php echo $resource->django_id; ?>' class="linkTitulo">
                    <?php 

                    echo htmlspecialchars($resource->title) . '</a>';

                    if ($resource->status == '1'){
                        //echo ' <span class="badge text-bg-warning">' . __('INACTIVE', 'cc') . '</span>';
                    }elseif($resource->status == '0'){
                        //echo ' <span class="badge text-bg-warning">' . __('CLOSED', 'cc') . '</span>';
                    }

                    //if($resource->responsibality_mention){
                        //echo '<BR>Menção de responsabilidade:'. $resource->responsibility_mention ;
                    //}
                    echo '</h3>';
                    foreach($resource->shortened_title as $sortened){
                        echo '<span class="texto">';
                        echo __('Titulo abreviado','cc') . ': ';
                        echo  $sortened . '<BR></span>';
                    }
                    if($resource->language){
                        echo '<span class="texto">';
                        echo __('Disponível no idioma','cc') . ': ';
                        $n = 0;
                        foreach($resource->language as $language){
                        if($n > 0){echo ', ';}
                        print_lang_value($language, $site_language);
                        $n++;
                        }
                        echo '<BR></span>';
                    }
                    if($resource->country){
                        echo '<span class="texto"> ';
                        echo __('País','cc') . ': ';
                        print_lang_value($resource->country, $site_language);
                        echo '<BR></span>';
                    }
                    if(isset($issn)){
                        foreach($resource->issn as $issn){
                            echo '<span class="texto">ISSN: '. $issn . '</span><br>';
                        }
                    }
                    ?>
                    <br>
                    <a href="<?php echo real_site_url($cc_plugin_slug); ?>/detail/?id=<?php echo $resource->django_id; ?>" class="btnDetalhes">
                    <?php _e('ver mais detalhes', 'cc')?></a>
                    
                    <?php


                    ?>
                    <!--<span class="more"><a href="<?php echo real_site_url($cc_plugin_slug); ?>/detail/?id=<?php echo $resource->django_id; ?>"><?php _e('See more details','lis'); ?></a></span>
                --><?php
                    //echo '</table>';
                    echo '</div>';
                    echo '</article>';
                }
                ?>

            </div> <!-- /row results area -->
            <hr>
            <?php echo $pages->display_pages(); ?>
        </div> <!-- /col results area -->

        <div class="col-md-5 col-lg-4" id="filterRight">
            <div class="boxFilter">
                <?php
                
                    if(isset($applied_filter_list)){
                    if ($applied_filter_list) :?>
                    <section>
                    <form method="get" name="searchFilter" id="formFilters" action="?results" style="overflow:hidden">
                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                        <input type="hidden" name="q" id="query" value="<?php echo $query; ?>" >
                        <input type="hidden" name="filter" id="filter" value="" >
                        <h5 style="border-bottom: 2px solid #ddd"><?php _e('Filtros aplicados', 'cc');?></h5>

                        <?php foreach ( $applied_filter_list as $ap_filter => $ap_filter_values ) :?>
                            <?php if ($ap_filter != 'country_code'): ?>
                                <h5><?php //echo $filter_title_translated[$ap_filter]; ?></h5>
                                <ul>
                                <?php foreach ( $ap_filter_values as $value ) :?>
                                    <input type="hidden" name="apply_filter" class="apply_filter"
                                            id="<?php echo md5($value) ?>" value='<?php echo $ap_filter . ':"' . $value . '"'; ?>' >
                                    <li>
                                        <span class="filter-item">
                                            <?php
                                                if ($ap_filter == 'country' or $ap_filter == 'language'){
                                                    echo print_lang_value($value, $site_language);
                                                }elseif ($ap_filter == 'status'){
                                                    if($value == 0){ echo __('encerrado', 'cc');}
                                                    if($value == 1){ echo __('corrente', 'cc');}
                                                }elseif ($ap_filter == 'institution_thematic'){
                                                    echo $thematic_translated[$value];
                                                }else{
                                                    echo $value;
                                                }
                                            ?>
                                        </span>
                                        <span class="filter-item-del">
                                            <a href="javascript:remove_filter('<?php echo md5($value) ?>')">
                                                <img src="<?php echo CC_PLUGIN_URL; ?>template/images/del.png">
                                            </a>
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                                </ul>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </form>
                    <section>
                <?php endif; }  ?>
                <!----------------------->
                 
            
                <!----------------------------->
                <?php 
                                foreach($filter_list as $filter_field) {?>
                                <?php if ($facet_list[$filter_field] ): ?>
                                    <section>
                                        <h5 class="box1Title"><?php echo cluster_to_text($filter_field);//[$filter_field]; ?></h5>
                                        <ul class="filter-list">
                                            <?php foreach ( $facet_list[$filter_field] as $filter_item ) { ?>
                                                <?php
                                                    $cluster = $filter_field;
                                                    $filter_value = $filter_item[0];
                                                    $filter_count = $filter_item[1];

                                                    if ('descriptor_filter' == $cluster) {
                                                        $cluster = 'descriptor';
                                                    }
                                                ?>
                                                <?php //$class = ( 'mj' != $cluster || filter_var($filter_value, FILTER_VALIDATE_INT) === false ) ? 'cat-item' : 'cat-item hide'; ?>
                                                <li class="cat-item">
                                                    <?php
                                                        $filter_link = '?';
                                                        if ($query != ''){
                                                            $filter_link .= 'q=' . $query . '&';
                                                        }
                                                        $filter_link .= 'filter=' . $cluster . ':"' . $filter_value . '"';
                                                        if ($user_filter != ''){
                                                            $filter_link .= ' AND ' . $user_filter ;
                                                        }
                                                    ?>
                                                    <?php if ( strpos($filter_value, '^') !== false ): ?>
                                                        <a href='<?php echo $filter_link; ?>'><?php print_lang_value($filter_value, $site_language); ?></a>
                                                    <?php elseif($filter_value == 0): ?>
                                                        <a href='<?php echo $filter_link; ?>'><?php echo __('encerrado', 'cc'); ?></a>
                                                    <?php elseif($filter_value == 1): ?>
                                                        <a href='<?php echo $filter_link; ?>'><?php echo __('corrente', 'cc'); ?></a>
                                                    <?php else: ?>
                                                        <a href='<?php echo $filter_link; ?>'><?php echo $filter_value; ?></a>
                                                    <?php endif; ?>

                                                    <span class="cat-item-count"><?php echo $filter_count; ?></span>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                        <?php if ( count($facet_list[$filter_field]) == 20 ) : ?>
                                        <div class="show-more text-center">
                                            <a href="javascript:void(0)" class="btn-ajax" data-fb="30" data-cluster="<?php echo $filter_field; ?>"><?php _e('show more','cc'); ?></a>
                                            <a href="javascript:void(0)" class="loading"><?php _e('loading','cc'); ?>...</a>
                                        </div>
                                        <?php endif; ?>
                                    </section>
                                <?php endif; ?>
                            <?php } ?>




            </div>
        </div>
    </div>
</section>

<script type="text/javascript">
    jQuery(function ($) {
        $(document).on( "click", ".btn-ajax", function(e) {
            e.preventDefault();

            var _this = $(this);
            var fb = $(this).data('fb');
            var cluster = $(this).data('cluster');

            $(this).hide();
            $(this).next('.loading').show();

            $.ajax({
                type: "POST",
                url: cc_script_vars.ajaxurl,
                data: {
                    action: 'centers_show_more_clusters',
                    lang: '<?php echo $lang_dir; ?>',
                    site_lang: '<?php echo $site_language; ?>',
                    query: '<?php echo $query; ?>',
                    filter: '<?php echo $filter; ?>',
                    uf: '<?php echo $user_filter; ?>',
                    cluster: cluster,
                    fb: fb
                },
                success: function(response){
                    var html = $.parseHTML( response );
                    var this_len = _this.parent().siblings('.filter-list').find(".cat-item").length;
                    _this.parent().siblings('.filter-list').replaceWith( response );
                    _this.data('fb', fb+10);
                    _this.next('.loading').hide();

                    var response_len = $(html).find(".cat-item").length;
                    var mod = parseInt(response_len % 10);

                    if ( mod || response_len == this_len ) {
                        _this.remove();
                    } else {
                        _this.show();
                    }
                },
                error: function(error){ console.log(error) }
            });
        });
    });
</script>

<?php include('footer.php'); ?>
