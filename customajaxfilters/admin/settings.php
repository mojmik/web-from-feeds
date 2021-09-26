<?php
namespace CustomAjaxFilters\Admin;
use \CustomAjaxFilters\Majax\MajaxWP as MajaxWP;

class Settings {	
    static private $settings=[];	
    static private $settingsMap=[    
      "site"  => [            
            "currencyFormat" => ["default" => "$%1", "desc" => "currency format used for prices"],           
        ],      
    ];

    static function getPath($path="") {
        return CAF_MAJAX_PATH . "../settings/".$path;
    }
    static function checkPath() { 
        if (!file_exists(Settings::getPath())) {
            mkdir(Settings::getPath(), 0744, true);
        }               
    }
    static function loadSetting($file,$type,$isArray=false) {     
        $key=Settings::getSettingKey($type,$file);	 	 
        if (!array_key_exists($key,Settings::$settings)) {            
            Settings::$settings[$key]=@file_get_contents(Settings::getPath("$key.txt"));                   
        }
        if (empty(Settings::$settings[$key])) {
            if (!empty(Settings::$settingsMap[$type][$file]["default"])) Settings::$settings[$key]=Settings::$settingsMap[$type][$file]["default"];
        }
		if (!$isArray) return Settings::$settings[$key];
		else return explode(";",Settings::$settings[$key]);
	}
	static function writeSetting($file,$in,$isArray=false) {     
        if ($isArray) $in=implode(";",$in);		
        Settings::$settings[$file]=$in;
        @file_put_contents(Settings::getPath()."$file.txt",$in);      		
        return Settings::$settings[$file];
	}

	static function loadSecret($file) {          
        return Settings::loadSetting($file,"secret");		
	}
    static function getSettingKey($type,$name) {
        return "$type-$name";
    }
    static function editAllSettings($table) {
		global $wpdb;
		
		$setting=[];
		if (!isset($_POST["cafActionEditSettings"])) {
			return;
		}		
		foreach (Settings::$settingsMap as $settingsType => $settingsSet) {
            foreach ($settingsSet as $aKey => $setting) {
                $type=(!empty($setting["type"])) ?  $setting["type"] : "";
                if (is_array($setting)) $setting=$aKey;               
                $key=Settings::getSettingKey($settingsType,$setting);	 			
                $val=filter_input( INPUT_POST, $key, FILTER_SANITIZE_STRING );  
                if (isset($val)) {
                    if ($type=="checkbox" && $val=="0") $val="";
                    $sql = $wpdb->prepare("DELETE FROM `$table` WHERE `opt` like '%s'",$key);
                    $wpdb->query($sql);
                    $sql = $wpdb->prepare("INSERT INTO `$table` (`opt`, `val`) values (%s,%s)",$key,$val);				
                    $wpdb->query($sql);
                    Settings::writeSetting($key,$val);
                    Settings::$settings[$key]=$val;
                }			
            }			
		}
		echo "saved";
    }
    static function loadAllSettings($table) {
        global $wpdb;		
		$query = "SELECT * FROM `".$table."`";	
		foreach( $wpdb->get_results($query) as $key => $row) {								
			Settings::$settings[$row->opt]=$row->val;
		}	
    }
    static function adminAllSettings($table) {
        Settings::editAllSettings($table);
        Settings::loadAllSettings($table);
        ?>
		<h2>CAF settings</h2>
			
		<?php
        foreach (Settings::$settingsMap as $settingsType => $settingsSet) {
            ?>
            <h2><?=  $settingsType?>settings</h2>
            <form method='post' class='caf-editFieldRow editSettings'>	
            <?php
            foreach ($settingsSet as $key => $setting) {
                $desc="";
                if (!empty($setting["hide"])) continue;
                if (!empty($setting["desc"])) $desc.="<li>".$setting["desc"]."</li>";
                if (!empty($setting["default"])) $desc.="<li>(default: ".$setting["default"].")</li>";
                $type=(!empty($setting["type"])) ?  $setting["type"] : "";
                if (is_array($setting)) $setting=$key;
                $settingKey=Settings::getSettingKey($settingsType,$setting);	
                $settingValue=(empty(Settings::$settings[$settingKey]) ? "" : Settings::$settings[$settingKey]);
                ?>
                    <div>
                        <div><label><?= $setting?><br /><ul style='font-size:smaller;'><?= $desc?></ul></label></div>
                        <?php
                        if ($type=="checkbox") {
                            ?>  
                                <input type='hidden' value='0' name='<?= $settingKey?>'>
                                <input type='checkbox' name='<?= $settingKey?>' <?= ($settingValue=="1") ? "value='1' checked='checked'" : "" ?> />
                            <?php 
                        } else {
                            ?>                        
                                <input type='text' name='<?= $settingKey?>' value='<?= $settingValue?>' />
                            <?php
                        }
                        ?>
                    </div>	
                <?php
            }
            ?>
            <div><input name='cafActionEditSettings' type='submit' value='Edit' /></div>
			</form>
            <?php
        }
		?>			
				
		<?php
    }
}
