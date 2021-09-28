<?php
namespace CustomAjaxFilters;

class Loader {	

    public function __construct() {	        
        spl_autoload_register([$this,"mLoadClass"]);        
        define('CAF_MAJAX_PATH',plugin_dir_path( __FILE__ ). "majax/");  
        //translations
        add_action('init', [$this,"globalInit"]);

        $baseName=plugin_basename(__FILE__);
        $currentScript=basename(__FILE__);
        define('CAF_RELPATH_MAIN',str_replace($currentScript,"",$baseName));        

        $cjActive=true;
		if ($cjActive)	{


            $cj=new Admin\ComissionJunction();
            $page=["link" => "", "id" => get_option( 'page_on_front' )];
            $cj->handleRewriteRules($page); 
            $cj->addShortCodes();                
		}
    }
    public function globalInit() {
        //load_plugin_textdomain( PFEA_TEXTDOMAIN, false, PFEA_PLUGIN_PATH.'/customajaxfilters/languages' );
        load_plugin_textdomain( PFEA_TEXTDOMAIN, false, CAF_RELPATH_MAIN.'languages' );
    }
    public function initAdmin() {
        $mautawp=new Admin\AutaPlugin(); 
        $mautawp->initWP();
    }
    
    function mLoadClass($class) {	
		if (strpos($class,"CustomAjaxFilters")!==0) return;
		$path=PFEA_PLUGIN_PATH.str_replace("\\","/",strtolower("$class.php"));		
        require($path);
    }

}