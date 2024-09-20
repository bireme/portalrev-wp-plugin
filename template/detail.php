<?php
/*
Template Name: CC Home
*/
global $cc_service_url, $cc_plugin_slug, $cc_plugin_title;

require_once(CC_PLUGIN_PATH . '/lib/Paginator.php');
require_once(CC_PLUGIN_PATH . '/template/translations.php');

$cc_config = get_option('cc_config');
$cc_initial_filter = $cc_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

//compatibility with older version
$search = sanitize_text_field($_GET['search']);
$country = sanitize_text_field($_GET['country']);
$user = sanitize_text_field($_GET['user']);

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

if ($old_query != ''){
    $query .= $old_query;
}

$query = stripslashes($query);
$sanitize_user_filter = sanitize_text_field($_GET['filter']);
$user_filter = stripslashes($sanitize_user_filter);
$page = ( isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 1 );
$total = 0;
$count = 10;
$filter = '';

$ide = sanitize_text_field($_GET['id']);

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

$cc_search = $cc_service_url . 'api/title/'.$ide.'/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang;

if ( $user_filter != '' ) {
    $user_filter_list = preg_split("/ AND /", $user_filter);
    $applied_filter_list = array();
    foreach($user_filter_list as $filters){
        preg_match('/([a-z_]+):(.+)/',$filters, $filter_parts);
        if ($filter_parts){
            // convert to internal format
            $applied_filter_list[$filter_parts[1]][] = str_replace('"', '', $filter_parts[2]);
        }
    }
}

$response = @file_get_contents($cc_search);
if ($response){
    $response_json = json_decode($response);
    //var_dump($response_json);
    //var_dump($response_json);

    $center_list = $response_json;

    //var_dump($center_list);

    

    $type_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_type;
    $thematic_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_thematic;
    $country_list = (array) $response_json->diaServerResponse[0]->facet_counts->facet_fields->country;

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

}

$page_url_params = '?q=' . urlencode($query)  . '&filter=' . urlencode($user_filter);

$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);

$home_url = isset($cc_config['home_url_' . $lang]) ? $cc_config['home_url_' . $lang] : $cc_config['home_url'];
$plugin_title = isset($cc_config['plugin_title_' . $lang]) ? $cc_config['plugin_title_' . $lang] : $cc_config['plugin_title'];

if ( function_exists( 'pll_the_languages' ) ) {
    $available_languages = pll_languages_list();
    $available_languages_name = pll_languages_list(array('fields' => 'name'));
    $default_language = pll_default_language();
}

?>

<?php include('header.php') ?>

