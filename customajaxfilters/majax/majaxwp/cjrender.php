<?php
namespace CustomAjaxFilters\Majax\MajaxWP;


class CJrender {
    function outCategoriesTreeShortCode($atts=[]) {
        $this->currentCat=get_query_var("mikcat");
        ob_start();       
        if (!empty($atts["type"])) { 
            $this->setPostType($atts["type"]);        
            $cats=$this->getCategoriesArr();
        } else return false;
        
        $max= (!array_key_exists("max",$atts)) ? 15 : $atts["max"];
        $brands= (!array_key_exists("nobrands",$atts)) ? true : false;
        $filter= (!array_key_exists("nofilter",$atts)) ? true : false;
        $maxDepth=0;
        if (!$filter) $maxDepth=9;
        if ($brands) $this->getCjTools()->showBrandyNav($this->getMautaFieldName("brand"));
        ?>
        <div>
        <?php
        if ($filter) {
            if ($this->currentCat) {
                ?>
                        <a href='/'><?= $this->getCjTools()->translating->loadTranslation("(all categories)")?></a>
                <?php
            }
            else {
                ?>
                        <strong><?= $this->getCjTools()->translating->loadTranslation("(all categories)")?></strong>    
                <?php
            }
        }    
        $n=0;
        foreach ($cats as $c) {
            //root cats
            if ($max!==null && $max!="0" && $n>$max) break; //display only 15 root cats
            if (!$c["parent"]) { 
                echo $this->outParentCategory($cats,$c,0,$maxDepth);
                $n++;
            }
        }
        ?>
        </div>
        <?php
        return ob_get_clean();
       }

       function outParentCategory($cats,$thisCat,$depth,$maxDepth=1) {
        //recursively output category branch    
        $goDeeper=true;
        ?>    
        <ul>
            <?php         
            if ($this->currentCat==$thisCat["slug"]) {
                ?>
                <li><strong><?= $this->getCategoryNameFromPath($thisCat["path"])?> (<?= $thisCat["counts"]?>)</strong>
                <?php
            }
            else {
                $goDeeper=($depth<$maxDepth) || (strpos($this->currentCat,$thisCat["slug"])!==false);            
                ?>
                <li><a href='<?= $this->getPermaLink($thisCat["slug"])?>'><?= $this->getCategoryNameFromPath($thisCat["path"])?> (<?= $thisCat["counts"]?>)</a>
                <?php
            }            
            if ($goDeeper) {
                foreach ($cats as $key => $c) {
                    if ($c["parent"]===$thisCat["id"]) { 
                        echo $this->outParentCategory($cats,$c,$depth+1,$maxDepth);
                    }
                }
            }        
        ?>        
            </li>
        </ul>
        <?php
       }
       function showBrandyNav($metaName) {                
        $mikBrand=urlDecode(get_query_var("mikbrand"));
        $catSlug=get_query_var("mikcat");
        $brandyArr=$this->getCatMeta($catSlug,$metaName,false,true," LIMIT 1,15","ORDER BY rand()");
        $brandsArr=[];
        if (count($brandyArr)<2) return false;
        foreach ($brandyArr as $brand) {
            $brandVal=$brand["meta_value"];
            if (!in_array($brandVal,$brandsArr) && $brandVal) $brandsArr[]=$brandVal;
        }
        $brandyArr=$brandsArr;
        //if (empty($brandsArr) || count($brandsArr)<2) return "";
        //showBrandyNav($thisTerm->name,$thisTerm->slug,$brandyStr);
        
        if (!empty($this->currentCat)) $name=$this->getCatPathNice();
        else $name="";
        //$brandsText=$this->translating->loadTranslation("products by brands");
        $allBrandsText=$this->translating->loadTranslation("(all brands)");
        ?>
        
        <?php
        if ($mikBrand) {
            ?>
            <a href='<?= $this->getUrl($catSlug)?>'><?= $allBrandsText?></a>
            <?php
        } else {
            ?>
            <strong><?= $allBrandsText?></strong>
            <?php
        }
        ?>
        
        <ul>
        <?php
        foreach ($brandyArr as $brand) {
         //$url="/$mikCatSlug/$catSlug/$mikBrandy/".urlEncode($brand)."/";         
         ?>
         <li>
         <?php
         if ($brand == $mikBrand) {    
          ?>
          <strong><?= $brand?></strong>
          <?php          
         }  
         else {
          ?>
            <a href='<?= $this->getUrl($catSlug,$brand)?>'><?= $brand?></a>
          <?php
         }
         ?>
         </li>
         <?php
        }
        ?>
        </ul>
        <?php
        return true;
    }
}