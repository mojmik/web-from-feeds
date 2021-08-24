<?php
   /*
   Plugin Name: CJ feed content generator
   Plugin URI: https://www.ttj.cz
   description: Adds custom post option, adds custom fields to administration interface v2
  mAuta plugin
   Version: 3.1a
   Author: Mik
   Author URI: https://www.ttj.cz
   License: GPL2
   */
   
// mhtz




define('CAF_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define('CAF_PLUGIN_FILE_URL', __FILE__);
define('CAF_SHORT_TITLE', 'CAF' );
define('CAF_TAB_PREFIX','mauta_');
define('CAF_ALLOW_ATTACHMENTS',false);
define('CAF_ALLOW_MIMG',false);
define('CAF_LOAD_EXTERNAL_UI',false);
define('CAF_FORCE_CJ',true);
define('CAF_TEXTDOMAIN',"wpcustomajaxfilters");


require_once CAF_PLUGIN_PATH . '/customajaxfilters/loader.php';
$loader=new CustomAjaxFilters\Loader();
//$loader->initAdmin();

if (is_admin() || wp_is_json_request()) $loader->initAdmin();
else $loader->initFrontend();

