<?php                                                                              

class CMS_XML
{                  
    private $errorno;
    public $error;
    private $arr;
    public $arr_flat = [];
    

    function __construct() {  


       $this->errorno = 0;

       $this->readXmlFlat();

    }

    public function onoffFiles($id, $names, $status)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($names as $name) {
            foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
                if($value1["name"] == $name){
                    break;
                }
            }

            $this->arr_flat[$key]["content"][$key1]["status"] = 1 - $status;
        }

        $this->saveXml();

        return 1 - $status;

    }

    public function onoffFile($id, $name)
    {
    
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        $this->arr_flat[$key]["content"][$key1]["status"] = 1 - $this->arr_flat[$key]["content"][$key1]["status"];

        $this->saveXml();

        return $this->arr_flat[$key]["content"][$key1];
        
    }

    public function optionFile($id, $name)
    {
    
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        $this->arr_flat[$key]["content"][$key1]["option"] = 1 - $this->arr_flat[$key]["content"][$key1]["option"];

        $this->saveXml();

        return $this->arr_flat[$key]["content"][$key1];
        
    }

    public function optionFiles($id, $names, $option)
    {
    
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($names as $name) {

            foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
                 if($value1["name"] == $name){
                    break;
                 }
            }
            
            $this->arr_flat[$key]["content"][$key1]["option"] = 1 - $option;
        }

        $this->saveXml();

        return 1 - $option;
        
    }

    public function onoffSection($id, $status)
    {
    
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        $this->arr_flat[$key]["status"] = 1 - $status;

        $this->saveXml();

        return 1 - $status;
        
    }

    public function listContent($id)
    {

        foreach ($this->arr_flat as $key=>$value) {
          
           if ($value["id"] == $id) {
              break;
           }
        }

        return $this->arr_flat[$key]["content"];
    }

    public function updateContent($id, $data, $name)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        if ($data["th"]>0) $this->arr_flat[$key]["content"][$key1]["th"] = $data["th"];
        if ($data["tw"]>0) $this->arr_flat[$key]["content"][$key1]["tw"] = $data["tw"];
        if ($data["sth"]>0) $this->arr_flat[$key]["content"][$key1]["sth"] = $data["sth"];
        if ($data["stw"]>0) $this->arr_flat[$key]["content"][$key1]["stw"] = $data["stw"];
        if ($data["ih"]>0) $this->arr_flat[$key]["content"][$key1]["ih"] = $data["ih"];
        if ($data["iw"]>0) $this->arr_flat[$key]["content"][$key1]["iw"] = $data["iw"];
        if ($data["sih"]>0) $this->arr_flat[$key]["content"][$key1]["sih"] = $data["sih"];
        if ($data["siw"]>0) $this->arr_flat[$key]["content"][$key1]["siw"] = $data["siw"];
        if ($data["lih"]>0) $this->arr_flat[$key]["content"][$key1]["lih"] = $data["lih"];
        if ($data["liw"]>0) $this->arr_flat[$key]["content"][$key1]["liw"] = $data["liw"];

        $this->saveXml();

        return  $this->arr_flat[$key]["content"][$key1];

    }

    public function addCofile($id, $data, $name)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        $r = strrpos($name, ".");
        $r1 = strrpos($data, ".");
        $newname = substr($name, 0, $r)."".$adds.substr($data, $r1);
        
       
        $this->arr_flat[$key]["content"][$key1]["cofile"] = $newname;

        $this->saveXml();

        return  $this->arr_flat[$key]["content"][$key1];


    }

    public function replaceContent($id, $data, $name)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        $this->arr_flat[$key]["content"][$key1]["name"] = $data["name"];
        $this->arr_flat[$key]["content"][$key1]["thmbname"] = $data["thmbname"];
        $this->arr_flat[$key]["content"][$key1]["th"] = $data["th"];
        $this->arr_flat[$key]["content"][$key1]["tw"] = $data["tw"];
        $this->arr_flat[$key]["content"][$key1]["sth"] = $data["sth"];
        $this->arr_flat[$key]["content"][$key1]["stw"] = $data["stw"];
        $this->arr_flat[$key]["content"][$key1]["ih"] = $data["ih"];
        $this->arr_flat[$key]["content"][$key1]["iw"] = $data["iw"];
        $this->arr_flat[$key]["content"][$key1]["sih"] = $data["sih"];
        $this->arr_flat[$key]["content"][$key1]["siw"] = $data["siw"];
        $this->arr_flat[$key]["content"][$key1]["lih"] = $data["lih"];
        $this->arr_flat[$key]["content"][$key1]["liw"] = $data["liw"];

        $this->saveXml();

        return  $this->arr_flat[$key]["content"][$key1];

    }

    public function updateFile($id, $name, $caption1, $caption2, $caption3, $caption4, $txt) {
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }
        
        $this->arr_flat[$key]["content"][$key1]["caption"] = $caption1;
        $this->arr_flat[$key]["content"][$key1]["caption2"] = $caption2;
        $this->arr_flat[$key]["content"][$key1]["caption3"] = $caption3;
        $this->arr_flat[$key]["content"][$key1]["caption4"] = $caption4;
        $this->arr_flat[$key]["content"][$key1]["txt"] = $txt;
        

        $this->saveXml();

        return $this->arr_flat[$key]["content"][$key1];

    }

    public function moveToSection($id, $to_id, $files, $folders)
    {
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }


        foreach ($this->arr_flat as $key_to=>$value) {
           if ($value["id"] == $to_id) {
              break;
           }
        }

        foreach ($files as $file) {
            if ($this->moveFile($key, $key_to, $file)) {

            } else {
               return false;
            }
        }

        foreach ($folders as $folder) {

           foreach ($this->arr_flat as $key=>$value) {
              if ($value["id"] == $folder) {

                  $this->arr_flat[$key]["parent_id"] = $to_id;
              }
           }
        }


        $this->saveXml();

        return true;
    }

    private function moveFile($key, $key_to, $file)
    {

         $found = false;

         foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
              
              if($value1["name"] == $file){
                 $found = true;
                 break;
              }
         }

         if ($found) {
             $elem = array_splice($this->arr_flat[$key]["content"], $key1, 1);
             
             if (!is_array($this->arr_flat[$key_to]["content"])) {
                 $this->arr_flat[$key_to]["content"] = [];
             }

             array_push($this->arr_flat[$key_to]["content"], $elem[0]);
             
             return true;
         } else {
             $this->error = ERROR8;
             return false;
         }
    }

    public function deleteFiles($id, $names)
    {
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($names as $name) {
            foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
                 if($value1["name"] == $name){
                    break;
                 }
            }
        
            array_splice($this->arr_flat[$key]["content"], $key1, 1);
        }

        $this->saveXml();

        return true;
        
    }

    public function updateSection($id, $name, $descr1, $descr2, $descr3, $descr4, $show_preview)
    {
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        $this->arr_flat[$key]["name"] = $name;
        $this->arr_flat[$key]["description"] = $descr1;
        $this->arr_flat[$key]["description2"] = $descr2;
        $this->arr_flat[$key]["description3"] = $descr3;
        $this->arr_flat[$key]["description4"] = $descr4;
        $this->arr_flat[$key]["show_preview"] = $show_preview;

        $this->saveXml();

        return $this->arr_flat[$key];


    }

    public function deleteSection($id)
    {
    
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }
        
        array_splice($this->arr_flat, $key, 1);
       
        
        $this->saveXml();

        return true;
        
    }


    public function getSectionName($id)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
               return $value["name"];
           }
        }
        return false;
    }


    public function moveFileToPos($id, $aim, $target, $old_pos, $new_pos, $pos)
    {
        
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
               break;
           }
        }
        $key1 = false;
        $key2 = false;

        foreach ($this->arr_flat[$key]["content"] as $key_1=>$value1) {
             if($value1["name"] == $aim){
                $elem = $value1;
                $key1 = $key_1;
                break;

             }
        }

        foreach ($this->arr_flat[$key]["content"] as $key_2=>$value2) {
             if($value2["name"] == $target){
                $key2 = $key_2;
                break;
             }
        }

        if ($key1===false) {
             $this->error = ERROR8." (".$aim.")";
             return false;
        }
        if ($key2===false) {
             $this->error = ERROR8." (".$target.")";
             return false;
        }

        if ($key1 == $key2) {
            return true;
        }

        if ($pos == 'before' && $key2==0) {  //to the beggining
         
           array_splice($this->arr_flat[$key]["content"], $key1, 1);
           array_unshift($this->arr_flat[$key]["content"], $elem);

        } else if ($new_pos>$old_pos){
           if ($pos == 'before') {
               array_splice($this->arr_flat[$key]["content"], $key2, 0, [$elem]);
           } else {
               array_splice($this->arr_flat[$key]["content"], $key2+1, 0, [$elem]);
           }
           array_splice($this->arr_flat[$key]["content"], $key1, 1);
        } else {
           array_splice($this->arr_flat[$key]["content"], $key1, 1);

           if ($pos == 'before') {
               array_splice($this->arr_flat[$key]["content"], $key2, 0, [$elem]);
           } else {
               array_splice($this->arr_flat[$key]["content"], $key2+1, 0, [$elem]);
           }
        }

        $this->saveXml();

        return true;

    }

    public function moveSection($parent_id, $aim, $target, $old_pos, $new_pos, $pos)
    {
        
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["parent_id"] == $parent_id) {
               if($aim == $value["id"]) {
                   $elem = $value;
                   $key1 = $key;
                   break;
               }
               
           }
        }
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["parent_id"] == $parent_id) {
               if($target == $value["id"]) {
                   break;
               }
               
           }
        }
         

        if ($pos == 'before' && $key==0) {  //to the beggining
         
           array_splice($this->arr_flat, $key1, 1);
           array_unshift($this->arr_flat, $elem);

        } else if ($new_pos>$old_pos){
           if ($pos == 'before') {
               array_splice($this->arr_flat, $key, 0, [$elem]);
           } else {
               array_splice($this->arr_flat, $key+1, 0, [$elem]);
           }
           array_splice($this->arr_flat, $key1, 1);
        } else {
           array_splice($this->arr_flat, $key1, 1);

           if ($pos == 'before') {
               array_splice($this->arr_flat, $key, 0, [$elem]);
           } else {
               array_splice($this->arr_flat, $key+1, 0, [$elem]);
           }
        }

        $this->saveXml();

        return true;
    }



    public function getSectionId($name, $parent_id)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["parent_id"] == $parent_id) {
               if($value["name"] == $name) {
                   return $value;
               }
           }
        }
        return false;
    }
      
    public function existsSection($name, $parent_id)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["parent_id"] == $parent_id) {
               if($value["name"] == $name) {
                   return true;
               }
           }
        }
        return false;
    }

    private function makeNewName($name, $adds){

       $r = strrpos($name, ".");
       $newname = substr($name, 0, $r)."".$adds.substr($name, $r);

       return $newname;

    }


    public function duplicateFile($id, $name, $adds)
    {
      
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                $content = $value1;
                break;
             }
        }

        $newcontent = $content;

        $newcontent["name"] = $this->makeNewName($content["name"], $adds);
        $newcontent["thmbname"] = $this->makeNewName($content["thmbname"], $adds);

        
        array_splice($this->arr_flat[$key]["content"], $key1+1, 0, [$newcontent]);

        $this->saveXml();

        return  $content;

    }

    public function duplicateSection($id, $new_name, $withdata)
    {

        $elem = false;
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              $key1 = $key;
              $elem = $value;
           }
        }

        if (!$elem) {
            $this->error = ERROR4;
            return false;
        }

        $elem["id"]  = time();
        $elem["name"] = $new_name;

        if ($withdata==0){
           $elem["content"] = [];
        }

        array_splice($this->arr_flat, $key1+1, 0, [$elem]);

        $this->duplicateSubSection($id, $elem["id"], $withdata);

        $this->saveXml();

        return $new_name;

    }

    private function duplicateSubSection($id, $new_id, $withdata)
    {

        $tmp =  [];

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["parent_id"] == $id) {
              $elem = $value;
              sleep(1);
              $old_id = $elem["id"];
              $elem["id"]  = time();
              $elem["parent_id"]  = $new_id;

              if ($withdata==0){
                  $elem["content"] = [];
              }
              $tmp[] = $elem;
              $this->duplicateSubSection($old_id, $elem["id"], $withdata);
           }
        }

        foreach($tmp as $elem){
            array_push($this->arr_flat, $elem);

        }
        /*
        echo "<pre>";
         print_r($this->arr_flat);
        echo "</pre>";die;
        */
        return;
    }

    public function addSection($name, $type, $parent_id)
    {
        $section  = [];
        $section["id"]  = time();
        $section["name"]  = $name;
        $section["type"]  = $type;
        $section["count"]  = 0;
        $section["status"]  = 1;
        $section["description"]  = '';
        $section["description2"]  = '';
        $section["description3"]  = '';
        $section["description4"]  = '';
        $section["parent_id"]  = $parent_id;

        array_push($this->arr_flat, $section);
                
        $this->saveXml();

        return $section;
    }

    public function updateThumb($id, $name, $data) {
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        $this->arr_flat[$key]["content"][$key1]["th"] = $data["th"];
        $this->arr_flat[$key]["content"][$key1]["tw"] = $data["tw"];

        $this->saveXml();

        return true;

    }

    public function storeVideoSize($id, $name, $width, $height)
    {

        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }

        $this->arr_flat[$key]["content"][$key1]["ih"] = $height;

        $this->arr_flat[$key]["content"][$key1]["iw"] = $width;

        $this->saveXml();

        return true;


    }

    public function addScreenshot($id, $name, $data)
    {
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }

        foreach ($this->arr_flat[$key]["content"] as $key1=>$value1) {
             if($value1["name"] == $name){
                break;
             }
        }
        //var_dump($data); die;

        foreach ($data as $key2=>$value) {
          if ($key2!='name' && $key2!='ih' && $key2!='iw') {
               $this->arr_flat[$key]["content"][$key1][$key2] = $value;
          }
        }

        $this->saveXml();

        return true;
        
    }

    public function addVimeo($id, $name, $link)
    {
        $file  = [];
        $file["name"]  = $name;
        $file["vimeo_id"]  = substr($link,strrpos($link,"/")+1);
        $file["thmbname"] = "";
        $file["status"]  = 1;
        $file["option"]  = 0;
        $file["caption"]  = '';
        $file["caption1"]  = '';
        $file["caption2"]  = '';
        $file["caption3"]  = '';
        $file["caption4"]  = '';
        $file["txt"]  = '';
        $file["cropped"]  = 0;
           
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }
        if (!is_array($this->arr_flat[$key]["content"])) {
            $this->arr_flat[$key]["content"] = [];
        }
        array_push($this->arr_flat[$key]["content"], $file);
                
        $this->saveXml();

        return $file;

    }

    public function addFile($id, $data)
    {
        $file  = $data;
        $file["status"]  = 1;
        $file["option"]  = 0;
        $file["caption"]  = '';
        $file["caption1"]  = '';
        $file["caption2"]  = '';
        $file["caption3"]  = '';
        $file["caption4"]  = '';
        $file["txt"]  = '';
        $file["cropped"]  = 0;
           
        foreach ($this->arr_flat as $key=>$value) {
           if ($value["id"] == $id) {
              break;
           }
        }
        if (!is_array($this->arr_flat[$key]["content"])) {
            $this->arr_flat[$key]["content"] = [];
        }
        array_push($this->arr_flat[$key]["content"], $file);
                
        $this->saveXml();

        return $file;
    }


    function  getPathString($id, $path){
            
        $path_arr = $this->getPathArray($id, array());
    
        $path = '';
    
        if ($path_arr === false) {
            return false;
        }
    
        foreach ($path_arr as $val) {
            $path = "/".$val["name"].$path;
        }

        return FOR_XML.$path;
    
    }
    
    function  getPathArray($id, $path){
    
       //echo "id=".$id."<br>";
    
       if($id==-1){
          return $path;
       }

       $arr = $this->arr_flat;
    
       //echo "arr=".count($arr)."<br>";
    
       $next = false;
    
       for ($i=0;$i<count($arr);$i++){
          //echo $arr[$i]["id"]." == ".$id."<br>";
    
          if($arr[$i]["id"] == $id){
             $path[] = ['name' => $arr[$i]["name"], 'id' => $arr[$i]["id"]];
             $next = $arr[$i]["name"];
             break;
          }
       }
       if ($next === false) {
           return false;
       }
    
       //echo "p=".$next."<br>"; 
       return $this->getPathArray($arr[$i]["parent_id"], $path);
       //return $path;
    }
    
    
        

    function unique_id($l = 8) {
        return substr(md5(uniqid(mt_rand(), true)), 0, $l);
    }


    public function readXml()
    {
        $sxe = simplexml_load_file(FOR_XML."/info.xml");
            
        $xml_arr = $this->parseXml($sxe,"");

        return $xml_arr;

    }

    public function echoScriptPath() {
        list($scriptPath) = get_included_files();
       echo 'The script being executed is ' . $scriptPath;
    }

    private function fromUtils(){
        list($scriptPath) = get_included_files();
        return strpos($scriptPath,'utils');
    }
    
    public function readXmlFlat()
    {
        if ($this->fromUtils()){  
           $sxe = simplexml_load_file("../".FOR_XML."/info.xml", 'SimpleXMLElement', LIBXML_NOCDATA);
        } else {
           $sxe = simplexml_load_file(FOR_XML."/info.xml", 'SimpleXMLElement', LIBXML_NOCDATA);
        }

        if ($sxe===false) {
            $this->arr_flat = false;
            return false;
        }   

        $this->parseXmlFlat($sxe,"","-1");

        return true;

    }

    function parseXmlFlat($sxe,$pre,$parent_id)
    {
     //var_dump($sxe);
     $k = 0;
     $arr = Array();

     //echo count($sxe->section);echo "<br>";

     foreach ($sxe->section as $key=>$section) {
         //echo $pre."<b>";

         $arr[$k] = Array();
         $arr[$k]["parent_id"] = $parent_id;

          foreach($section->attributes() as $a => $b) {
            $arr[$k][$a] = $this->escape($b);
            if($a=="name"){
               //echo $pre.$b." ".$parent_id."<br>";
            }
            if($a=="id"){
               $p_id = $this->escape($b);
            }
            //echo $a,'="',escape($b),"\"\n";
          }
     
          //echo "</b><br>";
          $j = 0;
          foreach ($section->content as $content) {
            //echo $pre.$content."<br>";
            $arr[$k]["content"][$j] = Array();
            $c = ((array)$content->txt);
            //$c = simplexml_load_string($content, null, LIBXML_NOCDATA);
            $arr[$k]["content"][$j]["txt"] =  (string)$c[0];// ? $c[0] : '';
            //echo $pre;
            foreach($content->attributes() as $a => $b) {
                $arr[$k]["content"][$j][$a] = $this->escape($b);
               //echo $a,'="',$arr[$k]["content"][$j][$a],"\"\n";
     
            }
            $j++;
            //echo "<br>";
          }
          array_push($this->arr_flat,$arr[$k]);
          //echo count($this->arr_flat)." ".$arr[$k]["name"]."<br>";
          //var_dump($section);
                        
          $this->parseXmlFlat($section,$pre."--",$p_id);
             
          $k++;
     }    
      
      return true;
    }


    function parseXmlOld($sxe,$pre)
    {
     //var_dump($sxe);
     $k = 0;
     $arr = Array();
     foreach ($sxe->section as $section) {
         //echo $pre."<b>";
         //echo strpos($section["description"],"\n")."<br>";
         $arr[$k] = Array();
    
     foreach($section->attributes() as $a => $b) {
       $arr[$k][$a] = $this->escape($b);
       //echo $a,'="',escape($b),"\"\n";
     }
     
     //echo "</b><br>";
     $j = 0;
     foreach ($section->content as $content) {
       //echo $pre.$content["name"]."<br>";
       $arr[$k]["content"][$j] = Array();
       //echo $pre;
       foreach($content->attributes() as $a => $b) {
         $arr[$k]["content"][$j][$a] = $this->escape($b);
          //echo $a,'="',$arr[$k]["content"][$j][$a],"\"\n";

       }
       $j++;
       //echo "<br>";
         }
    
         $arr[$k]["sections"] = $this->parseXml($section,$pre."--");
        
        $k++;
     }    
      return $arr;
    }


