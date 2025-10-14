<?php
function cc_page_admin() {

    global $biblio_texts;
    $config = get_option('cc_config');

    $default_filter_list = array('status' => __('Status','cc') ,
                                'descriptor_filter' => __('Assunto','cc') ,
                                'indexed_database' =>  __('Base de dados','cc'),
                                'language' =>  __('Idioma','cc'),
                                'country' =>  __('Country', 'cc'),
                                'thematic_area_display' =>  __('Area temática','cc')
    );

    $available_filter_list   = $default_filter_list;

    if ($biblio_texts['filter']){
        $available_filter_list = array_merge($biblio_texts['filter'], $default_filter_list);
    }else{
        $available_filter_list   = $default_filter_list;
        $biblio_texts['filter'] = $default_filter_list;
    }

    $config_filter_list = array();
    if ( $config['available_filter'] ){
        $config_filter_list = explode(';', $config['available_filter']);
    }
?>
    <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('Journals Settings', 'cc'); ?></h2>

            <form method="post" action="options.php">

                <?php settings_fields('cc-settings-group'); ?>

                <table class="form-table">
                    <tbody>
                        <tr valign="top">
                            <th scope="row"><?php _e('Plugin page', 'cc'); ?>:</th>
                            <td><input type="text" name="cc_config[plugin_slug]" value="<?php echo ($config['plugin_slug'] != '' ? $config['plugin_slug'] : 'centers'); ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Google Analytics code', 'cc'); ?>:</th>
                            <td><input type="text" name="cc_config[google_analytics_code]" value="<?php echo $config['google_analytics_code'] ?>" class="regular-text code"></td>
                        </tr>
                        <tr valign="top">
                            <th scope="row"><?php _e('Initial filter query', 'cc'); ?>:</th>
                            <td><input type="text" name="cc_config[initial_filter]" value='<?php echo $config['initial_filter'] ?>' class="regular-text code"></td>
                        </tr>
                        <?php
                        if ( function_exists( 'pll_the_languages' ) ) {
                            $available_languages = pll_languages_list();
                            $available_languages_name = pll_languages_list(array('fields' => 'name'));
                            $count = 0;
                            foreach ($available_languages as $lang) {
                                $plugin_title = 'plugin_title_' . $lang;
                                $home_url = 'home_url_' . $lang;

                                echo '<tr valign="top">';
                                echo '   <th scope="row">' . __("Page title", "cc") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '   <td><input type="text" name="cc_config[' . $plugin_title . ']" value="' . $config[$plugin_title] . '" class="regular-text code"></td>';
                                echo '</tr>';

                                echo '<tr valign="top">';
                                echo '    <th scope="row"> ' . __("Home URL", "cc") . ' (' . $available_languages_name[$count] . '):</th>';
                                echo '    <td><input type="text" name="cc_config[' . $home_url . ']" value="' . $config[$home_url] . '" class="regular-text code"></td>';
                                echo '</tr>';

                                $count++;
                            }
                        }else{
                            echo '<tr valign="top">';
                            echo '   <th scope="row">' . __("Page title", "cc") . ':</th>';
                            echo '   <td><input type="text" name="cc_config[plugin_title]" value="' . $config["plugin_title"] . '" class="regular-text code"></td>';
                            echo '</tr>';

                            echo '<tr valign="top">';
                            echo '    <th scope="row"> ' . __("Home URL", "cc") . ':</th>';
                            echo '    <td><input type="text" name="cc_config[home_url]" value="' . $config['home_url'] . '" class="regular-text code"></td>';
                            echo '</tr>';
                        }
                        ?>
                        <!---------------------------->
                        <tr valign="top"><th scope="row"><?php _e('Search filters', 'biblio');?>:</th><td>
                            <table border=0>
                                <tr><td>
                                    <p align="left"><?php _e('Available', 'biblio');?><br>
                                        <ul id="sortable1" class="connectedSortable">
                                        <?php 
                                            foreach ($available_filter_list as $filter_field => $filter_title){
                                                if ( !in_array($filter_field, $config_filter_list) ) {
                                                    echo '<li class="ui-state-default" id="' .  $filter_field .'">' . $filter_title . '</li>';
                                                }
                                            }?>
                                            </ul>
                                        </p>
                                    </td><td>
                                        <p align="left"><?php _e('Selected', 'biblio');?> <br>
                                          <ul id="sortable2" class="connectedSortable">
                                              <?php
                                                foreach ($config_filter_list as $selected_filter) {
                                                    $filter_title = $biblio_texts['filter'][$selected_filter];
                                                    if ($filter_title != ''){
                                                        echo '<li class="ui-state-default" id="' . $selected_filter . '">' . $filter_title . '</li>';
                                                    }
                                                }
                                              ?>
                                          </ul>
                                          <input type="hidden" id="available_filter_aux" name="cc_config[available_filter]" value="<?php echo $config['available_filter']; ?>" >
                                        </p>
                                </td></tr>
                            </table></td>
                        </tr>
                    <!------------------------>
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
                </p>
            </form>
        </div>
        <script type="text/javascript">
        var $j = jQuery.noConflict();

        $j( function() {
            $j("#sortable1, #sortable2").sortable({
                connectWith: ".connectedSortable"
            });

            $j("#sortable2").sortable({
                update: function(event, ui) {
                    var changedList = this.id;
                    var selected_filter = $j(this).sortable('toArray');
                    var selected_filter_list = selected_filter.join(';');
                    $j('#available_filter_aux').val(selected_filter_list);
                }
            });

        } );
    </script>
<?php
}
?>
