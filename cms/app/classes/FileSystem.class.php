<?php
class FileSystem
{                  
    private $errorno;
    public $error;
   
    

    function __construct() {  
 
       $this->errorno = 0;

    }

    function getNewsXml($path) 
    {
       return file_get_contents($path."/news.xml");
    }

    function saveNewsFile($_param, $xml) 
    {

      $param = trim($_param);
      
      if(!file_exists($param)){
        $a = mkdir($param,0755);
        $a = mkdir($param."/images",0755);
        $a = mkdir($param."/thumbnails",0755);
      }
 
      mkdir($param."/tmp",0755);
      rmdir($param."/tmp");
      return file_put_contents($param."/news.xml", $xml);
    }

    public function listContent($path)
    {   
      $dir = scandir($path.ORIGINALS_FOLDER);
      
      foreach($dir as $index => &$item)
      {
          if(is_dir($path. '/' . $item))
          {
              unset($dir[$index]);
          }
      }
      
      $dir = array_values($dir);

      $res = [];

      foreach($dir as $index => $value)
      {
         $res[] = ["name"=> $value];
      }
      return $res;
    }

    public function updateSEO($path, $seo)
    {
      
      mkdir($path."/tmp",0755);
      rmdir($path."/tmp");
      return file_put_contents($path."/seo.json", $seo);

    }

    public function renameSection($old, $new)
    {
      
      return rename($old, $new);

    }

    private function makeNewName($name, $adds)
    {

       $r = strrpos($name, ".");

       $newname = substr($name, 0, $r)."".$adds.substr($name, $r);

       return $newname;

    }


    public function duplicateFile($path, $arr, $adds)
    {  

        //echo $path."/images/".$arr["thmbname"]."->".$path."/images/".$this->makeNewName($arr["thmbname"], $adds);
        copy($path."/images/".$arr["thmbname"], $path."/images/".$this->makeNewName($arr["thmbname"], $adds));
        copy($path."/images/".$arr["name"], $path."/images/".$this->makeNewName($arr["name"], $adds));
        copy($path."/originals/".$arr["name"], $path."/originals/".$this->makeNewName($arr["name"], $adds));
        copy($path."/".$arr["name"], $path."/".$this->makeNewName($arr["name"], $adds));
        copy($path."/thumbnails/".$arr["thmbname"], $path."/thumbnails/".$this->makeNewName($arr["thmbname"], $adds));
        copy($path."/smallimages/".$arr["thmbname"], $path."/smallimages/".$this->makeNewName($arr["thmbname"], $adds));
        copy($path."/largeimages/".$arr["thmbname"], $path."/largeimages/".$this->makeNewName($arr["thmbname"], $adds));
        copy($path."/thumbnails_cms/".$arr["thmbname"], $path."/thumbnails_cms/".$this->makeNewName($arr["thmbname"], $adds));
        copy($path."/smallthumbnails/".$arr["thmbname"], $path."/smallthumbnails/".$this->makeNewName($arr["thmbname"], $adds));
        copy($path."/thumbnails140/".$arr["thmbname"], $path."/thumbnails140/".$this->makeNewName($arr["thmbname"], $adds));

        return true;
    }

    public function duplicateSection($path, $adds, $withdata)
    {
        $a = mkdir($path.$adds,0755);

        if (!$a) {
            $this->error = ERROR9." (".$path.$adds.")";
            return false;
        }
        
        $this->copyr($path, $path.$adds, intval($withdata));

        return true;
    }

    /**
     * Copy a file, or recursively copy a folder and its contents
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.0.1
     * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
     * @param       string   $source    Source path
     * @param       string   $dest      Destination path
     * @return      bool     Returns TRUE on success, FALSE on failure
     */
    private function copyr($source, $dest, $copyfiles)
    {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }
        