function buildXmlOnlyInfo($arr,$pre,$path){
 $newxml = "";
 $newxmlFull = "";
 $newxmlLight = "";
 $newxmlShort = "";
 $json = "";
 $jsonshort = "";

 global $settings;
 $pre1 = str_replace("\t"," ",$pre);

 for($i=0;$i<count($arr);$i++){
  if($arr[$i]["name"]!="MODELS" && $pre==""){continue;}
  $folder = $path."/".$arr[$i]["name"];

  $info1 = $folder."/info1.txt";
  $info2 = $folder."/info2.txt";
  $text =  $folder."/text.txt";
  $infoxml =  $folder."/data.xml";
  //echo $text."<br>";

  $newxml .= $pre."<section";
  $newxmlFull .= $pre."<section";
  $newxmlLight .= $pre."<s";
  $newxmlShort .= $pre."<s";
  $json .= $pre1."{\n".$pre1;
  $jsonshort .= $pre1."{\n".$pre1;

  while(list($k,$v) = each($arr[$i])){
   if(!is_array($v) && $k!="extraFile"){
     //echo $k."=".$v." ";
     if($k=="status"){$key = "st";}else{$key= substr($k,0,1);}

     if($k=="id"){$key = "id";}

     $newxml .= " ".$k."=\"".$this->unescape($v)."\"";
     $newxmlFull .= " ".$k."=\"".$this->unescape($v)."\"";

     if($k!="description2" && $k!="description3" && $k!="description4"){
      $newxmlLight .= " ".$key."=\"".$this->unescape1($v)."\"";
      $newxmlShort .= " ".$key."=\"".$this->unescape1($v)."\"";

      $json .= "\"".$key."\":\"".$v."\",";
      $jsonshort .= "\"".$key."\":\"".$this->unescape1($v)."\",";
     }

   }
  }
  //alt-source="ogv;webm;..."


  //echo $info1." ".file_exists($info1)." ".filesize($info1)."<br>";
  if(file_exists($info1) && filesize($info1)>0){
    $newxmlLight .= " i1=\""."1"."\"";
    //$newxmlShort .= " i1=\""."1"."\"";
  }else{
    $newxmlLight .= " i1=\""."0"."\"";
    //$newxmlShort .= " i1=\""."0"."\"";
  }
  if(file_exists($info2) && filesize($info2)>0){
    $newxmlLight .= " i2=\""."1"."\"";
    //$newxmlShort .= " i2=\""."1"."\"";
  }else{
    $newxmlLight .= " i2=\""."0"."\"";
    //$newxmlShort .= " i2=\""."0"."\"";
  }
  if(file_exists($text) && filesize($text)>0){

    $newxmlLight .= " ds=\""."1"."\"";
    //$newxmlShort .= " ds=\""."1"."\"";
  }else{
    $newxmlLight .= " ds=\""."0"."\"";
    //$newxmlShort .= " ds=\""."0"."\"";
  }


  $newxml .= ">\n";
  $newxmlFull .= ">\n";
  $newxmlLight .= ">\n";
  $newxmlShort .= ">\n";
  //$json .= $pre."},\n";

    //echo $infoxml."<br>\n";
  if(file_exists($infoxml) && filesize($infoxml)>0){
    
    $newxmlShort .= $pre."<![CDATA[";
    $tf = fopen($infoxml,"r");
    $txt = fread($tf,filesize($infoxml));  
    $newxmlShort .=$pre. $txt;
    $json .= '"info":"'.$txt.'"';    
    $jsonshort .= '"info":"'.$txt.'"';    
    fclose($tf);
    $newxmlShort .= $pre."]]>\n";
  }
 
  if(file_exists($text) && filesize($text)>0){
    //echo "text\n";
    $newxmlShort .= "<![CDATA[";
    $tf = fopen($text,"r");
    $txt = fread($tf,filesize($text));
    $newxmlShort .= $txt;    
    $json .= '"info":"'.$txt.'"';    
    $jsonshort .= '"info":"'.$txt.'"';    
    fclose($tf);
    $newxmlShort .= "]]>\n";
  }
  //echo "</b><br>";
  $tmp = buildXmlOnlyInfo($arr[$i]["sections"],$pre."\t",$path."/".$arr[$i]["name"]);
  $newxml .= $tmp[0];
  $newxmlFull .= $tmp[1];
  $newxmlLight .= $tmp[2];
  $newxmlShort .= $tmp[3];
  $json .= "\n".$pre1."\"sections\":\n".$pre1."[\n".$tmp[4]."".$pre1."],";
  $jsonshort .= "\n".$pre1."\"s\":\n".$pre1."[\n".$tmp[5]."".$pre1."],";


  $newxml .= $pre."</section>\n";
  $newxmlFull .= $pre."</section>\n";
  $newxmlLight .= $pre."</s>\n";
  $newxmlShort .= $pre."</s>\n";

  if($i<(count($arr)-1)){
    $json .= $pre1."},\n";
    $jsonshort .= $pre1."},\n";
  }else{
    $json .= $pre1."}\n";
    $jsonshort .= $pre1."}\n";
  }
 }// main for
 //$json = substr($json, 0, strlen($json)-1); 

 $resA = Array();
 $resA[0] = $newxml;
 $resA[1] = $newxmlFull;
 $resA[2] = $newxmlLight;
 $resA[3] = $newxmlShort;
 $resA[4] = $json;
 
 return $resA;
}

	
function buildXml($arr,$pre,$path,$parent_id){
 $newxml = "";
 $newxmlFull = "";
 $newxmlLight = "";
 $newxmlShort = "";
 $json = "";
 $jsonshort = "";
 $jsonlight = "";

 global $settings;
 $pre1 = str_replace("\t"," ",$pre);

 for($i=0;$i<count($arr);$i++){

  if($arr[$i]["parent_id"]!=$parent_id){continue;}

  //echo str_replace("\t","--",$pre).$arr[$i]["name"]."<br>";
  //echo str_replace("\t","--",$pre).$path."/".$arr[$i]["name"]."<br>";
  $folder = $path."/".$arr[$i]["name"];
  $eList = $this->getExtrafiles($arr[$i]["name"]);

  $info1 = $folder."/info1.txt";
  $info2 = $folder."/info2.txt";
  $text =  $folder."/text.txt";
  //echo $info1."<br>";
  $newxml .= $pre."<section";
  $newxmlFull .= $pre."<section";
  $newxmlLight .= $pre."<s";
  $newxmlShort .= $pre."<s";
  $json .= $pre1."{\n".$pre1;
  $jsonshort .= $pre1."{\n".$pre1;
  $jsonlight .= $pre1."{\n".$pre1;

  //while(list($k,$v) = each($arr[$i])){
  foreach ($arr[$i] as $k=>$v){ 
   if(!is_array($v) && $k!="extraFile"){
     //echo $k."=".$v." ";
     if($k=="status"){$key = "st";}else{$key= substr($k,0,1);}
     if($k=="id"){$key = "id";}

     $newxml .= " ".$k."=\"".$this->unescape($v)."\"";
     $newxmlFull .= " ".$k."=\"".$this->unescape($v)."\"";

     if($k!="description2"  && $k!="description3" && $k!="description4"){
      $newxmlLight .= " ".$key."=\"".$this->unescape1($v)."\"";
      $newxmlShort .= " ".$key."=\"".$this->unescape1($v)."\"";
      $jsonlight .= "\"".$key."\":\"".$this->escapeAmp($this->unescape1($v))."\",";

      $jsonshort .= "\"".$key."\":\"".$this->unescape1($v)."\",";
     }
      $json .= "\"".$k."\":\"".$v."\",";

   }
  }
  //alt-source="ogv;webm;..."


  //echo $info1." ".file_exists($info1)." ".filesize($info1)."<br>";
  if(file_exists($info1) && filesize($info1)>0){
    $newxmlLight .= " i1=\""."1"."\"";
    $newxmlShort .= " i1=\""."1"."\"";
    $jsonlight .= "\""."i1"."\":\""."1"."\",";
  }else{
    $newxmlLight .= " i1=\""."0"."\"";
    $newxmlShort .= " i1=\""."0"."\"";
    $jsonlight .= "\""."i1"."\":\""."0"."\",";
  }
  if(file_exists($info2) && filesize($info2)>0){
    $newxmlLight .= " i2=\""."1"."\"";
    $newxmlShort .= " i2=\""."1"."\"";
    $jsonlight .= "\""."i2"."\":\""."1"."\",";
  }else{
    $newxmlLight .= " i2=\""."0"."\"";
    $newxmlShort .= " i2=\""."0"."\"";
    $jsonlight .= "\""."i2"."\":\""."0"."\",";
  }
  if(file_exists($text) && filesize($text)>0){

    $newxmlLight .= " ds=\""."1"."\"";
    $newxmlShort .= " ds=\""."1"."\"";
    $jsonlight .= "\""."ds"."\":\""."1"."\",";
  }else{
    $newxmlLight .= " ds=\""."0"."\"";
    $newxmlShort .= " ds=\""."0"."\"";
    $jsonlight .= "\""."ds"."\":\""."0"."\",";
  }

   if(count($eList)>0){
      //echo $row->name ."==". substr($eList[$j],0,strrpos($eList[$j],"."))."<br>";
      $u =" extraFile=\"".implode(",",$eList)."\"";
      $newxml .= $u;
      $newxmlFull .= $u;
      $newxmlLight .= $u;
      $newxmlShort .= $u;
      $json .= " \"extraFile\":\"".implode(",",$eList)."\"";
     
   }

  $newxml .= ">\n";
  $newxmlFull .= ">\n";
  $newxmlLight .= ">\n";
  $newxmlShort .= ">\n";
  //$json .= $pre."},\n";

  if(file_exists($text) && filesize($text)>0){
    $newxmlFull .= "<![CDATA[";
    $tf = fopen($text,"r");
    $newxmlFull .= fread($tf,filesize($text));    
    fclose($tf);
    $newxmlFull .= "]]>\n";
  }
  //echo "</b><br>";
  $tmp = $this->buildXml($arr,$pre."\t",$path."/".$arr[$i]["name"],$arr[$i]["id"]);
  $newxml .= $tmp[0];
  $newxmlFull .= $tmp[1];
  $newxmlLight .= $tmp[2];
  $newxmlShort .= $tmp[3];
  $json .= "\n".$pre1."\"sections\":\n".$pre1."[\n".$tmp[4]."".$pre1."],";
  $jsonshort .= "\n".$pre1."\"ss\":\n".$pre1."[\n".$tmp[5]."".$pre1."],";
  $jsonlight .= "\n".$pre1."\"ss\":\n".$pre1."[\n".$tmp[6]."".$pre1."],";

  $include = ($arr[$i]["name"]=="INTRO" || $arr[$i]["name"]=="BACKGROUND");

  $json .= "\n".$pre1."\"content\":\n".$pre1."[\n";
  $jsonshort .= "\n".$pre1."\"c\":\n".$pre1."[\n";
  $jsonlight .= "\n".$pre1."\"c\":\n".$pre1."[\n";

  if (!count($arr[$i]["content"]))  $json .= '""';


  for($ii=0;$ii<count($arr[$i]["content"]);$ii++){
    //echo $arr[$i]["content"][$ii]["name"]."<br>";
   if($arr[$i]["content"][$ii]["name"]==""){
          continue;
   }
   $newxml .= $pre."\t<content";
   $newxmlFull .= $pre."\t<content";
   $newxmlLight .= $pre."\t<c";
   if($include){
    $newxmlShort .= $pre."\t<c";
   }
    $json .= $pre1."  {";
    $jsonlight .= $pre1."  {";

   if($include){
    $jsonshort .= $pre1."  {";
   }
   //while(list($k,$v) = each($arr[$i]["content"][$ii])){
  foreach ($arr[$i]["content"][$ii] as $k=>$v){ 
     //echo $k."=".$v." ";
    //if($k=="thmbname"){
     //$newxml .= " ".$k."=\"".$this->unescape($arr[$i]["content"][$ii]["name"])."\"";
    //}else{ 
     if($k!="cofile" && $k!="alt-source" && $k!="txt"){
       $newxml .= " ".$k."=\"".$this->unescape($v)."\"";
       $newxmlFull .= " ".$k."=\"".$this->unescape($v)."\"";
       $json .= "\"".$k."\":\"".$this->unescapeAmp($v)."\",";
     }

     if($k!="cofile" && $k!="alt-source" && $k!="caption" && $k!="caption2" && $k!="caption3" && $k!="caption4" && $k!="thmbname" && $k!="ih" && $k!="iw" && $k!="sh" && $k!="sw" && $k!="th" && $k!="tw" && $k!="sth" && $k!="stw" && $k!="lih" && $k!="liw"  && $k!="txt"){
       if($k=="cropped"){
        //$newxmlLight .= " ".substr($k,0,2)."=\"".$this->unescape($v)."\"";
       }else{
        $newxmlLight .= " ".substr($k,0,1)."=\"".$this->unescape($v)."\"";
        $jsonlight .= "\"".substr($k,0,1)."\":\"".$this->unescapeAmp($v)."\",";
       }

       if($include){
        if($k=="cropped"){
         //$newxmlShort .= " ".substr($k,0,2)."=\"".$this->unescape($v)."\"";
        }else{
         $newxmlShort .= " ".substr($k,0,1)."=\"".$this->unescape($v)."\"";
         $jsonshort .= "\"".substr($k,0,1)."\":\"".$this->unescapeAmp($v)."\",";
        }
       }
     }

    
   } // while
    $json = substr($json, 0, strlen($json)-1); 
    //$jsonlight = substr($jsonlight, 0, strlen($jsonlight)-1); 

    if($include){
        $jsonshort = substr($jsonshort, 0, strlen($jsonshort)-1); 
    }

   $cg = $arr[$i]["content"][$ii]["name"];
   $alt = substr($cg,0,strrpos($cg,"."));
   
   $alt_arr = array();

   $allowed_alt = explode(" ",$settings["allowed_alt_vd"]);

   for($ai=0;$ai<count($allowed_alt);$ai++){
     //echo $folder."/images/".$alt.".".$allowed_alt[$ai]."<br>";
     if(file_exists($folder."/images/".$alt.".".$allowed_alt[$ai])){
      array_push($alt_arr,$allowed_alt[$ai]);
     }


   }

   if($this->getImageExtrafile($folder."/cofiles/",$alt)){
     $e = " cofile=\"".$this->getImageExtrafile($folder."/cofiles/",$alt)."\"";
     $ej = " ,\"cofile\":\"".$this->getImageExtrafile($folder."/cofiles/",$alt)."\"";
     $ejj = " ,\"cf\":\"".$this->getImageExtrafile($folder."/cofiles/",$alt)."\"";

       $newxml .= $e;
       $newxmlFull .= $e;
       $newxmlLight .= $e;
       if($include){$newxmlShort .= $e;}
       $json .= $ej;
       if($include){$jsonshort .= $ej;}
       $jsonlight .= $ejj;
    }

   if(count($alt_arr)>0){
       $o = " alt-source=\"".implode(";",$alt_arr)."\"";
       $oj = " ,\"alt-source\":\"".implode(";",$alt_arr)."\"";
       $ojj = " \"alt\":\"".implode(";",$alt_arr)."\",";
     

       $newxml .= $o;
       $newxmlFull .= $o;
       $newxmlLight .= $o;
       if($include){$newxmlShort .= $o;}
       $json .= $oj;
       if($include){$jsonshort .= $oj;}
       $jsonlight .= $ojj;
   }
   if($settings["caption1"]!=""){
      $newxmlLight .= " "."c"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption"])."\"";
      $jsonlight .= "\""."c"."\":\"".$this->unescape($arr[$i]["content"][$ii]["caption"])."\",";

      if($include){
       $newxmlShort .= " "."c"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption"])."\"";}
   }
   if($settings["caption2"]!=""){
      $newxmlLight .= " "."c2"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption2"])."\"";
      $jsonlight .= "\""."c2"."\":\"".$this->unescape($arr[$i]["content"][$ii]["caption2"])."\",";
      if($include){
       $newxmlShort .= " "."c2"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption2"])."\"";   
      }
   }
   if($settings["caption3"]!=""){
      $newxmlLight .= " "."c3"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption3"])."\"";
      $jsonlight .= "\""."c3"."\":\"".$this->unescape($arr[$i]["content"][$ii]["caption3"])."\",";
      if($include){
       $newxmlShort .= " "."c3"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption3"])."\"";   
      }
   }
   if($settings["caption4"]!=""){
      $newxmlLight .= " "."c4"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption4"])."\"";
      $jsonlight .= "\""."c4"."\":\"".$this->unescape($arr[$i]["content"][$ii]["caption4"])."\",";
      if($include){
       $newxmlShort .= " "."c4"."=\"".$this->unescape($arr[$i]["content"][$ii]["caption4"])."\"";   
      }
   }

   //$newxmlFull .= " "."i"."=\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\"";
   //$newxmlFull .= " "."t"."=\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\"";
   $newxmlLight .= " "."i"."=\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\"";
   $newxmlLight .= " "."t"."=\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\"";
   $jsonlight .= "\""."i"."\":\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\",";
   $jsonlight .= "\""."t"."\":\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\",";

     $small_vert_image_height = $settings["small_vert_image_height"];
     if($small_vert_image_height==""){$small_vert_image_height = 0;}
     if($small_vert_image_height!=0){
       $iratio = $settings["vert_image_height"]/$small_vert_image_height;
       $a = " "."si"."=\"".($arr[$i]["content"][$ii]["siw"])."x".($arr[$i]["content"][$ii]["sih"])."\""; 
       //$newxmlFull .= $a;
       $newxmlLight .= $a;
       $jsonlight .= "\""."si"."\":\"".($arr[$i]["content"][$ii]["sw"])."x".($arr[$i]["content"][$ii]["sh"])."\"";

     }

   if($include){
    $newxmlShort .= " "."i"."=\"".$arr[$i]["content"][$ii]["iw"]."x".$arr[$i]["content"][$ii]["ih"]."\"";
    $newxmlShort .= " "."t"."=\"".$arr[$i]["content"][$ii]["tw"]."x".$arr[$i]["content"][$ii]["th"]."\"";
     if($small_vert_image_height!=0){
       $newxmlShort .= $a;
     }
   }


   $newxml .= ">\n";
   $newxml .= $pre."\t<txt><![CDATA[".$arr[$i]["content"][$ii]["txt"]."]]></txt>\n";
   $newxml .= $pre."\t</content>\n";

   $newxmlFull .= "/>\n";
   $newxmlLight .= "/>\n";
   if($include){
     $newxmlShort .= "/>\n";
   }
   if($ii<(count($arr[$i]["content"])-1)){
      $json .= "},\n";
      $jsonlight .= "},\n";
      if($include){
         $jsonshort .= "},\n";
      }
   }else{
      $json .= "}\n";
      $jsonlight .= "}\n";
      if($include){
        $jsonshort .= "}\n";
      }
   }
   //echo "<br>";
  } // for content

  $json .= $pre1."]\n";
  $jsonshort .= $pre1."]\n";
  $jsonlight .= $pre1."]\n";

  $newxml .= $pre."</section>\n";
  $newxmlFull .= $pre."</section>\n";
  $newxmlLight .= $pre."</s>\n";
  $newxmlShort .= $pre."</s>\n";

  if($i<(count($arr)-1)){
    $json .= $pre1."},\n";
    $jsonshort .= $pre1."},\n";
    $jsonlight .= $pre1."},\n";
  }else{
    $json .= $pre1."}\n";
    $jsonshort .= $pre1."}\n";
    $jsonlight .= $pre1."}\n";
  }
 }// main for
 //$json = substr($json, 0, strlen($json)-1); 

 $resA = array();
 $resA[0] = $newxml;
 $resA[1] = $newxmlFull;
 $resA[2] = $newxmlLight;
 $resA[3] = $newxmlShort;
 $resA[4] = $json;
 $resA[5] = $jsonshort;
 $resA[6] = $jsonlight;
 
 return $resA;
}

