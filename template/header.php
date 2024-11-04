<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('Portal de Revistas Científicas em Ciências da Saúde', 'cc'); ?></title>
    <?php if ($cc_config['google_analytics_code'] != ''): ?>
        <!-- Global site tag (gtag.js) - Google Analytics -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo $cc_config['google_analytics_code'] ?>"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', '<?php echo $cc_config['google_analytics_code'] ?>');
        </script>
    <?php endif; ?>
    <?php wp_head(); ?>
</head>
<body>

<!-- Topo -->
<section id="barAcessibilidade">
    <div class="container">
        <div class="row">
            <div class="col-md-6" id="acessibilidadeTutorial">
                <a href="#main_container" tabindex="1">Conteúdo Principal <span class="hiddenMobile">1</span></a>
                <a href="#nav" tabindex="2">Menu <span class="hiddenMobile">2</span></a>
                <a href="#fieldSearch" tabindex="3" id="accessibilitySearch">Busca <span class="hiddenMobile">3</span></a>
                <a href="#footer" tabindex="4">Rodapé <span class="hiddenMobile">4</span></a>
            </div>
            <div class="col-md-6" id="acessibilidadeFontes">
                <a href="#!" id="fontPlus"  tabindex="5">+A</a> |
                <a href="#!" id="fontNormal"  tabindex="6">A</a> |
                <a href="#!" id="fontLess"  tabindex="7">-A</a> |
                <a href="#!" id="contraste"  tabindex="8"><i class="fas fa-adjust"></i> Alto Contraste</a> <!--|
                <a href="#" id="acessibilidade" class="" tabindex="9" href="docAcessibilidade.php"><i class="fas fa-wheelchair"></i> Accessiblity</a-->
            </div>
        </div>
    </div>
</section>	<!-- Topo -->

<header id="header">
    <div class="container  py-3">
        <div class="row">
            <div class="col-md-12">
        <?php //dynamic_sidebar('cc-header');?>
            </div>
            <div class="col-md-2" id="logo">
                <a href="index.php"><img src="http://logos.bireme.org/img/<?php echo $lang; ?>/bvs_color.svg" alt="" class="img-fluid imgBlack" ></a>
            </div>
            <div class="col-md-10">
                <div id="titleMain" class="float-left">
                    <?php if($cc_plugin_title != ''){?>
                    <div class="titleMain1"><?php _e($cc_plugin_title, 'cc'); ?></div>
                    <?php }else{ ?>
                    <div class="titleMain1"><?php _e('Defina um titulo nas configuracoes do plugin', 'cc'); ?></div>
                    <?php } ?>
                </div>
                <?php if ( $available_languages ) : ?>
                <div class="lang">
                    <ul>
                        <?php
                            for ($count = 0; $count < count($available_languages); $count++) {
                                $for_lang = $available_languages[$count];
                                $for_lang_name = $available_languages_name[$count];
                                $lang_slug = ($for_lang != $default_language ? $for_lang : '');
                                echo '<li>';
                                if ($lang != $for_lang) {
                                    echo '<a href="' . site_url($lang_slug . '/' . $cc_plugin_slug) . '">' . $for_lang_name . '</a></li>';
                                }else{
                                    echo '<a href="" class="active">' . $for_lang_name . '</a></li>';
                                }
                                echo '</li>';
                            }
                         ?>
                    </ul>
                </div>
            <?php endif; ?>
                <div class="clearfix"></div>
               
                <div class="headerSearch" >
                    <form action="<?php echo site_url($lang . '/' . $cc_plugin_slug) . '/?results'?>">
                        <input type="hidden" name="lang" id="lang" value="<?php echo $lang; ?>">
                        <div class="row">
                            <div class="col-md-10 inputBoxSearch">
                                <input type="text" name="q" id="fieldSearch" placeholder="<?php _e('Search', 'cc'); ?>" value="<?php echo $query ?>">
                                <a id="speakBtn" href="#"><i class="fas fa-microphone-alt"></i></a>
                            </div>
                            <div class="col-md-2 btnBoxSearch">
                                <button type="submit">
                                    <i class="fas fa-search"></i>
                                    <span class="textBTSearch"><?php _e('Search', 'cc'); ?></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div id="nav-iahx">
    <div class="container ">
        
<!-- livezilla.net PLACE WHERE YOU WANT TO SHOW GRAPHIC BUTTON -->
<a href="javascript:void(window.open('//contacto.bvsalud.org/chat.php?group=Atendimento&ptl=pt-br&htgs=MQ__&hfk=MQ__','','width=400,height=600,left=0,top=0,resizable=yes,menubar=no,location=no,status=yes,scrollbars=yes'))" class="lz_cbl"><?php _e('Contato', 'cc');?></a>
<!-- livezilla.net PLACE WHERE YOU WANT TO SHOW GRAPHIC BUTTON -->

<!-- livezilla.net PLACE SOMEWHERE IN BODY -->
<!-- PASS THRU DATA OBJECT -->
<script type="text/javascript">
var lz_data = {overwrite:false,language:'pt-br'};
</script>
<!-- PASS THRU DATA OBJECT -->

<div id="lvztr_ee0" style="display:none"></div><script id="lz_r_scr_8d3063714732d133ba17c3d23be22dbf" type="text/javascript" defer>lz_code_id="8d3063714732d133ba17c3d23be22dbf";var script = document.createElement("script");script.async=true;script.type="text/javascript";var src = "//contacto.bvsalud.org/server.php?rqst=track&output=jcrpt&group=Atendimento&htgs=MQ__&hfk=MQ__&nse="+Math.random();script.src=src;document.getElementById('lvztr_ee0').appendChild(script);</script>
<!-- livezilla.net PLACE SOMEWHERE IN BODY -->

<style>
.livezilla {    padding-left: 25% !important;}
.image-ultimate-hover-2 .iheu-info{background: transparent !important;}
@media (max-width: 568px) {
.storytitle, .storycontent{margin-left: 0px !important;}
.top #parent {padding: 12px !important;}
}
</style>
    </div>










                        </div>
</header>
