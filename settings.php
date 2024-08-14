<?php
function cc_page_admin() {
    $config = get_option('cc_config');

?>
    <div class="wrap">
            <div id="icon-options-general" class="icon32"></div>
            <h2><?php _e('Cooperating Centers Settings', 'cc'); ?></h2>

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
                    </tbody>
                </table>
                <p class="submit">
                    <input type="submit" class="button-primary" value="<?php _e('Save changes') ?>" />
                </p>
            </form>
        </div>
<?php
}
?>
