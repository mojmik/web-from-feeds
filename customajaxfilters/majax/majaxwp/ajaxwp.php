<?php
/*
 this feeds ajax from wordpress with minimal loading
*/
namespace CustomAjaxFilters\Majax\MajaxWP;
use \CustomAjaxFilters\Admin as MajaxAdmin;






Class AjaxWP {
	
	private $atts;
	private $renderer;
	
	function __construct() {
		define('SHORTINIT', true);
		define('DOING_AJAX', true);

		add_action('wp_ajax_filter_rows', [$this,'filter_rows'] );
		add_action('wp_ajax_nopriv_filter_rows', [$this,'filter_rows'] );
		/*
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/customfields.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/customfield.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/majaxhtmlelements.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/majaxform.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/majaxloader.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/majaxquery.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/majaxrender.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/majaxitem.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/caching.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/mikdb.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/imagecache.php');
		require_once(plugin_dir_path( __FILE__ ) . '/majaxwp/translating.php');
		require_once(plugin_dir_path( __FILE__ ) . '../admin/importcsv.php');
		require_once(plugin_dir_path( __FILE__ ) . '../admin/settings.php');
		*/
		define('CAF_TAB_PREFIX','mauta_');
		define('CAF_MAJAX_PATH',plugin_dir_path( __FILE__ ));

		 $this->atts["language"]=(empty($_POST["language"])) ? "" : $_POST["language"];
		 $this->atts["type"]=(empty($_POST["mautaCPT"])) ? "" : $_POST["mautaCPT"];
		 $this->renderer = new MajaxRender(true,$this->atts); //use false pro preloading hardcoded fields (save one sql query)
	}

	function contact_filled() {
		
		MikDb::init(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);		
		if (isset($_POST["category"])) {
			$this->renderer->getMajaxLoader()->getPostIdFromAjax();
			$rows=$this->renderer->getMajaxLoader()->getSingle();
			$this->renderer->showRows($rows,["custTitle" => "single","miscAction"=>"contactFilled"]);		
		}    
		else {
			//form without posts
			$this->renderer->showFormFilled("contactFilled","kontakt form");
		}	
		exit;
	}
	function single_row() {
		$this->renderer = new MajaxRender(true,$this->atts); //use false pro preloading hardcoded fields (save one sql query)
		MikDb::init(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);		
		$this->renderer->getMajaxLoader()->getPostIdFromAjax();
		$rows=$this->renderer->getMajaxLoader()->getSingle();
		$this->renderer->showRows($rows,["custTitle" => "single","miscAction"=>"action"]);		
		exit;
	}
	function formInit() {
		$this->renderer = new MajaxRender(false,$this->atts); //use false pro preloading hardcoded fields (save one sql query)
		$this->renderer->showFormFields($_POST["mautaCPT"]);	
	}
	function filter_rows() {
		$this->renderer = new MajaxRender(true,$this->atts); //use false pro preloading hardcoded fields (save one sql query)
		MikDb::init(DB_HOST,DB_USER,DB_PASSWORD,DB_NAME);	
		
		$page=intval($_POST["aktPage"]);
		$buildCounts=MajaxAdmin\Settings::loadSetting("buildCounts","site");	
		/*
		buildcounts	loads all rows and slice arrays afterwards. might choke in big sites
		if no form filters shown, this is not needed
		*/
		if ($buildCounts) {
			$query=$this->renderer->getMajaxQuery()->produceSQL(["postAll" => true]);
			$rows=Caching::getCachedRows($query);
			$countsJson=Caching::getCachedJson("json_$query");
			$countsRows=$this->renderer->buildCounts($rows,$countsJson);	
			if (!$countsJson) {
				Caching::addCache("json_$query",$countsRows);
			}
			$this->renderer->showRows($countsRows,["custTitle" => "majaxcounts","limit"=>0]);
			$this->renderer->showRows($this->renderer->filterMetaSelects($rows),["aktPage" => $page,"sliceArray"=>true]);		
		} else {
			$rows=$this->renderer->getMajaxLoader()->getMulti(["page" => $page]);
			$this->renderer->showRows($this->renderer->filterMetaSelects($rows),["aktPage" => $page]);		
		}
		
		
		exit;
	}
}


	


