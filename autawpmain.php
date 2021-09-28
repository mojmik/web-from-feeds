<?php
   /*
   Plugin Name: Product feeds easy affil import
   Plugin URI: https://www.cyltr.com/cj-feed-content-generator/
   Description: Create whole affiliate website simply by importing product feeds. Easy to customize, templatable and blazing fast.
   Version: 3.1
   Author: Mik
   Author URI: https://www.ttj.cz
   License: GPL2
   */
 


define('PFEA_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('PFEA_PLUGIN_FILE_URL', __FILE__);
define('PFEA_SHORT_TITLE', 'CAF' );
define('PFEA_TAB_PREFIX','mauta_');
define('PFEA_LOAD_EXTERNAL_UI',false);
define('PFEA_FORCE_CJ',true);
define('PFEA_TEXTDOMAIN',"wpcustomajaxfilters");


require_once PFEA_PLUGIN_PATH . '/customajaxfilters/loader.php';
$loader=new CustomAjaxFilters\Loader();

if (is_admin() || wp_is_json_request()) $loader->initAdmin();