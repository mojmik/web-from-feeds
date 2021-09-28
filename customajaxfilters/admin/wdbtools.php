<?php
namespace CustomAjaxFilters\Admin;

use stdClass;

class WDBtools {	
		
	public static function createTableIfNotExists($tableName,$fieldsDef,$args=[]) {
		global $wpdb;
		if($wpdb->get_var("SHOW TABLES LIKE '{$tableName}'") == $tableName) {            
            return true;
        }
      	WDBtools::createTable($tableName,$fieldsDef,$args);
	}
	public static function dropTable($tableName) {
		global $wpdb;			
		$wpdb->query( "DROP TABLE IF EXISTS {$tableName}");
	}
    public static function createTable($tableName,$fieldsDef,$args=[]) {
        global $wpdb;			
		
        $charset_collate = $wpdb->get_charset_collate();
        if (!empty($args["drop"])) WDBtools::dropTable($tableName);
        //check table exists

        $sql = "CREATE TABLE `{$tableName}` (";
        $n=0;
        $primary="";
        foreach ($fieldsDef as $f => $def) {
		 $f="`{$f}`";
		 if ($n>0) $sql.=", ";
		 if (!empty($def["sql"])) {
			$sql.="$f ".$def["sql"];
		 } else {
			$sql.=$f." ".(empty($def["type"]) ? "" : $def["type"]);
			$sql.=(empty($def["notnull"])) ? "" : " NOT NULL";
			$sql.=(empty($def["autoinc"])) ? "" : " AUTO_INCREMENT";
		 }
		 
         if (!empty($def["primary"])) { 
             if ($primary) $primary.=",";
             $primary=$f;
         }
         $n++;
        }
        $sql.=", PRIMARY KEY ($primary)";
		$sql.=") $charset_collate;";
		if (!empty($args["debug"])) echo "<br />sql debug: ".$sql;
        else { 
			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );
		}
	}	

	public static function wpdbTableEmpty($tableName,$where="1") {
		global $wpdb;	
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$tableName} WHERE %s",$where)); 
		if ($count == 0) return true;
		return false;
	}
	public static function wpdbTableCount($tableName,$where="1") {
		global $wpdb;	
		$count = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM {$tableName} WHERE %s",$where)); 
		return $count;
	}
	public static function wpdbGetRows($tableNames,$cols="*",$where=[],$useCache=false) {
		//WDBtools::wpdbGetRows($this->params["cjCatsTable"],["id","path","counts"],[["name"=>"parent","type" =>"%s", "operator" => ">", "value" => $parentId ]]);
		global $wpdb;	
		$values=[];
		if (is_array($cols)) $cols=implode(",",$cols);	
		if (is_array($tableNames)) $tableNames=implode(",",$tableNames);			

		if (!empty($where)) {		  			  
			  $where1="";
			  for ($n=0;$n<count($where);$n++) {
				$operator=(empty($where[$n]["operator"])) ? "=" : $where[$n]["operator"];
				if ($operator=="LIKE") $operator=" LIKE ";
				$type=(empty($where[$n]["type"])) ? "%s" : $where[$n]["type"];
				if ($n>0) $where1.=" AND ";
				$where1.="`".$where[$n]["name"]."`".$operator.$type;
				$values[]=$where[$n]["value"];
			  }
		  	return $wpdb->get_results($wpdb->prepare("SELECT $cols FROM {$tableNames} WHERE $where1 ",$values),ARRAY_A); 
		} else {
			return $wpdb->get_results("SELECT $cols FROM {$tableNames} ",ARRAY_A); 
		}
	}
	public static function wpDbGetRowsPrepared($query,$useCache=false) {
		global $wpdb;	
			if ($wpdb) {
				return $wpdb->get_results($query,ARRAY_A); 
			} 
		
	}
	public static function wpdbGetRowsAdvanced($params) {		
		global $wpdb;	
		if (!empty($params["cols"])) $cols=$params["cols"];
		if (!empty($params["tableNames"])) $tableNames=$params["tableNames"];
		if (!empty($params["orderDir"])) $orderDir=$params["orderDir"];
		if (!empty($params["useCache"])) $useCache=$params["useCache"];
		if (!empty($params["order"])) $order=$params["order"];
		if (!empty($params["limit"])) $limit=$params["limit"];
		if (!empty($params["where"])) $where=$params["where"];


		$values=[];
		if (is_array($cols)) $cols=implode(",",$cols);	
		if (is_array($tableNames)) $tableNames=implode(",",$tableNames);	
		$orderStr="";
		if (!empty($order))	{
			$orderStr.="ORDER BY ";
			$n=0;
			foreach ($order as $o) {
				if ($n>0) $orderStr.=",";
				$orderStr.=$o;
				$n++;
			}
			$orderStr.=" ".$orderDir;
		}
		$limitStr="";
		if (!empty($limit)) {
			$limitStr.="LIMIT ";
			$n=0;
			foreach ($limit as $l) {
				if ($n>0) $limitStr.=",";
				$limitStr.=$l;
				$n++;
			}
		}

		if (!empty($where)) {		  			  
			  $where1="";
			  for ($n=0;$n<count($where);$n++) {
				$operator=(empty($where[$n]["operator"])) ? "=" : $where[$n]["operator"];
				$type=(empty($where[$n]["type"])) ? "%s" : $where[$n]["type"];
				if ($n>0) $where1.=" AND ";
				$where1.="`".$where[$n]["name"]."` ".$operator." ".$type;
				$values[]=$where[$n]["value"];
			  }
			$query=$wpdb->prepare("SELECT $cols FROM {$tableNames} WHERE $where1 $orderStr $limitStr",$values);
			$query=str_replace("'NULL'","NULL",$query);
			
		  	 return $wpdb->get_results($query,ARRAY_A); 
		} else {
			
	      	 return $wpdb->get_results("SELECT $cols FROM {$tableNames} $orderStr $limitStr",ARRAY_A); 
		}
	}
	
	public static function wpdbUpdateRows($tableName,$fields=[],$where=[]) {
		global $wpdb;	
		$sql="UPDATE `$tableName` SET ";	
		$params=[];
		for ($n=0;$n<count($fields);$n++) {
			$type=(empty($fields[$n]["type"])) ? "%s" : $fields[$n]["type"];
			if ($n>0) $sql.=",";
			$sql.="`".$fields[$n]["name"]."` = ".$type;
			$params[]=$fields[$n]["value"];
		}
		$sql.=" WHERE ";
		for ($n=0;$n<count($where);$n++) {
			$type=(empty($where[$n]["type"])) ? "%s" : $where[$n]["type"];
			if ($n>0) $sql.=",";
			$sql.="`".$where[$n]["name"]."` = ".$type;
			$params[]=$where[$n]["value"];
		}
		$sql=$wpdb->prepare($sql,$params);
		return $wpdb->get_results($sql,ARRAY_A); 
	}
	public static function getWPprefix() {
		global $wpdb;	
		if (!empty($wpdb)) return $wpdb->prefix;
		return "";
	}
	public static function getTablePrefix() {		
		$fixPrefix="mauta_";
		return WDBtools::getWPprefix().$fixPrefix;
	}
	public static function  getInsertQueryFromArray($table,$mArr,$skipCols=[]) {
		$query="INSERT INTO `$table` SET ";
		$n=0;
		foreach ($mArr as $colName => $mVal) {   
		  //echo "<br />colname:$colName value:$mVal";
		  if (!in_array($colName,$skipCols)) {
			if ($n>0) $query.=",";   
			$query.="`$colName`='$mVal'";
			$n++;
		 }
		}
		return $query;
	}
	public static function insertRow($table,$mArr,$skipCols=[]) {
		global $wpdb;
		$sql=WDBtools::getInsertQueryFromArray($table,$mArr,$skipCols);
		$wpdb->get_results($sql);
		return $wpdb->insert_id;
	}
	public static function clearTable($table,$where=[]) {
		global $wpdb;
		if (count($where)<1) $wpdb->query("TRUNCATE TABLE `$table`");
		else {
			$sql="DELETE FROM `$table` WHERE ";
			$n=0;
			foreach ($where as $w) {
				if ($n>0) $sql.=" AND ";
				$sql.=$w;
				$n++;
			}
			$wpdb->query($sql);
		}
	}	
	public static function makeWhere($whereArr=[]) {
		$where="";
		$n=0;
		foreach ($whereArr as $w) {
			if ($w) {				
				if ($n>0) $where.=" AND ".$w;
				else $where.=$w;
				$n++;
			}			
		}
		if ($where) $where=" WHERE ".$where;
		return $where;
	}	
}