<section class="container" id="main_container">
    <nav>
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <!--<a href="<?php echo ($home_url != '') ? $home_url : real_site_url() ?>"><?php _e('Home','cc'); ?></a>-->
                <a href="<?php echo ($home_url != '') ? $home_url : real_site_url() ?>"><?php _e('Home','cc'); ?></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo real_site_url($cc_plugin_slug); ?>"><?php echo $plugin_title ?></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <?php
                       echo $center_list->title;
                ?>
            </li>
        </ol>
    </nav>

	<div class="row">
    <div class="col-12 cc-banner">
                </div>
        <div class="col-12 col-md-8 col-lg-9">
            <div class="row">
            
                <?php

                    $pos++;
                    $resource = $center_list;
                    echo '<article class="col-lg-12">';
                    echo '<h1>' . $center_list->title . '</h1>';
                    echo '<div class="box1">';
                    //echo '<span class="badge text-bg-info">' . strval( intval($start) + $pos ) . '/' . $total . '</span>';
                    echo '<h3 class="box1Title">';
                    echo $center_list->title;
                    if ($resource->status == '2'){
                        echo ' <span class="badge text-bg-warning">' . __('INACTIVE', 'cc') . '</span>';
                    }elseif($resource->status == '3'){
                        echo ' <span class="badge text-bg-warning">' . __('CLOSED', 'cc') . '</span>';
                    }
                    echo '<br/>';
                    if ($resource->django_id){
                        echo '<small>';
                            echo $resource->django_id . '<br/>';
                        echo '</small>';
                    }
                    echo '</h3>';
                    echo '<table class="table table-sm table-detail">';
                    echo '<tr>';
                    echo '  <td ></td>';
                    echo '  <td>' . $resource->cooperative_center_code . '</td>';
                    echo '</tr>';
                
                    if ($resource->subtitle){
                    echo '<tr>';
                    echo '  <td >Subtitulo</td>';
                    echo '  <td colspan="3">' . $center_list->subtitle . '</td>';
                    echo '</tr>';
                    }

                    if ($resource->section){
                        foreach ( $resource->section as $value ){
                        echo '<tr>';
                        echo '  <td >Seção/Parte:</td>';
                        echo '  <td>'.$value . '</td>';
                        echo '</tr>';
                        }
                    }

                    echo '<tr>';
                    echo '  <td ><b>Titulo abreviado:</b>';
                    echo '  ' . $center_list->shortened_title . '</td>';
                    echo '</tr>';

                    echo '<tr>';
                    echo '  <td >ISSN:</td>';
                    echo '  <td >' . $center_list->issn . '</td>';
                    echo '  <td width="220px">' . '</td>';
                    echo '</tr>';


                    /*
                    echo '<tr>';
                    echo '  <td >Editora: </td>';
                    echo '  <td>' . $center_list->medline_shortened_title . '</td>';
                    echo '</tr>';
                    */
                    if ($resource->responsibility_mention){
                    echo '<tr>';
                    echo '  <td >Menção de responsabilidade:</td>';
                    echo '  <td>' . $center_list->responsibility_mention . '</td>';
                    echo '</tr>';
                    }
                    if ($resource->comercial_editor){
                        echo '<tr>';
                        echo '  <td >Editor:</td>';
                        echo '  <td>' . $center_list->comercial_editor . '</td>';
                        echo '</tr>';
                    }

                    if ($resource->city){
                    echo '<tr>';
                    echo '  <td >Cidade:</td>';
                    echo '  <td> ' . $center_list->city . '</td>';
                    echo '</tr>';
                    }

                    if ($resource->country){
                        foreach ( $resource->country as $value ){
                    echo '<tr>';
                    echo '  <td>País</td>';
                    echo '  <td>' . $value . '</td>';
                    echo '</tr>';
                        }
                    }

                    if ($resource->initial_date){
                        echo '<tr>';
                        echo '  <td >Publicação iniciada em: </td>';
                        echo '  <td> ' . $center_list->initial_date . '</td>';
                        echo '</tr>';
                        }

                    if ($resource->final_date){
                            echo '<tr>';
                            echo '  <td >Publicação encerrada em: </td>';
                            echo '  <td> ' . $center_list->final_date . '</td>';
                            echo '</tr>';
                    }
                    if($center_list->continued_by != ''){
                        echo '<tr>';
                        echo '  <td >Continuação parcial de:</td>';
                        echo '    <td>';
                        foreach ( $resource->continued_by as $values){
                            echo $values . ' <BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    if($center_list->partial_continuation != ''){
                        echo '<tr>';
                        echo '  <td >Continuação parcial de:</td>';
                        echo '    <td>';
                        foreach ( $resource->partial_continuation as $values){
                            echo $values . ' <BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    if($center_list->thematic_area != ''){
                    echo '<tr>';
                    echo '  <td >Área temática:</td>';
                    echo '  <td> ' . $center_list->thematic_area . '</td>';
                    echo '</tr>';
                    }  
                    if($center_list->descriptors != ''){
                        echo '<tr>';
                        echo '  <td >Assuntos:</td>';
                        echo '    <td>';
                        foreach ( $resource->descriptors as $descriptors){
                            echo $descriptors . ' <BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    if ($resource->secs_number){
                        echo '<tr>';
                        echo '    <td>No secs Bireme:</td>';
                        echo '    <td>';
                        echo '      <a href="https://www.google.com/maps/search/' . $resource->secs_number . '" target="_blank">' . $resource->secs_number . '</a>';
                        echo '    </td>';
                        echo '</tr>';
                    }     
                    if ($resource->online){
                        echo '<tr>';
                        echo '  <td valign="top"><i class="fas fa-table"></i></td>';
                        echo '    <td>';
                        $exclude_common_types = array('CooperatingCenters', 'ParticipantsUnits', 'VHLNetwork');
                        foreach ( $resource->online as $type ){
                            echo $type_translated[$type] . '<br/>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    echo '</table>';
                    echo '</div>';
                    echo '</article>';
                ?>

            </div> <!-- /row results area -->
            <hr>
            <?php echo $pages->display_pages(); ?>
        </div> <!-- /col results area -->


        <div class="col-md-4 col-lg-3" id="filterRight">
            <div class="boxFilter">
                <?php if ($applied_filter_list) :?>
                    <form method="get" name="searchFilter" id="formFilters" action="<?php echo site_url($lang_slug . '/' . $cc_plugin_slug) . 'results.php?results'?>">
                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                        <input type="hidden" name="q" id="query" value="<?php echo $query; ?>" >
                        <input type="hidden" name="filter" id="filter" value="" >

                        <?php foreach ( $applied_filter_list as $ap_filter => $ap_filter_values ) :?>
                            <?php if ($ap_filter != 'country_code'): ?>
                                <h5><?php echo $filter_title_translated[$ap_filter]; ?></h5>
                                <ul>
                                <?php foreach ( $ap_filter_values as $value ) :?>
                                    <input type="hidden" name="apply_filter" class="apply_filter"
                                            id="<?php echo md5($value) ?>" value='<?php echo $ap_filter . ':"' . $value . '"'; ?>' >
                                    <li>
                                        <span class="filter-item">
                                            <?php
                                                if ($ap_filter == 'country'){
                                                    echo print_lang_value($value, $site_language);
                                                }elseif ($ap_filter == 'institution_type'){
                                                    echo $type_translated[$value];
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
                <?php endif; ?>
<!--
                <section>
                    <h5 class="box1Title"><?php _e('Filtros','cc'); ?></h5>
                    <?php var_dump($resource);?>
                    <ul class="filter-list">
                        <?php foreach ( $type_list as $type ) { ?>
                            <li class="cat-item">
                                <?php
                                    $filter_link = '?';
                                    if ($query != ''){
                                        $filter_link .= 'q=' . $query . '&';
                                    }
                                    $filter_link .= 'filter=institution_type:"' . $type[0] . '"';
                                    if ($user_filter != ''){
                                        $filter_link .= ' AND ' . $user_filter ;
                                    }
                                ?>
                                <a href='<?php echo $filter_link; ?>'><?php echo $type_translated[$type[0]]; ?></a>
                                <span class="cat-item-count">(<?php echo $type[1]; ?>)</span>
                            </li>
                        <?php } ?>
                    </ul>
                </section>

                <?php if ($thematic_list): ?>
                    <section>
                        <h5 class="box1Title"><?php _e('Thematic Networks','cc'); ?></h5>
                        <ul class="filter-list">
                            <?php foreach ($thematic_list as $thematic) { ?>
                                <?php
                                    $filter_link = '?';
                                    if ($query != ''){
                                        $filter_link .= 'q=' . $query . '&';
                                    }
                                    $filter_link .= 'filter=institution_thematic:"' . $thematic[0] . '"';
                                    if ($user_filter != ''){
                                        $filter_link .= ' AND ' . $user_filter ;
                                    }
                                ?>
                                <li class="cat-item">
                                    <a href='<?php echo $filter_link; ?>'><?php echo $thematic_translated[$thematic[0]] ?></a>
                                    <span class="cat-item-count">(<?php echo $thematic[1] ?>)</span>
                                </li>
                            <?php } ?>
                        </ul>
                    </section>
                <?php endif; ?>

                <?php if ($country_list): ?>
                    <section>
                        <h5 class="box1Title"><?php _e('Country','cc'); ?></h5>
                        <ul class="filter-list">
                            <?php foreach ( $country_list as $country ) { ?>
                                <?php
                                    $filter_link = '?';
                                    if ($query != ''){
                                        $filter_link .= 'q=' . $query . '&';
                                    }
                                    $filter_link .= 'filter=country:"' . $country[0] . '"';
                                    if ($user_filter != ''){
                                        $filter_link .= ' AND ' . $user_filter ;
                                    }
                                ?>
                                <li class="cat-item">
                                    <a href='<?php echo $filter_link; ?>'><?php print_lang_value($country[0], $site_language)?></a>
                                    <span class="cat-item-count">(<?php echo $country[1] ?>)</span>
                                </li>
                            <?php } ?>
                        </ul>
                        <?php if ( count($country_list) == 20 ) : ?>
                            <div class="show-more text-center">
                                <a href="javascript:void(0)" class="btn-ajax" data-fb="30" data-cluster="country"><?php _e('show more','cc'); ?></a>
                                <a href="javascript:void(0)" class="loading"><?php _e('loading','cc'); ?>...</a>
                            </div>
                        <?php endif; ?>
                    </section>
                <?php endif; ?>
                        -->
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
