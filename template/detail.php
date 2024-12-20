<?php
/*
Template Name: CC Home
*/
header_remove("X-Frame-Options");

// Ou, para permitir apenas um domínio específico
header("X-Frame-Options: ALLOW-FROM https:////contacto.bvsalud.org");
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

$cc_search = $cc_service_url . 'api/title/'.$ide.'/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang . '&format=json';

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
//echo $cc_search;
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

/*/////////////////////////////////cent4ro cooperantes para colections = treco a ser removido

$cc_search2 = $cc_service_url . 'api/institution/';
$cesta = array();



$response = @file_get_contents($cc_search2);

if ($response){
    $response_json = json_decode($response);
    $cop_list = $response_json->response;
}

foreach ( $cop_list as $resource) {
    array_push($cesta, $resource->cc_code);
}
////////////////////////*/

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
                <?php _e('Home','cc'); ?>
            </li>
            <li class="breadcrumb-item">
                <a href="<?php echo real_site_url($cc_plugin_slug); ?>"><?php echo $plugin_title ?></a>
            </li>
            <li class="breadcrumb-item" aria-current="page">
                <?php echo $center_list->title;?>
            </li>
        </ol>
    </nav>

	<div class="row">
    <div class="col-12 cc-banner">
                </div>
        <div class="col-12 col-md-10 col-lg-10">
            <div class="row">
                <?php
                    $pos++;
                    $resource = $center_list;
                    echo '<article class="col-lg-12">';
                    echo '<div class="box1">';
                    echo '<h1 class="tituloArtigo" style="font-size:1.5rem; padding-left: 6px;">' . $center_list->title . '</h1>';
                    //echo '<span class="badge text-bg-info">' . strval( intval($start) + $pos ) . '/' . $total . '</span>';
                    //echo $center_list->title;

                    if ($resource->status == '1'){
                       //echo ' <span class="badge text-bg-warning">' . __('INACTIVE', 'cc') . '</span>';
                    }else{
                       // echo ' <span class="badge text-bg-warning">' . __('CLOSED', 'cc') . '</span>';
                    }
                    echo '<br>';
                    echo '<table class="table table-sm table-detail">';
                    echo '<tr>';
                    echo '  <td style="min-width:140px;"></td>';
                    echo '</tr>';
                
                    if ($resource->status){
                        if($resource->status == 'C'){
                            $status = 'Corrente';
                        }else{
                            $status = 'Encerrado';
                        }
                        echo '<tr>';
                        echo '  <td >Status</td>';
                        echo '  <td colspan="3">' . $status . '</td>';
                        echo '</tr>';
                        }
    
                    if ($resource->subtitle){
                        echo '<tr>';
                        echo '  <td>'. __('Subtitulo','cc') . ':</td>';
                        echo '  <td colspan="3">' . $center_list->subtitle . '</td>';
                        echo '</tr>';
                    }

                    if ($resource->section){
                        foreach ( $resource->section as $value ){
                        echo '<tr>';
                        echo '  <td>'. __('Seção/Parte','cc') . ':</td>';
                        echo '  <td>'.$value . '</td>';
                        echo '</tr>';
                        }
                    }

                    echo '<tr>';
                    echo '  <td>'. __('Titulo abreviado','cc') . ':</td>';
                    echo '  <td >'. $center_list->shortened_title . '</td>';
                    echo '</tr>';

                    if ($resource->parallel_titles){
                        foreach ( $resource->parallel_titles as $value ){
                            echo '<tr>';
                            echo '  <td>'. __('Titulo paralelo','cc') . ':</td>';
                            echo '  <td>' . tratarVariacoes($value) . '</td>';
                            echo '</tr>';
                        }
                    }

                    if ($resource->other_titles){
                        echo '<tr>';
                        echo '  <td>'. __('Outras variações','cc') . ':</td>';
                        echo '  <td>'; 
                        foreach ( $resource->other_titles as $values){
                            //echo tratarVariacoes($values) . '<BR>';

                            echo '<a = href="' . site_url($lang . '/' . $cc_plugin_slug) . '?lang=' . $lang . '&q=' . issnVariacoes($values) . '">';
                            echo tratarVariacoes($values) . ' </a><BR>';
                        }
                        echo '</td>';
                        echo '</tr>';
                    }

                    echo '<tr>';
                    echo '  <td >ISSN:</td>';
                    echo '  <td >' . $center_list->issn . '</td>';
                    echo '</tr>';

                    /*
                    echo '<tr>';
                    echo '  <td >Editora: </td>';
                    echo '  <td>' . $center_list->medline_shortened_title . '</td>';
                    echo '</tr>';
                    */

                    if ($resource->responsibility_mention){
                        echo '<tr>';
                        echo '  <td>'. __('Menção de responsabilidade','cc') . ':</td>';
                        echo '  <td>' . $center_list->responsibility_mention . '</td>';
                        echo '</tr>';
                    }
                    if ($resource->editor_cc_code){
                        echo '<tr>';
                        echo '  <td>'. __('Código do editor','cc') . ':</td>';
                        echo '  <td>';
                        echo  "<a href='https://bvsalud.org/centros?lang=pt&q=".$center_list->editor_cc_code."' target='_blank'>";                        
                        echo $center_list->editor_cc_code . '</a></td>';
                        echo '</tr>';
                    }
                    if ($resource->comercial_editor){
                        echo '<tr>';
                        echo '  <td>'. __('Editora','cc') . ':</td>';
                        echo '  <td>' . $center_list->comercial_editor . '</td>';
                        echo '</tr>';
                    }

                    if ($resource->city){
                        echo '<tr>';
                        echo '  <td>'. __('Cidade','cc') . ':</td>';
                        echo '  <td> ' . $center_list->city . '</td>';
                        echo '</tr>';
                    }

                    if ($resource->country){
                        foreach ( $resource->country as $value ){
                            echo '<tr>';
                            echo '  <td>'. __('País','cc') . ':</td>';
                            echo '  <td>' . $value . '</td>';
                            echo '</tr>';
                        }
                    }
                    if ($resource->frequency){
                        echo '<tr>';
                        echo '  <td>'. __('Periodicidade','cc') . ':</td>';
                        echo '  <td> ' . tratarFrequencia($center_list->frequency, $lang) . '</td>';
                        echo '</tr>';
                        }
                    if ($resource->initial_date){
                        echo '<tr>';
                        echo '  <td>'. __('Publicação iniciada em','cc') . ':</td>';
                        echo '  <td> ' . $center_list->initial_date . '</td>';
                        echo '</tr>';
                        }

                    if ($resource->final_date){
                        echo '<tr>';
                        echo '  <td >Publicação encerrada em: </td>';
                        echo '  <td> ' . $center_list->final_date . '</td>';
                        echo '</tr>';
                    }
                    if($center_list->continuation[0] != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Titulo anterior','cc') . ':</td>';
                        echo '    <td>';
                        foreach ( $resource->continuation as $values){
                            echo '<a = href="' . site_url($lang . '/' . $cc_plugin_slug) . '?lang=' . $lang . '&q=' . issnVariacoes($values) . '">';
                            echo tratarVariacoes($values) . ' </a><BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    if($center_list->fusion[0] != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Fusão com ... de ... ','cc') . ':</td>';
                        echo '    <td>';
                        foreach ( $resource->fusion as $values){
                            echo '<a = href="' . site_url($lang . '/' . $cc_plugin_slug) . '?lang=' . $lang . '&q=' . issnVariacoes($values) . '">';
                            echo tratarVariacoes($values) . ' </a><BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    if($center_list->continued_by[0] != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Continuação por','cc') . ':</td>';
                        echo '    <td>';
                        foreach ( $resource->continued_by as $values){
                            echo '<a = href="' . site_url($lang . '/' . $cc_plugin_slug) . '?lang=' . $lang . '&q=' . issnVariacoes($values) . '">';
                            echo tratarVariacoes($values) . ' </a><BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    if($center_list->partial_continuation[0] != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Continuação parcial de','cc') . ':</td>';
                        echo '    <td>';
                        foreach ( $resource->partial_continuation as $values){
                            echo '<a = href="' . site_url($lang . '/' . $cc_plugin_slug) . '?lang=' . $lang . '&q=' . issnVariacoes($values) . '">';
                            echo tratarVariacoes($values) . ' </a><BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }

                    if($center_list->absorbed[0] != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Absorveu a','cc') . ':</td>';
                        echo '    <td>';
                        foreach ( $resource->absorbed as $absorbed){
                            echo '<a = href="' . site_url($lang . '/' . $cc_plugin_slug) . '?lang=' . $lang . '&q=' . issnVariacoes($absorbed) . '">';
                            echo tratarVariacoes($absorbed) . '</a> <BR>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }

                    if($center_list->thematic_area != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Área temática','cc') . ':</td>';
                        echo '  <td> ' . $center_list->thematic_area . '</td>';
                        echo '</tr>';
                    }  
                    if($center_list->descriptors != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Assuntos','cc') . ':</td>';
                        echo '    <td>';
                        foreach ( $resource->descriptors as $descriptors){
                            echo $descriptors . ' <BR>';
                        }
                        echo '</td></tr>';
                    }
                    if($center_list->index_range != ''){
                        echo '<tr>';
                        echo '  <td>'. __('Título indexado em','cc') . ':</td>';
                        echo '    <td colspan="2">';
                        foreach ( $resource->index_range as $index_range){
                            echo tratarIndexo($index_range) .  ' <BR>';
                        }
                        echo '</td></tr>';
                    }
                    if($center_list->national_code != ''){
                        echo '<tr>';
                        echo '  <td>'. __('No. CCN Brasil','cc') . ':</td>';
                        echo '    <td>';
                        echo  $center_list->national_code . ' <BR>';
                        echo '</td></tr>';
                    }
                    if ($resource->secs_number){
                        echo '<tr>';
                        echo '    <td>No. SECS Bireme:</td>';
                        echo '    <td>';
                        echo $resource->secs_number;
                        echo '</td></tr>';
                    }     

function separar_indices($texto) {
    // Regex para capturar os campos ^a até ^f
    preg_match_all('/\^a([^\^]*)/', $texto, $matches_a);
    preg_match_all('/\^b([^\^]*)/', $texto, $matches_b);
    preg_match_all('/\^c([^\^]*)/', $texto, $matches_c);
    preg_match_all('/\^d([^\^]*)/', $texto, $matches_d);
    preg_match_all('/\^e([^\^]*)/', $texto, $matches_e);
    preg_match_all('/\^f([^\^]*)/', $texto, $matches_f);
    
    $i = 0;
    // Iterar através dos resultados e construir um array associativo
    //for ($i = 0; $i < count($matches_a[1]); $i++) {
        $resultados[] = [
            'a' => isset($matches_a[1][$i]) ? $matches_a[1][$i] : '',
            'b' => isset($matches_b[1][$i]) ? $matches_b[1][$i] : '',
            'c' => isset($matches_c[1][$i]) ? $matches_c[1][$i] : '',
            'd' => isset($matches_d[1][$i]) ? $matches_d[1][$i] : '',
            'e' => isset($matches_e[1][$i]) ? $matches_e[1][$i] : '',
            'f' => isset($matches_f[1][$i]) ? $matches_f[1][$i] : ''
        ];
    //}
    
    return $resultados;
}
?>
<!--<iframe id="contact-form" width="100%" height="645px" src="//contacto.bvsalud.org/chat.php?group=e-blueinfo&ptl=<?php echo $contact_lang; ?>&hg=Pw__&hcgs=MQ__&htgs=MQ__&hinv=MQ__&hfk=MQ__" frameborder="0" scrolling="no"></iframe>        
-->
<?php
function tratarFrequencia($texto, $olang) {
    if ($olang == 'en') {
        $texto = str_replace("Q", "Quarterly", $texto);
        if($texto == 'T'){ $texto = str_replace("T", "Four-monthly", $texto); }
        $texto = str_replace("S", "Bimonthly", $texto);
        $texto = str_replace("A", "Annual", $texto);
        $texto = str_replace("B", "Bimonthly", $texto);
        $texto = str_replace("C", "Biweekly", $texto);
        $texto = str_replace("D", "Daily", $texto);
        $texto = str_replace("E", "Fortnightly", $texto);
        $texto = str_replace("F", "Semiannual/Biannual", $texto);
        $texto = str_replace("G", "Biennial", $texto);
        $texto = str_replace("H", "Triennial", $texto);
        $texto = str_replace("I", "Three times a week", $texto);
        $texto = str_replace("J", "Three times a month", $texto);
        $texto = str_replace("K", "Irregular", $texto);
        $texto = str_replace("M", "Monthly", $texto);
        $texto = str_replace("U", "Continuous publication", $texto);
        $texto = str_replace("W", "Weekly", $texto);
        $texto = str_replace("Z", "Other frequencies", $texto);
        if ($texto == '') {
            $texto = 'Unknown frequency';
        }
    } elseif ($olang == 'es') {
        $texto = str_replace("Q", "Trimestral", $texto);
        if($texto == 'T'){ $texto = str_replace("T", "Cuatrimestral", $texto); }
        $texto = str_replace("S", "Bimestral", $texto);
        $texto = str_replace("A", "Anual", $texto);
        $texto = str_replace("B", "Bimestral", $texto);
        $texto = str_replace("C", "Bisemanario", $texto);
        $texto = str_replace("D", "Diario", $texto);
        $texto = str_replace("E", "Quincenal", $texto);
        $texto = str_replace("F", "Semestral/Bianual", $texto);
        $texto = str_replace("G", "Bienal", $texto);
        $texto = str_replace("H", "Trienal", $texto);
        $texto = str_replace("I", "Tres veces por semana", $texto);
        $texto = str_replace("J", "Tres veces al mes", $texto);
        $texto = str_replace("K", "Irregular", $texto);
        $texto = str_replace("M", "Mensual", $texto);
        $texto = str_replace("U", "Publicación continua", $texto);
        $texto = str_replace("W", "Semanal", $texto);
        $texto = str_replace("Z", "Otras frecuencias", $texto);
        if ($texto == '') {
            $texto = 'Frecuencia desconocida';
        }
    } else { // Default to Portuguese
        $texto = str_replace("Q", "Trimestral", $texto);
        if($texto == 'T'){ $texto = str_replace("T", "Quadrimestral", $texto); }
        $texto = str_replace("S", "Bimensal", $texto);
        $texto = str_replace("A", "Anual", $texto);
        $texto = str_replace("B", "Bimestral", $texto);
        $texto = str_replace("C", "Bissemanal", $texto);
        $texto = str_replace("D", "Diário", $texto);
        $texto = str_replace("E", "Quinzenal", $texto);
        $texto = str_replace("F", "Semestral/Bianual", $texto);
        $texto = str_replace("G", "Bienal", $texto);
        $texto = str_replace("H", "Trienal", $texto);
        $texto = str_replace("I", "Três vezes por semana", $texto);
        $texto = str_replace("J", "Três vezes por mês", $texto);
        $texto = str_replace("K", "Irregular", $texto);
        $texto = str_replace("M", "Mensal", $texto);
        $texto = str_replace("U", "Publicação contínua", $texto);
        $texto = str_replace("W", "Semanal", $texto);
        $texto = str_replace("Z", "Outras frequências", $texto);
        if ($texto == '') {
            $texto = 'Frequência desconhecida';
        }
    }
    return $texto;
}
function susbtituirSiglas($texto){
    $texto = str_replace("ALAP", "Permitido para assinantes do formato impresso", $texto);
    $texto = str_replace("AAEL", "Para assinantes do formato eletrônico", $texto);
    $texto = str_replace("ACOP", "Não disponível", $texto);
    $texto = str_replace("ALIV", "Gratuito", $texto);
    return $texto;
}
function susbtituirSiglasD($texto){
    $texto = str_replace("IP", "Acesso controlado por IP", $texto);
    $texto = str_replace("PASS", "Acesso controlado por senha", $texto);
    $texto = str_replace("LIVRE", "Livre acesso", $texto);
    $texto = str_replace("IP/PASS", "Acesso controlado por IP e senha", $texto);
    return $texto;
}
function substituirSiglasIndexado($texto){
    //$texto = explode('^n', $texto);
    //$texto = str_replace("LL", "LILACS", $texto);
    //$texto = str_replace("BA", "BIOLOGICAL ABSTRACTS", $texto);
    //$texto = str_replace("IM", "INDEX MEDICUS", $texto);
    ///$texto = str_replace("EM", "EXCERPTA MEDICA", $texto);
    return $texto;
}
function tratarIndexo($texto){
    preg_match('/\^n([^\\^]*)/', $texto, $nMatch);
    preg_match('/\^a([^\\^]*)/', $texto, $aMatch);
    preg_match('/\^b([^\\^]*)/', $texto, $bMatch);
    preg_match('/\^c([^\\^]*)/', $texto, $cMatch);
    preg_match('/\^d([^\\^]*)/', $texto, $dMatch);
    preg_match('/\^e([^\\^]*)/', $texto, $eMatch);
    preg_match('/\^f([^\\^]*)/', $texto, $fMatch);
    preg_match('/\^g([^\\^]*)/', $texto, $gMatch);
    preg_match('/\^h([^\\^]*)/', $texto, $hMatch);
    preg_match('/\^i([^\\^]*)/', $texto, $iMatch);

    // O valor de ^n é obrigatório
    $nValue = isset($nMatch[1]) ? $nMatch[1] : '';

    // Cria um array de valores de subcampos (de ^a a ^i)
    $subcampos = [];

    // Adiciona cada valor ao array de subcampos, se existir
    if (isset($aMatch[1])) $subcampos[] = $aMatch[1];
    if (isset($bMatch[1])) $subcampos[] = $bMatch[1];
    if (isset($cMatch[1])) $subcampos[] = $cMatch[1];
    if (isset($dMatch[1])) $subcampos[] = $dMatch[1];
    if (isset($eMatch[1])) $subcampos[] = $eMatch[1];
    if (isset($fMatch[1])) $subcampos[] = $fMatch[1];
    if (isset($gMatch[1])) $subcampos[] = $gMatch[1];
    if (isset($hMatch[1])) $subcampos[] = $hMatch[1];
    if (isset($iMatch[1])) $subcampos[] = $iMatch[1];

    // Concatena o valor de ^n com os outros subcampos, separados por vírgulas
    $resultado = $nValue;

    if (!empty($subcampos)) {
        // Adiciona a vírgula entre o valor de ^n e os outros subcampos, se existirem
        $resultado .= ', ' . implode(', ', $subcampos);
    }

    return $resultado;
}
function tratarIndexadoEm($texto){
    //////////////////////////
    $texto = str_replace("LL^a", "LILACS; ", $texto);
    $texto = str_replace("BA^a", "BIOLOGICAL ABSTRACTS; ", $texto);
    $texto = str_replace("IM^a", "INDEX MEDICUS; ", $texto);
    $texto = str_replace("EM^a", "EXCERPTA MEDICA; ", $texto);

    $array = explode('^', $texto);
    $texto = $array[1];
    $array = explode('^n', $texto);
    $texto = $array[1] . $array[0];

    ////////////////////////////////
    $texto = str_replace("^a", "; ", $texto);
    $texto = str_replace("^b", ", ", $texto);
    $texto = str_replace("^c", ", ", $texto);
    $texto = str_replace("^d", ", ", $texto);
    $texto = str_replace("^e", ", ", $texto);
    $texto = str_replace("^f", ", ", $texto);
    $texto = str_replace("^g", ", ", $texto);
    $texto = str_replace("^h", ", ", $texto);
    $texto = str_replace("^i", ", ", $texto);
    return $texto;
}
function tratarVariacoes($texto){
    $texto = str_replace("^a", ", ", $texto);
    $texto = str_replace("^i", " - issn: ", $texto);
    return $texto;
}
function issnVariacoes($texto){
    $partes = explode('^i', $texto);

    if($partes[1] != ''){
        return $partes[1];
    }else{
        return $partes[0];
    }

}

function tratarOnlineNotes($texto, $n){
    $texto = str_replace("^xempty", "", $texto);
    $texto = str_replace("^z".$n, "", $texto);
    if(strpos($texto, '^xempty')> 0){
        return '';
    }
    return $texto;
}
                    //prepara o array de notas 
                    if ($resource->online_notes){
                        $countnotes = 1;
                        foreach ( $resource->online_notes as $type ){
                            $arraynotes[$countnotes] = tratarOnlineNotes($type, $countnotes);
                            $countnotes++;
                        }
                    }

                    if ($resource->online){

                        echo '<tr>';
                        echo '  <td valign="top">' .  __('Formato eletrônico','cc') . '</td>';
                        echo '    <td colspan="3">';
                        $exclude_common_types = array('CooperatingCenters', 'ParticipantsUnits', 'VHLNetwork');
                        $no = 1;
                        $countonline = 1;
                        foreach ( $resource->online as $type){
                          //echo $type . '<br/>';
                            $type = susbtituirSiglas($type);
                            $resultados = separar_indices($type);
                                            
                            //a - Texto Completo
                            //^b - url
                            //^c -
                            // e - Disponível a partir de   	
                            // f -  Terminando em   	
                            // Exibir os resultados
foreach ($resultados as $resultado) {
    echo "<b>" . __('Opção','cc')  . $no . "</b>";
    if($resultado['a']){
    echo "<BR>" . __('Texto Completo','cc') . ': ' .susbtituirSiglas($resultado['a']) . ' - ' . susbtituirSiglasD($resultado['d']);
    }
    echo ",<br> URL: " . $resultado['b'] . " (<a href='" .  $resultado['b']  ."' target='_blank'><i class='fas fa-long-arrow-right'></i>visitar link</a>)";
    echo ($resultado['c'] != '') ? "<BR>" . __('Agregador/Fornecedor','cc') . ": " . $resultado['c'] : '';
    echo ($resultado['e'] != '') ? "<BR>" . __('Disponível a partir de','cc') . ": " . $resultado['e'] : '';
    echo ($resultado['f'] != '') ? " <BR>Terminando em : " . $resultado['f'] : '';
    echo "<br>";
    $no += 1;
    echo PHP_EOL;
}

echo ($arraynotes[$countonline] != '') ? 'Notas:' . $arraynotes[$countonline] : '';
$countonline++;
echo "<Br><Br>";



                        }
                        echo '   </td>';
                        echo '</tr>';
                    }



                    if ($resource->online_type){
                        echo '<tr>';
                        echo '  <td valign="top"><i class="fas fa-table"></i>online type</td>';
                        echo '    <td>';
                        foreach ( $resource->online_type as $type ){
                            echo $type . '<br/>';
                        }
                        echo '   </td>';
                        echo '</tr>';
                    }
                    ///////^p -portugues, i- ingles, ê -espanhol


                    if ($resource->collection){
                        echo '<tr colspan="2">';
                        echo '  <td valign="top"><button class="btnDetalhes" id="openModalBtn">' . __('Coleções no Catálogo Coletivo SeCS', 'cc') . '</button></td>';
                        /*echo '    <td>';
                        foreach ( $resource->collection as $type ){
                            echo nl2br($type) . '<BR>';
                        }
                        echo '   </td>';*/
                        echo '</tr>';
                    }
                    include 'modal.php';
                    echo '</table>';
                    echo '</div>';
                    echo '</article>';
                ?>
<div class="row" style="overflow:hidden;">
<?php
/*

$remote_url = 'https://contacto.bvsalud.org/chat.php?group=e-blueinfo&ptl=' . $_GET['lang'] . '&hg=Pw__&hcgs=MQ__&htgs=MQ__&hinv=MQ__&hfk=MQ__';

// Inicia a solicitação cURL
$ch = curl_init($remote_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HEADER, false);

// Obter o conteúdo
$response = curl_exec($ch);
curl_close($ch);

// Exibe o conteúdo como se fosse local
echo $response;
*/
?>
</div>
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
