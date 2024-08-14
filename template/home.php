<?php
/*
Template Name: CC Home
*/
global $cc_service_url, $cc_plugin_slug, $cc_plugin_title;

require_once(CC_PLUGIN_PATH . '/lib/Paginator.php');

$cc_config = get_option('cc_config');
$cc_initial_filter = $cc_config['initial_filter'];

$site_language = strtolower(get_bloginfo('language'));
$lang = substr($site_language,0,2);

$query = ( isset($_GET['s']) ? sanitize_text_field($_GET['s']) : sanitize_text_field($_GET['q']) );
$query = stripslashes($query);
$sanitize_user_filter = sanitize_text_field($_GET['filter']);
$user_filter = stripslashes($sanitize_user_filter);
$page = ( isset($_GET['page']) ? sanitize_text_field($_GET['page']) : 1 );
$total = 0;
$count = 10;
$filter = '';

if ($cc_initial_filter != ''){
    if ($user_filter != ''){
        $filter = $cc_initial_filter . ' AND ' . $user_filter;
    }else{
        $filter = $cc_initial_filter;
    }
}else{
    $filter = $user_filter;
}
$start = ($page * $count) - $count;

$cc_search = $cc_service_url . 'api/institution/search/?q=' . urlencode($query) . '&fq=' . urlencode($filter) . '&start=' . $start . '&lang=' . $lang;

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
    $total = $response_json->diaServerResponse[0]->response->numFound;
    $start = $response_json->diaServerResponse[0]->response->start;
    $legislation_list = $response_json->diaServerResponse[0]->response->docs;

    $type_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_type;
    $thematic_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->institution_thematic;
    $country_list = $response_json->diaServerResponse[0]->facet_counts->facet_fields->country;
}

$page_url_params = '?q=' . urlencode($query)  . '&filter=' . urlencode($filter);

$pages = new Paginator($total, $start, $count);
$pages->paginate($page_url_params);

$home_url = isset($cc_config['home_url_' . $lang]) ? $cc_config['home_url_' . $lang] : real_site_url();

/* filters translations */
$type_translated['CoordinatingCentersRg'] = __('CoordinatingCentersRg','cc');
$type_translated['CoordinatingCentersNc'] = __('CoordinatingCentersNc','cc');
$type_translated['CooperatingCenters'] = __('CooperatingCenters','cc');
$type_translated['CooperatingCentersLILACS'] = __('CooperatingCentersLILACS','cc');
$type_translated['CooperatingCentersLEYES'] = __('CooperatingCentersLEYES','cc');
$type_translated['ParticipantsUnits'] = __('ParticipantsUnits','cc');
$type_translated['VHLNetwork'] = __('VHLNetwork','cc');
$type_translated['EDITOR'] = __('Editor','cc');

$thematic_translated['MedCarib'] = __('MedCarib','cc');
$thematic_translated['Nursing'] = __('Nursing','cc');
$thematic_translated['Border'] = __('Border','cc');
$thematic_translated['Disastres'] = __('Disastres','cc');
$thematic_translated['Psychology'] = __('Psychology','cc');
$thematic_translated['MTCI'] = __('MTCI','cc');

?>

<?php include('header.php') ?>

