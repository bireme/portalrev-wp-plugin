<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php _e('VHL Network Directory', 'cc'); ?></title>

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
    <div class="container">
        <div class="row">
            <div class="col-md-3" id="logo">
                <a href="index.php"><img src="http://logos.bireme.org/img/<?php echo $lang; ?>/bvs_color.svg" alt="" class="img-fluid imgBlack" ></a>
            </div>
            <div class="col-md-9">
                <div id="titleMain" class="float-left">
                    <div class="titleMain1"><?php _e('VHL Network Directory', 'cc'); ?></div>
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
                    <form action="?results">
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
</header>