public function saveXml(){

 $xml_arr = $this->arr_flat;
 
 
 $xml = $this->buildXml($xml_arr,"\t",DATA_FOLDER,-1);
 //($xml);


 str_replace("\n","___",$xml[0]);
 str_replace("\n","___",$xml[1]);
 $log_file = "logs/".date("d")."_log.txt";
 $f = fopen($log_file,"a+");
  fwrite($f, "xml length ".strlen($xml[0])."\n");  
 
 if(strlen($xml[0])>=0){
   $fx = fopen("../data_cms/info.xml","w");
   //while ($fx && !flock($fx, LOCK_EX | LOCK_NB)){
     // sleep(1);
      
   //}
   //ftruncate($fx, 0); // очищаем файл
   fwrite($fx,"<sections>\n".$xml[0]."</sections>");
   //flock($fx, LOCK_UN); // снятие блокировки
   fclose($fx);
 }
 
 if(strlen($xml[1])>=0){
   $fxl = fopen("../data_cms/infoFull.xml","w");
   fwrite($fxl,"<sections>\n".$xml[1]."</sections>");
   fclose($fxl);
 }
 if(strlen($xml[2])>=0){
   $fxl = fopen("../data_cms/infolight.xml","w");
   fwrite($fxl,"<ss>\n".$xml[2]."</ss>");
   fclose($fxl);
 }
 if(strlen($xml[3])>=0){
   $fxl = fopen("../data_cms/infoshort.xml","w");
   fwrite($fxl,"<ss>\n".$xml[3]."</ss>");
   fclose($fxl);
 }
 if(strlen($xml[4])>=0){
   $fxl = fopen("../data_cms/info.json","w");
   fwrite($fxl,"[\n".$xml[4]."]");
   fclose($fxl);
 }
 if(strlen($xml[5])>=0){
   $fxl = fopen("../data_cms/infoshort.json","w");
   fwrite($fxl,"[\n".$xml[5]."]");
   fclose($fxl);
 }
 if(strlen($xml[6])>=0){
   $fxl = fopen("../data_cms/infolight.json","w");
   fwrite($fxl,"[\n".$xml[6]."]");
   fclose($fxl);
 }
}