        // Simple copy for a file
        if (is_file($source)) {
            if ($copyfiles) {
                return copy($source, $dest);
            } else {
                return;
            }
        }
    
        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest,0755);
        }
               
        // Loop through the folder
        $dir = dir($source);

        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            $this->copyr("$source/$entry", "$dest/$entry", $copyfiles);
        }
    
        // Clean up
        $dir->close();
        return true;
    }
    
        public function moveFoldersToSection($path, $pathto, $folders)
        {
            foreach($folders as $folder){
               //echo $path."/".$folder."->".$pathto."/".$folder;
           $res = rename($path."/".$folder, $pathto."/".$folder); 
        }
        //var_dump($res); die;
        return $res;
    }

    public function moveFilesToSection($path, $pathto, $files)
    {

      $folders = ["","originals","images","smallimages","largeimages","thumbnails","thumbnails_cms","smallthumbnails","thumbnails140"];

      foreach($files as $filename){
          $name = substr($sec["filename"],0,strrpos($sec["filename"],"."));
          $file = $name.".jpg";
          $pdf = $name.".pdf";

          @rename($path."/".$filename, $pathto."/".$filename);

          foreach($folders as $folder) {
              if (!is_dir($pathto."/".$folder)) { mkdir($pathto."/".$folder);}
               
              @rename($path."/".$folder."/".$file, $pathto."/".$folder."/".$file);
              @rename($path."/".$folder."/".$filename, $pathto."/".$folder."/".$filename);
          }
      }
      return true;

    }


    function addSection($_param){
      
      $settings = getSettings();
      $param = trim($_param);
    
    
      if(file_exists($param)){
        rename($param,$param."_error");
      }

      $a = mkdir($param,0755);
      $a = mkdir($param."/images",0755);
      $a = mkdir($param."/thumbnails",0755);
      $a = mkdir($param."/thumbnails_cms",0755);
    
      $q = explode("/",$param);
      $name = $q[count($q)-1];

      file_put_contents($param."/seo.json", '{"title":"'.$this->decodeName($name).'","description":"","keywords":""}');



      return $a;
    }

