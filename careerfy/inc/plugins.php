<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class careerfy_theme_plugins {

    public function __construct() {
        add_action('admin_menu', [$this, 'admin_menu_pages'], 10);

        add_action('wp_ajax_careerfy_plugin_installation', array($this, 'careerfy_plugin_installation'));
        add_action('wp_ajax_careerfy_plugin_activate', array($this, 'careerfy_plugin_activate'));
        add_action('wp_ajax_careerfy_plugin_update', array($this, 'careerfy_plugin_update'));
        add_action('wp_ajax_careerfy_plugin_deactivate', array($this, 'careerfy_plugin_deactivate'));
        add_action('wp_ajax_careerfy_plugin_uninstall', array($this, 'careerfy_plugin_uninstall'));
        add_action('wp_ajax_careerfy_bulk_plugin_action', array($this, 'careerfy_bulk_plugin_action'));
    }

    public function admin_menu_pages() {
        add_submenu_page('themes.php', esc_html_x('Plugins', 'Admin title', 'careerfy'), esc_html_x('Careerfy Plugins', 'Main menu title', 'careerfy'), 'manage_options', 'careerfy-plugins', [$this, 'plugins_page']);
    }

    public function plugins_page() {

        wp_enqueue_script('careerfy-admin-plugins');
        $plugins_array = self::plugins_list();
        $plugin_updates = get_plugin_updates();
        ?>
        <div class="careerfy-plugins-instalercon">
            <div class="careerfy-header">
                <div class="careerfy-header-content">
                    <div class="careerfy-logo">
                        <h1><?php esc_html_e('Plugin Management', 'careerfy'); ?></h1>
                    </div>
                    <div class="careerfy-header-actions">
                        <button type="button" class="careerfy-btn careerfy-btn-primary careerfy-bulk-install" data-action="install-required">
                            <i class="dashicons dashicons-download"></i>
                            <?php esc_html_e('Install Required Plugins', 'careerfy'); ?>
                        </button>
                        <button type="button" class="careerfy-btn careerfy-btn-secondary careerfy-bulk-update" data-action="update-all">
                            <i class="dashicons dashicons-update"></i>
                            <?php esc_html_e('Update All', 'careerfy'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="careerfy-plugins-dashboard">
                <!-- Plugin Statistics -->
                <div class="careerfy-stats-grid">
                    <?php
                    $stats = self::get_plugin_stats();
                    ?>
                    <div class="careerfy-stat-card">
                        <div class="careerfy-stat-icon">
                            <i class="dashicons dashicons-admin-plugins"></i>
                    </div>
                        <div class="careerfy-stat-content">
                            <h3><?php echo esc_html($stats['total']); ?></h3>
                            <p><?php esc_html_e('Total Plugins', 'careerfy'); ?></p>
                    </div>
                    </div>
                    <div class="careerfy-stat-card">
                        <div class="careerfy-stat-icon">
                            <i class="dashicons dashicons-yes-alt"></i>
                        </div>
                        <div class="careerfy-stat-content">
                            <h3 id="active-plugins-count"><?php echo esc_html($stats['active']); ?></h3>
                            <p><?php esc_html_e('Active Plugins', 'careerfy'); ?></p>
                        </div>
                    </div>
                    <div class="careerfy-stat-card">
                        <div class="careerfy-stat-icon">
                            <i class="dashicons dashicons-update"></i>
                        </div>
                        <div class="careerfy-stat-content">
                            <h3><?php echo esc_html($stats['updates']); ?></h3>
                            <p><?php esc_html_e('Updates Available', 'careerfy'); ?></p>
                        </div>
                    </div>
                    <div class="careerfy-stat-card">
                        <div class="careerfy-stat-icon">
                            <i class="dashicons dashicons-star-filled"></i>
                        </div>
                        <div class="careerfy-stat-content">
                            <h3><?php echo esc_html($stats['required']); ?></h3>
                            <p><?php esc_html_e('Required Plugins', 'careerfy'); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Required Plugins Status -->
                <div class="careerfy-required-status">
                    <?php
                    $required_status = self::get_required_plugins_status();
                    $status_class = '';
                    $status_icon = '';
                    $status_text = '';
                    
                    if ($required_status['active'] === $required_status['total']) {
                        $status_class = 'status-complete';
                        $status_icon = 'dashicons-yes-alt';
                        $status_text = __('All required plugins are active', 'careerfy');
                    } elseif ($required_status['installed'] === $required_status['total']) {
                        $status_class = 'status-partial';
                        $status_icon = 'dashicons-warning';
                        $status_text = __('All required plugins are installed but some are inactive', 'careerfy');
                    } else {
                        $status_class = 'status-incomplete';
                        $status_icon = 'dashicons-dismiss';
                        $status_text = __('Some required plugins are missing', 'careerfy');
                    }
                    ?>
                    <div class="required-status-container <?php echo esc_attr($status_class); ?>">
                        <div class="status-icon">
                            <span class="dashicons <?php echo esc_attr($status_icon); ?>"></span>
                        </div>
                        <div class="status-content">
                            <div class="status-title">
                                <?php esc_html_e('Required Plugins Status', 'careerfy'); ?>
                            </div>
                            <div class="status-description">
                                <?php echo esc_html($status_text); ?>
                            </div>
                            <div class="status-progress">
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo esc_attr(($required_status['active'] / $required_status['total']) * 100); ?>%"></div>
                                </div>
                                <div class="progress-text">
                                    <span class="progress-active"><?php echo esc_html($required_status['active']); ?></span>
                                    <span class="progress-separator">/</span>
                                    <span class="progress-total"><?php echo esc_html($required_status['total']); ?></span>
                                    <span class="progress-label"><?php esc_html_e('active', 'careerfy'); ?></span>
                                </div>
                            </div>
                        </div>
                        <?php if ($required_status['active'] < $required_status['total']) { ?>
                        <div class="status-action">
                            <button type="button" class="button button-primary careerfy-fix-required" data-action="fix-required">
                                <span class="dashicons dashicons-admin-tools"></span>
                                <?php esc_html_e('Fix Now', 'careerfy'); ?>
                            </button>
                        </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Plugin Filter Section -->
                <div class="careerfy-plugins-filter-section">
                    <div class="filter-container">
                        <div class="filter-label">
                            <span class="dashicons dashicons-filter"></span>
                            <span><?php esc_html_e('Filter Plugins:', 'careerfy'); ?></span>
                        </div>
                        <div class="filter-buttons">
                            <button type="button" class="filter-btn active" data-filter="all">
                                <span class="dashicons dashicons-admin-plugins"></span>
                                <?php esc_html_e('All', 'careerfy'); ?>
                                <span class="filter-count" id="count-all">0</span>
                            </button>
                            <button type="button" class="filter-btn" data-filter="required">
                                <span class="dashicons dashicons-star-filled"></span>
                                <?php esc_html_e('Required', 'careerfy'); ?>
                                <span class="filter-count" id="count-required">0</span>
                            </button>
                            <button type="button" class="filter-btn" data-filter="recommended">
                                <span class="dashicons dashicons-star-empty"></span>
                                <?php esc_html_e('Recommended', 'careerfy'); ?>
                                <span class="filter-count" id="count-recommended">0</span>
                            </button>
                            <button type="button" class="filter-btn" data-filter="active">
                                <span class="dashicons dashicons-yes-alt"></span>
                                <?php esc_html_e('Active', 'careerfy'); ?>
                                <span class="filter-count" id="count-active">0</span>
                            </button>
                            <button type="button" class="filter-btn" data-filter="inactive">
                                <span class="dashicons dashicons-minus"></span>
                                <?php esc_html_e('Inactive', 'careerfy'); ?>
                                <span class="filter-count" id="count-inactive">0</span>
                            </button>
                            <button type="button" class="filter-btn" data-filter="updates">
                                <span class="dashicons dashicons-update"></span>
                                <?php esc_html_e('Updates', 'careerfy'); ?>
                                <span class="filter-count" id="count-updates">0</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="admin-careerfy-row">
                    
                    <div class="admin-careerfy-box-wrap admin-careerfy-box-wrap-plugins">
                        
                        <!-- No plugins found message (hidden by default) -->
                        <div id="careerfy-no-plugins-found" class="careerfy-no-plugins-message" style="display: none;">
                            <div class="no-plugins-content">
                                <span class="dashicons dashicons-search"></span>
                                <h3><?php esc_html_e('No plugins found', 'careerfy'); ?></h3>
                                <p><?php esc_html_e('No plugins match the selected filter criteria.', 'careerfy'); ?></p>
                            </div>
                        </div>
                        
                        <?php
                        foreach ( $plugins_array as $plugin ) { 
                            $plugin_info = self::get_plugin_info($plugin, $plugin_updates);
                            ?>

                            <div class="admin-careerfy-box admin-careerfy-box-plugins <?php echo esc_attr($plugin_info['status_class']); ?>" 
                                data-plugin-slug="<?php echo esc_attr($plugin['slug']); ?>"
                                data-plugin-required="<?php echo $plugin['required'] ? 'true' : 'false'; ?>"
                                data-plugin-status="<?php echo esc_attr($plugin_info['status']); ?>"
                                data-plugin-has-update="<?php echo $plugin_info['has_update'] ? 'true' : 'false'; ?>"
                                data-plugin-source="<?php echo isset($plugin['source']) ? esc_attr($plugin['source']) : ''; ?>">
                                <!-- Plugin Icon -->
                                <div class="admin-careerfy-box-image">
                                    <img src="<?php echo esc_url( $plugin['thumbnail'] ); ?>" alt="<?php echo esc_attr( $plugin['name'] ); ?>">
                                    <div class="plugin-status-badge <?php echo esc_attr($plugin_info['status']); ?>">
                                        <?php echo esc_html($plugin_info['status_text']); ?>
                                    </div>
                                </div>

                                <!-- Main Content Area -->
                                <div class="admin-careerfy-box-content">
                                    <div class="plugin-main-content">
                                        <!-- Plugin Header -->
                                        <div class="plugin-header-section">
                                            <h3>
                                                <?php echo esc_html( $plugin['name'] ); ?>
                                                <?php if ($plugin_info['has_update']) { ?>
                                                    <span class="update-badge" title="<?php esc_attr_e('Update Available', 'careerfy'); ?>">
                                                        <span class="dashicons dashicons-update"></span>
                                                    </span>
                                                <?php } ?>
                                            </h3>
                                            
                                            <!-- Author and Version Info -->
                                            <div class="plugin-meta-info">
                                                <div class="author-info">
                                                    <strong><?php esc_html_e('Author:', 'careerfy'); ?></strong>
                                                    <a target="_blank" href="<?php echo esc_url($plugin['author_url']); ?>" rel="noopener">
                                                        <?php echo esc_html($plugin['author']); ?>
                                                    </a>
                                                </div>
                                                
                                                <?php if( !empty($plugin['version']) || !empty($plugin_info['installed_version']) ) { ?>
                                                    <div class="version-info">
                                                        <?php if (!empty($plugin_info['installed_version'])) { ?>
                                                            <strong><?php esc_html_e('Installed:', 'careerfy'); ?></strong>
                                                            <?php echo esc_html($plugin_info['installed_version']); ?>
                                                            <?php if (!empty($plugin['version']) && $plugin_info['has_update']) { ?>
                                                                | <strong><?php esc_html_e('Available:', 'careerfy'); ?></strong>
                                                                <?php echo esc_html($plugin['version']); ?>
                                                            <?php } ?>
                                                        <?php } elseif (!empty($plugin['version'])) { ?>
                                                            <strong><?php esc_html_e('Version:', 'careerfy'); ?></strong>
                                                            <?php echo esc_html($plugin['version']); ?>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            
                                            <!-- Plugin Labels -->
                                            <div class="plugin-labels">
                                                <?php if( $plugin['required'] ) { ?>
                                                <span class="admin-careerfy-required-label"><?php esc_html_e('Required', 'careerfy'); ?></span>
                                                <?php } else { ?>
                                                <span class="admin-careerfy-recommended-label"><?php esc_html_e('Recommended', 'careerfy'); ?></span>
                                                <?php } ?>

                                                <?php if( isset($plugin['wp_org']) && $plugin['wp_org'] ) { ?>
                                                <span class="admin-careerfy-wporg-label"><?php esc_html_e('WordPress.org', 'careerfy'); ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>

                                        <!-- Plugin Info -->
                                        <div class="plugin-info-section">
                                            <div class="plugin-description">
                                                <?php echo esc_html( $plugin['description'] ); ?>
                                            </div>

                                            <?php if ($plugin_info['compatibility_warning']) { ?>
                                            <div class="compatibility-warning">
                                                <span class="dashicons dashicons-warning"></span>
                                                <?php echo esc_html($plugin_info['compatibility_warning']); ?>
                                            </div>
                                            <?php } ?>
                                        </div>

                                    </div>
                                </div>

                                <div class="plugin-actions-section">
                                    <?php
                                    $action_links = self::get_action_links( $plugin, $plugin_info );
                                    if ( $action_links ) {
                                        echo $action_links;
                                    }
                                    ?>
                                </div>
                                
                            </div>
                            <?php
                        }
                        ?>

                    </div>

                </div>
            </div>
        </div>
        <div id="careerfy-plugin-loading" class="careerfy-loading-overlay" style="display: none;">
            <div class="loading-content">
                <div class="loading-header">
                    <div class="header-icon">
                        <span class="dashicons dashicons-admin-plugins"></span>
                    </div>
                    <h3 class="loading-title"><?php esc_html_e('Processing Plugins...', 'careerfy'); ?></h3>
                    <div class="careerfy-spinner"></div>
                </div>
                
                <div class="loading-progress">
                    <div class="progress-info">
                        <span class="progress-count">0/0</span>
                        <span class="progress-percentage">0%</span>
                    </div>
                    <div class="careerfy-progress-bar small">
                        <div class="careerfy-progress-fill default animated" style="width: 0%"></div>
                    </div>
                </div>
                
                <div class="loading-details">
                    <div class="current-action">
                        <span class="action-icon">⏳</span>
                        <span class="action-text"><?php esc_html_e('Preparing...', 'careerfy'); ?></span>
                    </div>
                    <div class="current-plugin">
                        <span class="plugin-name"></span>
                    </div>
                </div>
                
                <div class="loading-log">
                    <div class="log-header">
                        <span class="dashicons dashicons-list-view"></span>
                        <?php esc_html_e('Progress Log', 'careerfy'); ?>
                    </div>
                    <div class="log-content">
                        <!-- Progress messages will be added here -->
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public static function plugins_list() {
        $plugins_array = array(
            array(
                'name'     		=> 'Careerfy Framework',
                'slug'     		=> 'careerfy-framework',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/careerfy-framework.zip',
                'path'   		=> 'careerfy-framework/careerfy-framework.php',
                'required' 		=> true,
                'version' 		=> CAREERFY_VERSION, 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'Theme core plugin for theme functionality.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Careerfy Demo Data',
                'slug'     		=> 'careerfy-demo-data',
                'source'   		=> 'http://careerfy.net/download-plugins/careerfy-demo-data.zip',
                'path'   		=> 'careerfy-demo-data/careerfy-demo-data.php',
                'required' 		=> true,
                'version' 		=> '2.5', 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'Theme plugin for the demo data.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'WP JobSearch',
                'slug'     		=> 'wp-jobsearch',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/wp-jobsearch.zip',
                'path'   		=> 'wp-jobsearch/wp-jobsearch.php',
                'required' 		=> true,
                'version' 		=> WP_JOBSEARCH_VERSION, 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'Theme plugin for job board functionality.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Redux Framework',
                'slug'     		=> 'redux-framework',
                'path'   		=> 'redux-framework/redux-framework.php',
                'required' 		=> true,
                'version' 		=> '', 
                'author' 		=> 'Team Redux',
                'author_url' 	=> 'https://wordpress.org/plugins/redux-framework/',
                'description' 	=> 'Theme Options Framework', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'Envato Market',
                'slug'     		=> 'envato-market',
                'source'        => 'https://envato.github.io/wp-envato-market/dist/envato-market.zip',
                'path'   		=> 'envato-market/envato-market.php',
                'required' 		=> true,
                'version' 		=> '', 
                'author' 		=> 'Envato',
                'author_url' 	=> 'https://envato.com/',
                'description' 	=> '', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Elementor',
                'slug'     		=> 'elementor',
                'path'   		=> 'elementor/elementor.php',
                'required' 		=> false,
                'version' 		=> '', 
                'author' 		=> 'Elementor',
                'author_url' 	=> 'https://elementor.com/?utm_source=wp-plugins&utm_campaign=author-uri&utm_medium=wp-dash',
                'description' 	=> 'Plugin for page builder', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'WPBakery Page Builder',
                'slug'     		=> 'js_composer',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/js_composer.zip',
                'path'   		=> 'js_composer/js_composer.php',
                'required' 		=> true,
                'version' 		=> '8.5', 
                'author' 		=> 'WPBakery',
                'author_url' 	=> 'https://wpbakery.com/?utm_source=wpdashboard&utm_medium=wp-plugins&utm_campaign=info&utm_content=text',
                'description' 	=> 'Plugin for job page builder.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Revolution Slider',
                'slug'     		=> 'revslider',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/revslider.zip',
                'path'   		=> 'revslider/revslider.php',
                'required' 		=> true,
                'version' 		=> '6.7.34', 
                'author' 		=> 'ThemePunch',
                'author_url' 	=> 'https://themepunch.com/?utm_source=admin&utm_medium=button&utm_campaign=srusers&utm_content=info',
                'description' 	=> 'Plugin for job page builder.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'WooCommerce',
                'slug'     		=> 'woocommerce',
                'path'   		=> 'woocommerce/woocommerce.php',
                'required' 		=> true,
                'version' 		=> '', 
                'author' 		=> 'Automattic',
                'author_url' 	=> 'https://woocommerce.com/',
                'description' 	=> 'Plugin for shop and packages.', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'AddToAny Share Buttons',
                'slug'     		=> 'add-to-any',
                'path'   		=> 'add-to-any/add-to-any.php',
                'required' 		=> false,
                'version' 		=> '', 
                'author' 		=> 'AddToAny',
                'author_url' 	=> 'https://www.addtoany.com/',
                'description' 	=> 'Plugin for sharing posts.', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'Addon Jobsearch Scheduled Meetings',
                'slug'     		=> 'addon-jobsearch-scheduled-meetings',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/addon-jobsearch-scheduled-meetings.zip',
                'path'   		=> 'addon-jobsearch-scheduled-meetings/addon-jobsearch-scheduled-meetings.php',
                'required' 		=> false,
                'version' 		=> '2.5', 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'JobSearch addon plugin for meetings.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Addon Jobsearch Resume Export',
                'slug'     		=> 'addon-jobsearch-export-resume',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/addon-jobsearch-export-resume.zip',
                'path'   		=> 'addon-jobsearch-export-resume/addon-jobsearch-export-resume.php',
                'required' 		=> false,
                'version' 		=> '4.7', 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'JobSearch addon plugin for resume builder.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Addon Jobsearch Chat',
                'slug'     		=> 'addon-jobsearch-chat',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/addon-jobsearch-chat.zip',
                'path'   		=> 'addon-jobsearch-chat/addon-jobsearch-chat.php',
                'required' 		=> false,
                'version' 		=> '3.0', 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'JobSearch addon plugin for chat module.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Loco Translate',
                'slug'     		=> 'loco-translate',
                'path'   		=> 'loco-translate/loco.php',
                'required' 		=> false,
                'version' 		=> '', 
                'author' 		=> 'Tim Whitlock',
                'author_url' 	=> 'https://localise.biz/wordpress/plugin',
                'description' 	=> 'Plugin for translation', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'Classic Editor',
                'slug'     		=> 'classic-editor',
                'path'   		=> 'classic-editor/classic-editor.php',
                'required' 		=> true,
                'version' 		=> '', 
                'author' 		=> 'WordPress Contributors',
                'author_url' 	=> 'https://github.com/WordPress/classic-editor/',
                'description' 	=> '', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'Classic Widgets',
                'slug'     		=> 'classic-widgets',
                'path'   		=> 'classic-widgets/classic-widgets.php',
                'required' 		=> true,
                'version' 		=> '', 
                'author' 		=> 'WordPress Contributors',
                'author_url' 	=> 'https://github.com/WordPress/classic-widgets/',
                'description' 	=> '', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'WP All Import',
                'slug'     		=> 'wp-all-import',
                'path'   		=> 'wp-all-import/plugin.php',
                'required' 		=> false,
                'version' 		=> '', 
                'author' 		=> 'Soflyy',
                'author_url' 	=> 'https://profiles.wordpress.org/soflyy/',
                'description' 	=> '', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
            array(
                'name'     		=> 'WP All Import Wp Jobsearch Add-On',
                'slug'     		=> 'wp-all-import-jobsearch',
                'source'   		=> get_template_directory() . '/inc/activation-plugins/wp-all-import-jobsearch.zip',
                'path'   		=> 'wp-all-import-jobsearch/wp-all-import-jobsearch.php',
                'required' 		=> false,
                'version' 		=> '1.9', 
                'author' 		=> 'Eyecix',
                'author_url' 	=> 'http://eyecix.com',
                'description' 	=> 'JobSearch addon plugin for WP All Import.', 
                'thumbnail' 	=> '',
                'wp_org'		=> false,
            ),
            array(
                'name'     		=> 'Login as User',
                'slug'     		=> 'login-as-user',
                'path'   		=> 'login-as-user/login-as-user.php',
                'required' 		=> false,
                'version' 		=> '', 
                'author' 		=> 'Web357',
                'author_url' 	=> 'https://www.web357.com/',
                'description' 	=> '', 
                'thumbnail' 	=> '',
                'wp_org'		=> true,
            ),
        );

        return $plugins_array;
    }

    function get_plugin_stats() {

        $plugins_array = self::plugins_list();
        $stats = array(
            'total' => count($plugins_array),
            'active' => 0,
            'updates' => 0,
            'required' => 0
        );
        
        //
        $plugin_updates = get_plugin_updates();
        $update_plugins = get_site_transient('update_plugins');
        
        foreach ($plugins_array as $plugin) {
            if ($plugin['required']) {
                $stats['required']++;
            }
            
            if (is_plugin_active($plugin['path'])) {
                $stats['active']++;
            }
            
            // Check for WordPress.org updates (multiple sources)
            $has_update = isset($plugin_updates[$plugin['path']]) || (isset($update_plugins->response[$plugin['path']]));
            
            //
            if (!$has_update && file_exists(WP_PLUGIN_DIR . '/' . $plugin['path'])) {

                if (isset($plugin['wp_org']) && $plugin['wp_org']) {
                    $plugin_data = self::get_plugin_data($plugin['path']);
                    if ($plugin_data && !empty($plugin_data['Version'])) {
                        $has_update = self::check_wporg_plugin_version($plugin['slug'], $plugin_data['Version']);
                    }
                }
                // Check for custom plugin updates
                elseif (!empty($plugin['version'])) {
                    $plugin_data = self::get_plugin_data($plugin['path']);
                    if ($plugin_data && !empty($plugin_data['Version'])) {
                        $has_update = version_compare($plugin_data['Version'], $plugin['version'], '<');
                    }
                }
            }
            
            if ($has_update) {
                $stats['updates']++;
            }
        }
        
        return $stats;
    }

    public static function get_plugin_data($plugin_path) {
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_path)) {
			return get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin_path);
		}
    }

    public static function check_wporg_plugin_version($plugin_slug, $installed_version) {
        if (empty($plugin_slug) || empty($installed_version)) {
            return false;
        }
        
        // Use WordPress.org API to get latest version with shorter timeout
        $api_url = "https://api.wordpress.org/plugins/info/1.0/{$plugin_slug}.json";
        $response = wp_remote_get($api_url, array(
            'timeout' => 3, // Reduced from 10 to 3 seconds
            'user-agent' => 'WordPress/' . get_bloginfo('version') . '; ' . home_url()
        ));

        if (is_wp_error($response)) {
            return false;
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        if ($response_code !== 200) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $plugin_info = json_decode($body, true);
        
        if (isset($plugin_info['version'])) {
            return version_compare($installed_version, $plugin_info['version'], '<');
        }
        
        return false;
    }

    public static function get_required_plugins_status() {

        $plugins_array = self::plugins_list();
        $status = array(
            'total' => 0,
            'installed' => 0,
            'active' => 0
        );
        
        foreach ($plugins_array as $plugin) {
            if ($plugin['required']) {
                $status['total']++;
                
                // Check if plugin is installed
                if (file_exists(WP_PLUGIN_DIR . '/' . $plugin['path'])) {
                    $status['installed']++;
                    
                    // Check if plugin is active
                    if (is_plugin_active($plugin['path'])) {
                        $status['active']++;
                    }
                }
            }
        }
        
        return $status;
    }

    public static function get_plugin_info($plugin, $plugin_updates) {
        $info = array(
            'status' => 'not-installed',
            'status_text' => __('Not Installed', 'careerfy'),
            'status_class' => 'status-not-installed',
            'has_update' => false,
            'installed_version' => '',
            'compatibility_warning' => ''
        );
        
        $plugin_file = $plugin['path'];
        
        $update_plugins = get_site_transient( 'update_plugins' );
        
        // Check if plugin is installed
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) {
            if (is_plugin_active($plugin_file)) {
                $info['status'] = 'active';
                $info['status_text'] = __('Active', 'careerfy');
                $info['status_class'] = 'status-active';
            } else {
                $info['status'] = 'inactive';
                $info['status_text'] = __('Inactive', 'careerfy');
                $info['status_class'] = 'status-inactive';
            }
            
            // Get installed version using cached data
            $plugin_data = self::get_plugin_data($plugin_file);
            $info['installed_version'] = $plugin_data ? $plugin_data['Version'] : '';
            
            // Check for updates - WordPress.org plugins (multiple sources)
            if (isset($plugin_updates[$plugin_file]) || (isset($update_plugins->response[$plugin_file]))) {
                $info['has_update'] = true;
                $info['status'] = 'update-available';
                $info['status_text'] = __('Update Available', 'careerfy');
                $info['status_class'] = 'status-update-available';
            }
            // Fallback check for WordPress.org plugins using API (only on plugins page)
            elseif (isset($plugin['wp_org']) && $plugin['wp_org'] && !empty($info['installed_version'])) {
                
                $has_api_update = self::check_wporg_plugin_version($plugin['slug'], $info['installed_version']);
                
                if ($has_api_update) {
                    $info['has_update'] = true;
                    $info['status'] = 'update-available';
                    $info['status_text'] = __('Update Available', 'careerfy');
                    $info['status_class'] = 'status-update-available';
                }
            }
            // Check for updates - Custom plugins (compare with our plugins array version)
            elseif (!empty($plugin['version']) && !empty($info['installed_version'])) {
                if (version_compare($info['installed_version'], $plugin['version'], '<')) {
                    $info['has_update'] = true;
                    $info['status'] = 'update-available';
                    $info['status_text'] = __('Update Available', 'careerfy');
                    $info['status_class'] = 'status-update-available';
                }
            }
            
            // Check compatibility
            if ($plugin_data && !empty($plugin_data['RequiresWP'])) {
                global $wp_version;
                if (version_compare($wp_version, $plugin_data['RequiresWP'], '<')) {
                    $info['compatibility_warning'] = sprintf(
                        __('This plugin requires WordPress %s or higher.', 'careerfy'),
                        $plugin_data['RequiresWP']
                    );
                }
            }
        }
        
        return $info;
    }

    public function get_action_links( $plugin, $plugin_info = null ) {
        if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'update_plugins' ) && ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'delete_plugins' ) ) {
            return '';
        }

        if ( ! $plugin_info ) {
            $plugin_updates = get_plugin_updates();
            $plugin_info = self::get_plugin_info( $plugin, $plugin_updates );
        }

        $button = '';
        $plugin_name = $plugin['name']; 
        $plugin_file = $plugin['path'];
        $plugin_slug = $plugin['slug'];
        $plugin_source = isset($plugin['source']) ? $plugin['source'] : '';
        $is_required = $plugin['required'];

        // Check if plugin is installed
        $is_installed = file_exists( WP_PLUGIN_DIR . '/' . $plugin_file );
        $is_active = is_plugin_active( $plugin_file );
        
        // Check for updates (both WordPress.org and custom plugins)
        $plugin_updates_list = get_plugin_updates();
        $update_plugins = get_site_transient( 'update_plugins' );
        
        $has_update = isset( $plugin_updates_list[ $plugin_file ] ) || (isset($update_plugins->response[$plugin_file]));
        
        // Fallback check for WordPress.org plugins using API (use cached data when possible)
        if ( ! $has_update && $is_installed && isset($plugin['wp_org']) && $plugin['wp_org'] ) {
            $plugin_data = self::get_plugin_data( $plugin_file );
            if ( $plugin_data && ! empty( $plugin_data['Version'] ) ) {
                $has_update = self::check_wporg_plugin_version($plugin['slug'], $plugin_data['Version']);
            }
        }
        
        // For custom plugins, compare installed version with required version
        if ( ! $has_update && $is_installed && ! empty( $plugin['version'] ) ) {
            $plugin_data = self::get_plugin_data( $plugin_file );
            if ( $plugin_data && ! empty( $plugin_data['Version'] ) && version_compare( $plugin_data['Version'], $plugin['version'], '<' ) ) {
                $has_update = true;
            }
        }

        if ( ! $is_installed ) {
            // 1. Not installed - Show only Install button
            $install_text = esc_attr__( 'Install Now', 'careerfy' );
            if ( ! empty( $plugin_source ) ) {
                $button = sprintf(
                    '<a class="careerfy-plugin-js careerfy-install-btn button button-primary" data-name="%s" data-slug="%s" data-source="%s" data-file="%s" href="#">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_url( $plugin_source ),
                    esc_attr( $plugin_file ),
                    $install_text
                );
            } else {
                $button = sprintf(
                    '<a class="careerfy-plugin-js careerfy-install-btn button button-primary" href="#" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_attr( $plugin_file ),
                    $install_text
                );
            }
        } elseif ( ! $is_active ) {
            // 2. Installed but not active - Show Activate, Update (if available), and Uninstall (if not required)
            
            // Activate button
            if ( current_user_can( 'activate_plugin', $plugin_file ) ) {
                $button = sprintf( '<a href="#" class="careerfy-plugin-js careerfy-activate-btn button button-primary" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_attr( $plugin_file ),
                    esc_attr__( 'Activate', 'careerfy' )
                );
            }

            // Update button (if update available)
            if ( $has_update && current_user_can( 'update_plugins' ) ) {
                $button .= sprintf( ' <a href="#" class="careerfy-plugin-js careerfy-update-btn button" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_attr( $plugin_file ),
                    esc_attr__( 'Update Now', 'careerfy' )
                );
            }

            // Uninstall button (only for non-required plugins)
            if ( ! $is_required && current_user_can( 'delete_plugins' ) ) {
                $button .= sprintf( ' <a href="#" class="careerfy-plugin-js careerfy-uninstall-btn button button-link-delete" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_attr( $plugin_file ),
                    esc_attr__( 'Uninstall', 'careerfy' )
                );
            }
        } else {
            // 3. Active - Show Deactivate and Update (if available). NO Uninstall for active plugins
            
            // Active status indicator
            $button = sprintf('<span class="button button-disabled">%s</span>',
                esc_attr__( 'Active', 'careerfy' )
            );

            // Deactivate button
            if ( current_user_can( 'deactivate_plugin', $plugin_file ) ) {
                $button .= sprintf( ' <a href="#" class="careerfy-plugin-js careerfy-deactivate-btn button" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_attr( $plugin_file ),
                    esc_attr__( 'Deactivate', 'careerfy' )
                );
            }

            // Update button (if update available)
            if ( $has_update && current_user_can( 'update_plugins' ) ) {
                $button .= sprintf( ' <a href="#" class="careerfy-plugin-js careerfy-update-btn button" data-name="%s" data-slug="%s" data-file="%s">%s</a>',
                    esc_attr( $plugin_name ),
                    esc_attr( $plugin_slug ),
                    esc_attr( $plugin_file ),
                    esc_attr__( 'Update Now', 'careerfy' )
                );
            }
        }

        return $button;
    }

	public function careerfy_plugin_installation() {
		check_ajax_referer( 'careerfy-admin-nonce' );

		$status = array();
		$download_link = null;
		$plugin_source = isset( $_POST['plugin_source'] ) ? $_POST['plugin_source'] : '';
		$plugin_slug = isset( $_POST['plugin_slug'] ) ? $_POST['plugin_slug'] : '';

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		// Check if current user have permission to install plugin or not
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error();
		}

		if( empty($plugin_slug) ) {
			wp_send_json_error();
		}

		// Retrieves plugin installer pages from the WordPress.org Plugins API.
		$plugin_api = plugins_api(
			'plugin_information',
			array(
				'slug' => sanitize_key( wp_unslash( $plugin_slug ) ),
			)
		);
		
		if ( ! empty( $plugin_source ) ) {

			$download_link = esc_url( $plugin_source );

		} else {
			if ( is_wp_error( $plugin_api ) ) {
				wp_send_json_error();
			}
			$download_link = $plugin_api->download_link;
		}

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$response = $upgrader->install( $download_link );

		if ( is_wp_error( $response ) ) {
			$status['errorCode']    = $response->get_error_code();
			$status['errorMessage'] = $response->get_error_message();
			wp_send_json_error( $status );
		} else {
			wp_send_json_success();
		}
		
		
	}

	public function careerfy_plugin_activate() {
	    check_ajax_referer( 'careerfy-admin-nonce' );

	    $error = array();
	    $plugin_file = isset( $_POST['plugin_file'] ) ? $_POST['plugin_file'] : '';

	    if( empty($plugin_file) ) {
	    	wp_send_json_error();
	    }

	    // Check if current user has permission to activate plugins
	    if ( ! current_user_can( 'activate_plugins' ) ) {
	    	$error['errorMessage'] = __( 'You do not have permission to activate plugins.', 'careerfy' );
	    	wp_send_json_error( $error );
	    }

		$response  = activate_plugin( $plugin_file );
		if ( is_wp_error( $response ) ) {
			$error['errorMessage'] = $response->get_error_message();
			wp_send_json_error( $error );
		} else {
			wp_send_json_success();
		}
	}

	public function careerfy_plugin_update() {
		check_ajax_referer( 'careerfy-admin-nonce' );

		$error = array();
		$plugin_file = isset( $_POST['plugin_file'] ) ? sanitize_text_field( $_POST['plugin_file'] ) : '';
		$plugin_name = isset( $_POST['plugin_name'] ) ? sanitize_text_field( $_POST['plugin_name'] ) : '';

		if( empty($plugin_file) ) {
			wp_send_json_error();
		}

		// Check if current user have permission to update plugin or not
		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error();
		}

		// Get plugin data from our plugins array to check if it's a custom plugin
		$plugins_array = self::plugins_list();
		$plugin_data = null;
		
		foreach ( $plugins_array as $plugin ) {
			if ( $plugin['path'] === $plugin_file ) {
				$plugin_data = $plugin;
				break;
			}
		}

		// If plugin data found and it has a custom source, use custom update process
		if ( $plugin_data !== null && is_array( $plugin_data ) && isset( $plugin_data['source'] ) && ! empty( $plugin_data['source'] ) ) {
			$result = self::update_custom_plugin( $plugin_file, $plugin_data['source'], $plugin_data['name'] );
			if ( $result['success'] ) {
				wp_send_json_success();
			} else {
				$error['errorMessage'] = $result['message'];
				wp_send_json_error( $error );
			}
		} else {
			// Use WordPress built-in update for WordPress.org plugins
			include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

			$skin = new WP_Ajax_Upgrader_Skin();
			$upgrader = new Plugin_Upgrader( $skin );
			$response = $upgrader->upgrade( $plugin_file );

			if ( is_wp_error( $response ) ) {
				$error['errorCode'] = $response->get_error_code();
				$error['errorMessage'] = $response->get_error_message();
				wp_send_json_error( $error );
			} else {
				wp_send_json_success();
			}
		}
	}

	public function careerfy_plugin_deactivate() {
		check_ajax_referer( 'careerfy-admin-nonce' );

		$error = array();
		$plugin_file = isset( $_POST['plugin_file'] ) ? sanitize_text_field( $_POST['plugin_file'] ) : '';
		$plugin_name = isset( $_POST['plugin_name'] ) ? sanitize_text_field( $_POST['plugin_name'] ) : '';

		if( empty($plugin_file) ) {
			wp_send_json_error();
		}

		// Check if current user have permission to deactivate plugin or not
		if ( ! current_user_can( 'deactivate_plugin', $plugin_file ) ) {
			wp_send_json_error();
		}

		$response = deactivate_plugins( $plugin_file );
		// deactivate_plugins() doesn't return WP_Error, it returns null on success
		// We'll assume success if no fatal error occurred
		wp_send_json_success();
	}

	public function careerfy_plugin_uninstall() {
		check_ajax_referer( 'careerfy-admin-nonce' );

		$error = array();
		$plugin_file = isset( $_POST['plugin_file'] ) ? sanitize_text_field( $_POST['plugin_file'] ) : '';
		$plugin_name = isset( $_POST['plugin_name'] ) ? sanitize_text_field( $_POST['plugin_name'] ) : '';

		if( empty($plugin_file) ) {
			wp_send_json_error();
		}

		// Check if current user have permission to delete plugins or not
		if ( ! current_user_can( 'delete_plugins' ) ) {
			wp_send_json_error();
		}

		// Check if plugin is required - don't allow uninstalling required plugins
		$plugins_array = self::plugins_list();
		$is_required = false;
		foreach ( $plugins_array as $plugin ) {
			if ( $plugin['path'] === $plugin_file && $plugin['required'] ) {
				$is_required = true;
				break;
			}
		}

		if ( $is_required ) {
			$error['errorMessage'] = __( 'Cannot uninstall required plugin.', 'careerfy' );
			wp_send_json_error( $error );
		}

		// First deactivate the plugin if it's active
		if ( is_plugin_active( $plugin_file ) ) {
			deactivate_plugins( $plugin_file );
		}

		// Include necessary files for plugin deletion
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Delete the plugin
		$response = delete_plugins( array( $plugin_file ) );
		
		if ( is_wp_error( $response ) ) {
			$error['errorCode'] = $response->get_error_code();
			$error['errorMessage'] = $response->get_error_message();
			wp_send_json_error( $error );
		} else {
			wp_send_json_success();
		}
	}

	public function careerfy_bulk_plugin_action() {
		check_ajax_referer( 'careerfy-admin-nonce' );

		$bulk_action = isset( $_POST['bulk_action'] ) ? sanitize_text_field( $_POST['bulk_action'] ) : '';
		$plugins = isset( $_POST['plugins'] ) ? array_map( 'sanitize_text_field', $_POST['plugins'] ) : array();

		if ( empty( $plugins ) || empty( $bulk_action ) ) {
			wp_send_json_error( __( 'No plugins selected or invalid action.', 'careerfy' ) );
		}

		// Check user capabilities
		if ( ! current_user_can( 'install_plugins' ) && ! current_user_can( 'activate_plugins' ) && ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error( __( 'You do not have permission to manage plugins.', 'careerfy' ) );
		}

		// Clear plugin cache to get fresh status
		if ( ! function_exists( 'wp_clean_plugins_cache' ) ) {
			require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}
		wp_clean_plugins_cache();
        wp_update_plugins();

        require_once( ABSPATH . 'wp-admin/includes/update.php' );

		$results = array();
		$errors = array();

		// Get plugin array for reference
		$plugins_array = self::plugins_list();

		foreach ( $plugins as $plugin_slug ) {
			$plugin_data = null;
			foreach ( $plugins_array as $plugin ) {
				if ( isset( $plugin['slug'] ) && $plugin['slug'] === $plugin_slug ) {
					$plugin_data = $plugin;
					break;
				}
			}

			if ( ! $plugin_data ) {
				$errors[] = sprintf( __( 'Plugin %s not found in configuration.', 'careerfy' ), $plugin_slug );
				continue;
			}

			// Ensure we have required plugin data
			if ( ! isset( $plugin_data['path'] ) || ! isset( $plugin_data['name'] ) ) {
				$errors[] = sprintf( __( 'Invalid plugin configuration for %s.', 'careerfy' ), $plugin_slug );
				continue;
			}

			switch ( $bulk_action ) {
				case 'install-required':
					$plugin_path_full = WP_PLUGIN_DIR . '/' . $plugin_data['path'];
					$file_exists = file_exists( $plugin_path_full );
					
					// More reliable active check: if file doesn't exist, it can't be active
					$is_active = $file_exists && is_plugin_active( $plugin_data['path'] );
					
					if ( $plugin_data['required'] ) {
						if ( $is_active ) {
							// Plugin is already active - skip it
							$results[] = sprintf( __( '%s is already active - skipped.', 'careerfy' ), $plugin_data['name'] );
						} elseif ( ! $file_exists ) {
							// Plugin not installed - install it
							$plugin_source = isset( $plugin_data['source'] ) ? $plugin_data['source'] : '';
							
							$result = self::install_single_plugin( $plugin_data['slug'], $plugin_source, $plugin_data['name'] );
							if ( ! $result['success'] ) {
								$errors[] = $result['message'];
							} else {
								$results[] = $result['message'];
								
								// Auto-activate after successful installation
								if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_data['path'] ) ) {
									$activate_result = activate_plugin( $plugin_data['path'] );
									if ( is_wp_error( $activate_result ) ) {
										$errors[] = sprintf( __( 'Installed %s but failed to activate: %s', 'careerfy' ), $plugin_data['name'], $activate_result->get_error_message() );
									} else {
										$results[] = sprintf( __( '%s activated successfully.', 'careerfy' ), $plugin_data['name'] );
									}
								} else {
									$errors[] = sprintf( __( 'Plugin %s was installed but file still not found at: %s', 'careerfy' ), $plugin_data['name'], WP_PLUGIN_DIR . '/' . $plugin_data['path'] );
								}
							}
						} else {
							// Plugin is installed but not active - activate it
							$activate_result = activate_plugin( $plugin_data['path'] );
							if ( is_wp_error( $activate_result ) ) {
								$errors[] = sprintf( __( 'Failed to activate %s: %s', 'careerfy' ), $plugin_data['name'], $activate_result->get_error_message() );
							} else {
								$results[] = sprintf( __( '%s activated successfully.', 'careerfy' ), $plugin_data['name'] );
							}
						}
					}
					break;
				case 'activate-required':
					if ( $plugin_data['required'] && file_exists( WP_PLUGIN_DIR . '/' . $plugin_data['path'] ) ) {
						if ( is_plugin_active( $plugin_data['path'] ) ) {
							// Plugin is already active - skip it
							$results[] = sprintf( __( '%s is already active - skipped.', 'careerfy' ), $plugin_data['name'] );
						} else {
							// Plugin is installed but not active - activate it
							$activate_result = activate_plugin( $plugin_data['path'] );
							if ( is_wp_error( $activate_result ) ) {
								$errors[] = sprintf( __( 'Failed to activate %s: %s', 'careerfy' ), $plugin_data['name'], $activate_result->get_error_message() );
							} else {
								$results[] = sprintf( __( '%s activated successfully.', 'careerfy' ), $plugin_data['name'] );
							}
						}
					}
					break;
				case 'update-all':
					if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_data['path'] ) ) {
						$needs_update = false;
						
						// Check if it's a custom plugin with source URL
						if ( isset( $plugin_data['source'] ) && ! empty( $plugin_data['source'] ) ) {
							// For custom plugins, check version comparison
							$plugin_file_data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin_data['path'] );
							$installed_version = $plugin_file_data['Version'];
							
							if ( ! empty( $plugin_data['version'] ) && ! empty( $installed_version ) && 
								 version_compare( $installed_version, $plugin_data['version'], '<' ) ) {
								$needs_update = true;
								$result = self::update_custom_plugin( $plugin_data['path'], $plugin_data['source'], $plugin_data['name'] );
								if ( ! $result['success'] ) {
									$errors[] = $result['message'];
								} else {
									$results[] = $result['message'];
								}
							}
						} else {
							// For WordPress.org plugins, use built-in update mechanism
							$plugin_updates = get_plugin_updates();
							if ( isset( $plugin_updates[ $plugin_data['path'] ] ) ) {
								$needs_update = true;
								$result = self::update_single_plugin( $plugin_data['path'], $plugin_data['name'] );
								if ( ! $result['success'] ) {
									$errors[] = $result['message'];
								} else {
									$results[] = $result['message'];
								}
							}
						}
						
						// If no update was needed, log it
						if ( ! $needs_update ) {
							$results[] = sprintf( __( '%s is already up to date - skipped.', 'careerfy' ), $plugin_data['name'] );
						}
					}
					break;
			}
		}

		if ( ! empty( $errors ) ) {
			wp_send_json_error( implode( '<br>', $errors ) );
		} elseif ( ! empty( $results ) ) {
			wp_send_json_success( implode( '<br>', $results ) );
		} else {
			// No actions were performed - this might indicate an issue
			wp_send_json_error( __( 'No actions were performed. Please check if the plugins are already active or if there are permission issues.', 'careerfy' ) );
		}
	}

    private static function install_single_plugin( $plugin_slug, $plugin_source = '', $plugin_name = '' ) {
		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		// Determine download URL
		if ( ! empty( $plugin_source ) ) {
			$download_url = $plugin_source;
		} else {
			// Get from WordPress.org
			$api = plugins_api( 'plugin_information', array(
				'slug' => $plugin_slug,
				'fields' => array( 'download_link' => true )
			) );

			if ( is_wp_error( $api ) ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Failed to get plugin information for %s: %s', 'careerfy' ), $plugin_name, $api->get_error_message() )
				);
			}

			$download_url = $api->download_link;
		}

		// Install the plugin
		$result = $upgrader->install( $download_url );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to install %s: %s', 'careerfy' ), $plugin_name, $result->get_error_message() )
			);
		}

		if ( $result === false ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to install %s. Please try again.', 'careerfy' ), $plugin_name )
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( '%s installed successfully.', 'careerfy' ), $plugin_name )
		);
	}

	private static function update_single_plugin( $plugin_file, $plugin_name = '' ) {
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );

		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		$result = $upgrader->upgrade( $plugin_file );

		if ( is_wp_error( $result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to update %s: %s', 'careerfy' ), $plugin_name, $result->get_error_message() )
			);
		}

		if ( $result === false ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to update %s. Please try again.', 'careerfy' ), $plugin_name )
			);
		}

		return array(
			'success' => true,
			'message' => sprintf( __( '%s updated successfully.', 'careerfy' ), $plugin_name )
		);
	}

	private static function update_custom_plugin( $plugin_file, $plugin_source, $plugin_name = '' ) {
		// Check if plugin is currently active
		$was_active = is_plugin_active( $plugin_file );

		// Include necessary files
		include_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		include_once( ABSPATH . 'wp-admin/includes/file.php' );
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

		// Create upgrader instance
		$skin = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );

		// Download the plugin zip file
		$download_result = download_url( $plugin_source );
		
		if ( is_wp_error( $download_result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to download %s: %s', 'careerfy' ), $plugin_name, $download_result->get_error_message() )
			);
		}

		// Get plugin directory name from plugin file path
		$plugin_dir = dirname( $plugin_file );
		$plugin_path = WP_PLUGIN_DIR . '/' . $plugin_dir;

		// Deactivate plugin before updating
		if ( $was_active ) {
			deactivate_plugins( $plugin_file );
		}

		// Remove old plugin directory
		if ( file_exists( $plugin_path ) ) {
			global $wp_filesystem;
			
			// Initialize WP_Filesystem
			if ( ! function_exists( 'WP_Filesystem' ) ) {
				require_once( ABSPATH . 'wp-admin/includes/file.php' );
			}
			
			$filesystem_initialized = WP_Filesystem();
			
			if ( $filesystem_initialized && $wp_filesystem ) {
				$wp_filesystem->delete( $plugin_path, true );
			} else {
				// Fallback to PHP functions if WP_Filesystem fails
				self::recursive_delete( $plugin_path );
			}
		}

		// Extract the new plugin
		$unzip_result = unzip_file( $download_result, WP_PLUGIN_DIR );
		
		// Clean up downloaded file
		unlink( $download_result );

		if ( is_wp_error( $unzip_result ) ) {
			return array(
				'success' => false,
				'message' => sprintf( __( 'Failed to extract %s: %s', 'careerfy' ), $plugin_name, $unzip_result->get_error_message() )
			);
		}

		// Reactivate plugin if it was active before
		if ( $was_active && file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
			$activate_result = activate_plugin( $plugin_file );
			if ( is_wp_error( $activate_result ) ) {
				return array(
					'success' => false,
					'message' => sprintf( __( 'Updated %s but failed to reactivate: %s', 'careerfy' ), $plugin_name, $activate_result->get_error_message() )
				);
			}
		}

		return array(
			'success' => true,
			'message' => sprintf( __( '%s updated successfully.', 'careerfy' ), $plugin_name )
		);
	}

	private static function recursive_delete( $dir ) {
		if ( ! is_dir( $dir ) ) {
			return;
		}

		$files = array_diff( scandir( $dir ), array( '.', '..' ) );
		foreach ( $files as $file ) {
			$path = $dir . '/' . $file;
			if ( is_dir( $path ) ) {
				self::recursive_delete( $path );
			} else {
				unlink( $path );
			}
		}
		rmdir( $dir );
	}
}

new careerfy_theme_plugins;