public function escape($pstr){
  $str = str_replace("&amp;","&",$pstr);
  $str = str_replace("&apos;","'",$str);
  $str = str_replace("&quot;","\"",$str);

  return $str;
}
function escapeAmp($pstr){
  $str = str_replace("&amp;","&",$pstr);

  return $str;
}

function unescape1($pstr){
  $str = $this->unescape($pstr);
  if($str=="portfolio"){$str="p";}
  if($str=="text"){$str="t";}
  //$str = addslashes(decode($str));
  return $str;
}
function unescape($pstr){
  $str = str_replace("&","&amp;",$pstr);
  $str = str_replace("'","&apos;",$str);
  $str = str_replace("\"","&quot;",$str);

  return $str;
}
function unescapeAmp($pstr){
  $str = $pstr;
  $str = str_replace("'","&apos;",$str);
  $str = str_replace("\"","&quot;",$str);

  return $str;
}  
  
function decode($nm){
	$nm1="";
	$z = explode("[",$nm);
	$nm1 = $z[0];
	$w;
	for($i=1;$i<count($z);$i++){
		$w = explode("]",$z[$i]);
		$nm1 .=  chr($w[0]).$w[1];
	}
	return $nm1;
}

public function getExtrafiles($fld){
  
  $eList = array();
  $dir = "data_cms/".$fld."/extraFiles/";
  $i=0;
  if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
            if(is_file($dir . $file)){
               $eList[$i++] = $this->unescape($file);
            }
        }
        closedir($dh);
    }
  }
  return $eList;
}

function getImageExtrafile($fld,$nm){
   $dir = "../".$fld;
   $res = false;
   //echo $fld." / nm=".$nm."<br>";
 if (is_dir($dir)) {
    if ($dh = opendir($dir)) {
        while (($file = readdir($dh)) !== false) {
            //echo "filename: $file : filetype: " . filetype($dir . $file) . "<br>";
            if(is_file($dir . $file)){
               
   	       $alt = substr($file,0,strrpos($file,"."));
               if($alt==$nm){  
                 //echo $file."<br>";               
                 $res = $file;
               }
            }
        }
        closedir($dh);
    }
  }
  return $res;
}

    
}