<section class="container" id="main_container">
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="<?php echo $home_url ?>"><?php _e('Home','cc'); ?></a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">
                    <?php _e('VHL Network Directory', 'cc'); ?>
                </li>
            </ol>
        </nav>
		<div class="row">
			<div class="col-12 col-md-8 col-lg-9">
				<div class="row">
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=ar'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/argentina.jpg" alt="" class="img-fluid">
                            <h4>Argentina</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=bs'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/bahamas.jpg" alt="" class="img-fluid">
                            <h4>Bahamas</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=bz'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/belize.jpg" alt="" class="img-fluid">
                            <h4>Belize</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=bo'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/bolivia.jpg" alt="" class="img-fluid">
                            <h4>Bolívia</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=br'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/brasil.jpg" alt="" class="img-fluid">
                            <h4>Brasil</h4>
                        </a>
                        <!--
                        <select name="" id="" class="form-control">
                            <option value="" selected disabled="">Selecione</option>
                            <option value="" disabled><b> -- Norte -- </b></option>
                            <option value="">Acre</option>
                            <option value="">Amapá</option>
                            <option value="">Amazônia</option>
                            <option value="">Pará</option>
                            <option value="">Rondônia</option>
                            <option value="">Roraima</option>
                            <option value="">Tocantins</option>


                            <option value="" disabled><b> -- Nortedeste -- </b></option>
                            <option value="">Maranhão</option>
                            <option value="">Piauí</option>
                            <option value="">Ceará</option>
                            <option value="">Rio Grande do Norte</option>
                            <option value="">Paraíba</option>
                            <option value="">Pernambuco</option>
                            <option value="">Alagoas</option>
                            <option value="">Sergipe</option>
                            <option value="">Bahia</option>



                            <option value="" disabled><b> -- Centro Oeste -- </b></option>
                            <option value="">Mato Grosso</option>
                            <option value="">Goiás</option>
                            <option value="">Mato Grosso do Sul</option>
                            <option value="">DF</option>


                            <option value="" disabled><b> -- Sudeste -- </b></option>
                            <option value="">Paraná</option>
                            <option value="">Santa Catarina</option>
                            <option value="">Rio Grande do Sul</option>


                            <option value="" disabled><b> -- Sul -- </b></option>
                            <option value="">Minas Gerais</option>
                            <option value="">Espírito Santo</option>
                            <option value="">Rio de Janeiro</option>
                            <option value="">São Paulo</option>
                        </select>
                        -->
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/caribe.jpg" alt="" class="img-fluid">
                        <h4>Caribe</h4>
                        <select name="" id="" class="form-control">
                            <option value="" disabled selected>Seleciona</option>
                            <option value="">Ilhas Virgens Britanicas</option>
                            <option value="">Sao Cristovao e Nevis</option>
                            <option value="">Sao Vicete e Granadinas</option>
                            <option value="">Granada</option>
                            <option value="">Dominica</option>
                            <option value="">Santa Lucia</option>
                            <option value="">Barbados</option>
                            <option value="">Antigua</option>
                            <option value="">Anguila</option>
                        </select>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=cl'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/chile.jpg" alt="" class="img-fluid">
                            <h4>Chile</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=co'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/colombia.jpg" alt="" class="img-fluid">
                            <h4>Colômbia</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=cr'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/costa_rica.jpg" alt="" class="img-fluid">
                            <h4>Costa Rica</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=cu'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/cuba.jpg" alt="" class="img-fluid">
                            <h4>Cuba</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=sv'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/el_salvador.jpg" alt="" class="img-fluid">
                            <h4>El Salvador</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=ec'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/equador.jpg" alt="" class="img-fluid">
                            <h4>Equador</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=gt'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/guatemala.jpg" alt="" class="img-fluid">
                            <h4>Guatemala</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=gf'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/guiana.jpg" alt="" class="img-fluid">
                            <h4>Guiana</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=gf'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/francesa.jpg" alt="" class="img-fluid">
                            <h4>Guiana Francesa</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=ht'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/haiti.jpg" alt="" class="img-fluid">
                            <h4>Haiti</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=hn'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/honduras.jpg" alt="" class="img-fluid">
                            <h4>Honduras</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=jm'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/jamaica.jpg" alt="" class="img-fluid">
                            <h4>Jamaica</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=mx'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/mexico.jpg" alt="" class="img-fluid">
                            <h4>México</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=ni'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/nicaragua.jpg" alt="" class="img-fluid">
                            <h4>Nicarágua</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=pa'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/panama.jpg" alt="" class="img-fluid">
                            <h4>Panamá</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=py'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/paraguai.jpg" alt="" class="img-fluid">
                            <h4>Paraguai</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=pe'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/peru.jpg" alt="" class="img-fluid">
                            <h4>Peru</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=pr'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/porto_rico.jpg" alt="" class="img-fluid">
                            <h4>Porto Rico</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=do'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/republica_dominicana.jpg" alt="" class="img-fluid">
                            <h4>Rep. Dominicana</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=sr'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/suriname.jpg" alt="" class="img-fluid">
                            <h4>Suriname</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=tt'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/trinidad.jpg" alt="" class="img-fluid">
                            <h4>Trinidad e Tobago</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/uruguai.jpg" alt="" class="img-fluid">
                            <h4>Uruguai</h4>
                        </a>
                    </div>
                    <div class="col-6 col-lg-4 col-xl-3 country">
                        <a href="<?php echo real_site_url($cc_plugin_slug) . 'results?lang=' . $lang . '&country_code=ve'?>">
                            <img src="<?php echo CC_PLUGIN_URL; ?>template/images/flags/venezuela.jpg" alt="" class="img-fluid">
                            <h4>Venezuela</h4>
                        </a>
                    </div>

				</div>
			</div>

			<div class="col-md-4 col-lg-3" id="filterRight">
				<div class="boxFilter">
                    <h3><?php _e('VHL Network','cc'); ?></h3>
                    <ul>
                        <?php foreach ( $type_list as $type ) { ?>
                            <li class="cat-item">
                                <?php
                                    $filter_link = 'results?';
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

                    <h3><?php _e('Thematic Networks','cc'); ?></h3>
                    <ul>
                       <?php foreach ( $thematic_list as $thematic) { ?>
                           <?php
                               $filter_link = 'results?';
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

				</div>
			</div>
		</div>
	</section>

<?php include('footer.php'); ?>