function decodeName($nm){
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



    function deleteSection($param){
      $a = $this->fUnlink($param);
    
      return $a;
    }

    function joinFiles($param,$delimiter,$f){
     $settings = getSettings();
    
      $tmp = explode($delimiter,$param);
      $fld = $tmp[0];
      $file1_big = $tmp[2];
      $file1_thmb = $tmp[1];
      $file2_big = $tmp[4];
      $file2_thmb = $tmp[3];
    
      if($settings["join_thmbs"]==1 || $settings["join_thmbs"]=="on"){
    
       $result1 = join_images("../data_cms/".$fld."/thumbnails/",$file1_thmb,$file2_thmb,$f,"thumb");

       if(file_exists("../data_cms/".$fld."/thumbnails140/".$file1_thmb)){
        $result2 = join_images("../data_cms/".$fld."/thumbnails140/",$file1_thmb,$file2_thmb,$f,"thumb140");
       }
       if(file_exists("../data_cms/".$fld."/smallthumbnails/".$file1_thmb)){
        $result5 = join_images("../data_cms/".$fld."/smallthumbnails/",$file1_thmb,$file2_thmb,$f,"smallthumb");
       }
        // not join org
       //$result4 = join_images("../data_cms/".$fld."/",$file1_big,$file2_big,$f,"");
      }
      $result3 = join_images("../data_cms/".$fld."/images/",$file1_big,$file2_big,$f,"image");
      
    
      $small_vert_image_height = $settings["small_vert_image_height"];
      if($small_vert_image_height==""){$small_vert_image_height = 0;}
    
      if($small_vert_image_height!=0){
         $result4 = join_images("../data_cms/".$fld."/smallimages/",$file1_big,$file2_big,$f,"smallimage");
      }
    
    
      $large_vert_image_height = $settings["large_vert_image_height"];
      if($large_vert_image_height==""){$large_vert_image_height = 0;}
      if($large_vert_image_height!=0){
         $result6 = join_images("../data_cms/".$fld."/largeimages/",$file1_big,$file2_big,$f,"largeimage");
      }
    
      if($result3["result"]){
       if(!($settings["join_thmbs"]==1 || $settings["join_thmbs"]=="on")){
         copy("../data_cms/".$fld."/thumbnails/".$file1_thmb,"data_cms/".$fld."/thumbnails/".$file2_thmb);
         @copy("../data_cms/".$fld."/thumbnails140/".$file1_thmb,"data_cms/".$fld."/thumbnails140/".$file2_thmb);
       }  
         unlink("../data_cms/".$fld."/thumbnails/".$file1_thmb);
         @unlink("../data_cms/".$fld."/thumbnails140/".$file1_thmb);
         @unlink("../data_cms/".$fld."/smallthumbnails/".$file1_thmb);
         @unlink("../data_cms/".$fld."/smallthumbnails_bw/".$file1_thmb);
         unlink("../data_cms/".$fld."/images/".$file1_big);
         @unlink("../data_cms/".$fld."/images_bw/".$file1_big);
         @unlink("../data_cms/".$fld."/smallimages/".$file1_big);
         @unlink("../data_cms/".$fld."/largeimages/".$file1_big);
         @unlink("../data_cms/".$fld."/".$file1_big);
      }
      $res["result"] = $result3["result"];
      $res["ih"] = $result3["height"];
      $res["iw"] = $result3["width"];
      $res["th"] = $result1["height"];
      $res["tw"] = $result1["width"];
      if($small_vert_image_height!=0){
    
        $res["sih"] = $result4["height"];
        $res["siw"] = $result4["width"];
      }
      if($settings["join_thmbs"]==0 || $settings["join_thmbs"]=="off"){
        // making thumb
        $t = make_thumb_after_join($fld,$file2_big,$f);
        $res["th"] = $t["height"];
        $res["tw"] = $t["width"];
      }
      return $res;
    }


    function moveSectionInside($param,$details){
       $level = explode("/",$param);
       $tmp = explode("!|",$details);
    
       for($i=0;$i<count($tmp);$i++){
         $kv = explode("=",$tmp[$i]);
         $det[$kv[0]] = $kv[1];
       }
       $src = "data_cms/".$param;
       $trg = "data_cms/".$det["newfld"]."/".$level[count($level)-1];
    
       //echo  $src."->".$trg;
       //echo "<br>";
       if(file_exists($trg)){ return false;}
       $a = rename($src,$trg);
       return $a;
    }
    
    function moveSectionOutside($param,$details){
       $level = explode("/",$param);
       $tmp = explode("!|",$details);

       for($i=0;$i<count($tmp);$i++){
         $kv = explode("=",$tmp[$i]);
         $det[$kv[0]] = $kv[1];
       }
       $src = "data_cms/".$param;
       $trg = "data_cms/".$det["newfld"]."/".$level[count($level)-1];
    
       //echo  $src."->".$trg;
       //echo "<br>";
       if(file_exists($trg)){ return false;}
       $a = rename($src,$trg);
       //exit;
       return $a;
    }
    
    function fUnlink($f){
    	if (is_file($f)) unlink($f);
    	else if (is_dir($f)){
    		$dh=opendir($f);
    		while (false!==($file=readdir($dh))) {
    			if ($file!='.' && $file!='..'){
    				$this->fUnlink($f."/".$file);
    			}
    		}
    		closedir($dh);		
    		rmdir($f);
    	}
    	return true;	
    }
    //------------------------
    function moveContentToSection($path,$details,$delimiter){
      //details=newfld=LEVEL 123!|newsid=1!|id=2!|newid=0!|filename=
       $level = explode("/",$path);
       $levelto = explode("/",$path);
       $tmp = explode("!|",$details);
    
       for($i=0;$i<count($tmp);$i++){
         $kv = explode("=",$tmp[$i]);
         $sec[$kv[0]] = $kv[1];
       }
       $levelto[count($levelto)-1] = $sec["newfld"];
       //echo $levelto[0];
       $pathto = implode("/",$levelto);
     
      //echo $path."/images/".$file."<br>";
      //echo $pathto."/images/".$sec["filename"]."<br>";
      $name = substr($sec["filename"],0,strrpos($sec["filename"],"."));
      //echo $name;
      $filename = $name.".jpg";
      $pdf = $name.".pdf";
      
      $a = rename("../data_cms/".$path."/images/".$sec["filename"],"data_cms/".$pathto."/images/".$sec["filename"]);
      @rename("../data_cms/".$path."/images/".$filename,"data_cms/".$pathto."/images/".$filename);
      @rename("../data_cms/".$path."/images_bw/".$sec["filename"],"data_cms/".$pathto."/images_bw/".$sec["filename"]);
      @rename("../data_cms/".$path."/smallimages/".$sec["filename"],"data_cms/".$pathto."/smallimages/".$sec["filename"]);
      @rename("../data_cms/".$path."/largeimages/".$sec["filename"],"data_cms/".$pathto."/largeimages/".$sec["filename"]);
      @rename("../data_cms/".$path."/".$sec["filename"],"data_cms/".$pathto."/".$sec["filename"]);
      @rename("../data_cms/".$path."/thumbnails/".$filename,"data_cms/".$pathto."/thumbnails/".$filename);
      @rename("../data_cms/".$path."/thumbnails140/".$filename,"data_cms/".$pathto."/thumbnails140/".$filename);
      @rename("../data_cms/".$path."/smallthumbnails/".$filename,"data_cms/".$pathto."/smallthumbnails/".$filename);
      @rename("../data_cms/".$path."/smallthumbnails_bw/".$filename,"data_cms/".$pathto."/smallthumbnails_bw/".$filename);
      @rename("../data_cms/".$path."/pdf/".$pdf,"data_cms/".$pathto."/pdf/".$pdf);
    
      if(is_dir("../data_cms/".$path."/cofiles/")){
            //echo "DIR ".$path."\n";
    	$cdir = scandir("../data_cms/".$path."/cofiles/"); 
    	   foreach ($cdir as $key => $value) 
    	   { 
    	      if (!in_array($value,array(".",".."))) 
    	      { 
                    $vname = substr($value,0,strrpos($value,"."));
                    //echo $vname."==".$name." ".$value."\n";
     	        if($vname==$name){
                       @rename("../data_cms/".$path."/cofiles/".$value,"data_cms/".$pathto."/cofiles/".$value);
    	        }
	      }
           }
      }
      return $a;
    }


    function saveNewName($fld,$details){
       $tmp = explode("!|",$details);
    
       for($i=0;$i<count($tmp);$i++){
         $kv = explode("=",$tmp[$i]);
         $det[$kv[0]] = $kv[1];
       }
      $name = substr($det["oldname"],0,strrpos($det["oldname"], "."));   
      $oldjpgname = $name.".jpg";
    
      $cofile = getImageExtrafile("../data_cms/".$fld."/cofiles/",$name);
      //echo $cofile." ";
      $ext = substr($cofile, strrpos($cofile,".")+1);
    
    
      $name = substr($det["newname"],0,strrpos($det["newname"], "."));   
      $newjpgname = $name.".jpg";
    
      $newcofile = $name.".".$ext;
      //echo $newcofile." ";
      
        
      $a = rename("../data_cms/".$fld."/images/".$det["oldname"],"data_cms/".$fld."/images/".$det["newname"]);
      @rename("../data_cms/".$fld."/largeimages/".$oldjpgname,"data_cms/".$fld."/largeimages/".$newjpgname);
      @rename("../data_cms/".$fld."/smallimages/".$oldjpgname,"data_cms/".$fld."/smallimages/".$newjpgname);
      @rename("../data_cms/".$fld."/images_bw/".$oldjpgname,"data_cms/".$fld."/images_bw/".$newjpgname);
      @rename("../data_cms/".$fld."/thumbnails/".$oldjpgname,"data_cms/".$fld."/thumbnails/".$newjpgname);
      @rename("../data_cms/".$fld."/thumbnails140/".$oldjpgname,"data_cms/".$fld."/thumbnails140/".$newjpgname);
      @rename("../data_cms/".$fld."/thumbnails_bw/".$oldjpgname,"data_cms/".$fld."/thumbnails_bw/".$newjpgname);
      @rename("../data_cms/".$fld."/smallthumbnails/".$oldjpgname,"data_cms/".$fld."/smallthumbnails/".$newjpgname);
    
      @rename("../data_cms/".$fld."/images/".$oldjpgname,"data_cms/".$fld."/images/".$newjpgname);
      //origs
      @rename("../data_cms/".$fld."/".$det["oldname"],"data_cms/".$fld."/".$det["newname"]);
    
      @rename("../data_cms/".$fld."/cofiles/".$cofile,"data_cms/".$fld."/cofiles/".$newcofile);
      
      return $a;                          
    }
    /*
    function getImageExtrafile($fld,$nm){
       $dir = $fld;
   $res = false;
  //echo $fld." ".$nm."<br>";
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
    */
    //-----------------------

    function renameFile($param,$delimiter){
      $fld = explode($delimiter,$param);
      $a = rename("../data_cms/".$fld[0]."/thumbnails/".$fld[1],"data_cms/".$fld[0]."/thumbnails/".$fld[2]);
      @rename("../data_cms/".$fld[0]."/thumbnails140/".$fld[1],"data_cms/".$fld[0]."/thumbnails140/".$fld[2]);
      @rename("../data_cms/".$fld[0]."/pdf/".$fld[1],"data_cms/".$fld[0]."/pdf/".$fld[2]);
      @rename("../data_cms/".$fld[0]."/smallthumbnails/".$fld[1],"data_cms/".$fld[0]."/smallthumbnails/".$fld[2]);
      @rename("../data_cms/".$fld[0]."/largeimages/".$fld[1],"data_cms/".$fld[0]."/largeimages/".$fld[2]);
      @rename("../data_cms/".$fld[0]."/smallimages/".$fld[1],"data_cms/".$fld[0]."/smallimages/".$fld[2]);
      return $a;
    }

    function deleteFiles($fld,$_names){
      foreach ($_names as $_name) {
          $name = substr($_name,0,strrpos($_name, "."));   
          $name1 = $name.".jpg";
          $name2 = $name.".flv";
          $name3 = $name.".mov";
          $name4 = $name.".mp4";
          $name5 = $name.".ogg";
          $name6 = $name.".ogv";
          $name7 = $name.".pdf";
        
          $mask = $fld."/cofiles/".$name.".*";
          @array_map( "unlink", glob( $mask ) );
        
          $mask = $fld."/".$name.".*";
          @array_map( "unlink", glob( $mask ) );
      
          $mask = $fld."/images/".$name.".*";
          @array_map( "unlink", glob( $mask ) );
        
          
          @unlink($fld."/originals/".$name1);
          @unlink($fld."/largeimages/".$name1);
          @unlink($fld."/smallimages/".$name1);
          @unlink($fld."/thumbnails/".$name1);
          @unlink($fld."/thumbnails140/".$name1);
          @unlink($fld."/thumbnails_cms/".$name1);
          @unlink($fld."/smallthumbnails/".$name1);
          @unlink($fld."/pdf/".$name7);
      }
      return true;
    }


    function clearSection($dir){

      clearFolder("../data_cms/".$dir);
      clearFolder("../data_cms/".$dir."/images");
      clearFolder("../data_cms/".$dir."/thumbnails");
      if(is_dir("../data_cms/".$dir."/images_bw")) clearFolder("../data_cms/".$dir."/images_bw");
      if(is_dir("../data_cms/".$dir."/smallimages")) clearFolder("../data_cms/".$dir."/smallimages");
      if(is_dir("../data_cms/".$dir."/largeimages")) clearFolder("../data_cms/".$dir."/largeimages");
      if(is_dir("../data_cms/".$dir."/pdf")) clearFolder("../data_cms/".$dir."/pdf");
      if(is_dir("../data_cms/".$dir."/thumbnails140")) clearFolder("../data_cms/".$dir."/thumbnails140");
      if(is_dir("../data_cms/".$dir."/smallthumbnails")) clearFolder("../data_cms/".$dir."/smallthumbnails");
      if(is_dir("../data_cms/".$dir."/smallthumbnails_bw")) clearFolder("../data_cms/".$dir."/smallthumbnails_bw");
      return true;
    }

    function clearFolder($folder){
      $dh = opendir($folder);
      while (false!==($file=readdir($dh))) {
	if ($file!='.' && $file!='..'){
		if (is_file($folder."/".$file) && !strpos($file,".txt") && !strpos($file,".xml")) unlink($folder."/".$file);
	}
      }

   }


}
?>