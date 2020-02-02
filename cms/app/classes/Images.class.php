<?php
class Images
{                  
    private $errorno;
    public $error;
    private $settings;
    private $namemode;
    private $uploads_path = "uploads";
    private $originals = ORIGINALS_FOLDER;
    private $filename = "";
    private $target_path = "";
    private $ext = "";
    private $new_id;
    private $width = 0;
    private $height = 0;
    private $orig_ratio = 1;
    private $resizemode = "new";
    private $target = '';

    function __construct($settings) {  
 
       $this->errorno = 0;
       $this->settings = $settings;
       $this->namemode = "after";
    }


    public function regenerate($filename, $path, $target)
    {               
        $this->filename = $filename;
        $this->target_path = $path;
        $this->uploads_path = $path.$this->originals;
        $this->target = $target;
        $this->resizemode = "regenerate";

	$allowed_i = explode(" ",$this->settings["allowed_image"]);
	$allowed_v = explode(" ",$this->settings["allowed_vd"]);


        $this->filename = $this->getName("media");  // for video get screenshot if exists
        
        
	if (!file_exists($this->uploads_path.$this->filename)){

	    return false;
	}

	$ft = pathinfo($this->uploads_path."/".$this->filename);

	$this->ext = strtolower($ft["extension"]);

        
        if (in_array($this->ext, $allowed_i)){
            return $this->resizeImage();
        }
        if (in_array($this->ext, $allowed_v)){
            return $this->resizeImage();
        }

        return false;

    }

    public function cropImage($path, $filename, $target, $coords)
    {
        $this->filename = $filename;
        $this->target_path = $path;

        if ($target=="thumbnails" || $target=="smallthumbnails" ) {
            $quality = $this->settings["thumbnail_quality"];
        } else {
            $quality = $this->settings["image_quality"];
        }

        list($width, $height) = getimagesize($this->target_path.$this->originals.$this->filename);

	$ft = pathinfo($this->target_path.$this->originals.$this->filename);

	$this->ext = strtolower($ft["extension"]);


        if (!file_exists($this->target_path.$this->originals.$this->filename)) {
            $this->error = ERROR10." (".$this->target_path.$this->originals.$this->filename.")";
            return false;
        }

        $x1 = $coords["x1"] ? $width/$coords["x1"] : 0;
        $y1 = $coords["y1"] ? $height/$coords["y1"] : 0;
        $x2 = $width/$coords["x2"];
        $y2 = $height/$coords["y2"];

        $crop_x = $x1;
        $crop_y = $y1;
        $crop_width = $x2-$x1;
        $crop_height = $y2-$y1;

        $smallfile = ImageCreateTrueColor($crop_width,$crop_height);
 
        $source = imagecreatefromjpeg($this->target_path.$this->originals.$this->filename);
 
        //var_dump($smallfile, $source, 0, 0, $crop_x, $crop_y, $crop_width, $crop_height, $crop_width, $crop_height);
        imagecopyresampled($smallfile, $source, 0, 0, $crop_x, $crop_y, $crop_width, $crop_height, $crop_width, $crop_height);

        $a = imagejpeg($smallfile,$this->uploads_path."/".$this->filename,$quality);
        

        imagedestroy($smallfile);

        imagedestroy($source);

        if (!$a) {
           $this->error = ERROR11;
           return false;
        } else {

            $this->resizemode = "crop";
            $this->target = $target; 
            $res = $this->resizeImage();
            unlink($this->uploads_path."/".$this->filename);
            return $res;

        }

    }




    public function addScreenshot($filename, $path)
    {
        $this->filename = $filename;
        $this->target_path = $path;
        $this->resizemode = "screenshot";

	$allowed_i = explode(" ",$this->settings["allowed_image"]);

	$ft = pathinfo($this->uploads_path."/".$filename);

	$this->ext = strtolower($ft["extension"]);

        if (in_array($this->ext, $allowed_i)){

            if (!is_dir($path.$this->originals)) {@mkdir($path.$this->originals);}
                                          
            $source_img = imagecreatefrompng($this->uploads_path."/".$filename);
            $a = imagejpeg($source_img,$path.$this->originals.$this->getName("media"),100);

            if ($a){

            } else {
                $this->error = ERROR12." ".$this->originals;
                return false;
            }
            

            return $this->resizeImage();  

        }
    }

    public function replaceFile($filename, $path)
    {
        $this->filename = $filename;
        $this->target_path = $path;
        $this->new_id = rand(1000, 9999);
        $this->resizemode = "replace";

        return $this->proccessFile();
    }

    public function replaceThumb($filename, $current_filename, $path)
    {
        
        $this->filename = $current_filename;
        $this->target_path = $path;
        $this->new_id = rand(1000, 9999);
        $this->resizemode = "replace_thumb";

	$ft = pathinfo($this->uploads_path."/".$filename);

	$this->ext = strtolower($ft["extension"]);



        if ($this->convert($this->uploads_path."/".$filename,$this->uploads_path."/".$current_filename)) {
            unlink($this->uploads_path."/".$filename);

        } else {
            $this->error = ERROR13;
            return false;
        }

	$ft = pathinfo($this->uploads_path."/".$current_filename);

	$this->ext = strtolower($ft["extension"]);


        $res  = $this->resizeImage();  

        if ($res) {
            unlink($this->uploads_path."/".$current_filename);
        }

        return $res;
    }

    public function addFile($filename, $path)
    {
        $this->filename = $filename;
        $this->target_path = $path;
        $this->new_id = rand(1000, 9999);
        $this->resizemode = "new";

        return $this->proccessFile();
    }

    public function proccessFile(){

	$allowed_i = explode(" ",$this->settings["allowed_image"]);
	$allowed_v = explode(" ",$this->settings["allowed_vd"]);
	$allowed_m = explode(" ",$this->settings["allowed_audio"]);
	$allowed_o = explode(" ",$this->settings["allowed_other"]);

	$ft = pathinfo($this->uploads_path."/".$this->filename);

	$this->ext = strtolower($ft["extension"]);
	
        if (in_array($this->ext, $allowed_i)){

            if (!is_dir($this->target_path.$this->originals)) {@mkdir($this->target_path.$this->originals);}

            if ($this->convert($this->uploads_path."/".$this->filename,$this->target_path.$this->originals.$this->getName("thumb"))) {
                
            } else {
                $this->error = ERROR13." ".$this->originals;
                return false;
            }
            

            return $this->resizeImage();  

        }else if (in_array($this->ext, $allowed_v) || in_array($this->ext, $allowed_m) || in_array($this->ext, $allowed_o)){
            $video_folder = "/images/"; // media

            if (!is_dir($this->target_path.$video_folder)) {mkdir($this->target_path.$video_folder);}
                       
            if (copy($this->uploads_path."/".$this->filename, $this->target_path.$video_folder.$this->getName("media"))) {
            
               if ($this->ext == 'gif'){

                   $result = $this->resizeImage();
                   $result["name"]  = $this->getName("media");
                   return $result;

               } else if (in_array($this->ext, $allowed_o)) { //svg, animated gif

                   if (!is_dir($this->target_path."/thumbnails_cms")) {mkdir($this->target_path."/thumbnails_cms");}
                   if (!is_dir($this->target_path."/thumbnails_instagram")) {mkdir($this->target_path."/thumbnails_instagram");}

                   copy($this->uploads_path."/".$this->filename, $this->target_path."/thumbnails/".$this->getName("media"));
                   copy($this->uploads_path."/".$this->filename, $this->target_path."/thumbnails_cms/".$this->getName("media"));
                   copy($this->uploads_path."/".$this->filename, $this->target_path."/thumbnails_instagram/".$this->getName("media"));
               }

            } else {
               $this->error = ERROR14." ".$video_folder;
               return false;
            }
           
        } else {

           $this->error = ERROR15;
           return false;
        }

        $result = ["th" => 0, "tw" => 0, "ih" => 0, "iw" => 0, "sih" => 0, "siw" => 0, "lih" => 0, "liw" => 0 ];
        $result["name"]  = $this->getName("media");
        $result["thmbname"]  = "";

        if (in_array($this->ext, $allowed_o)) { //svg, animated gif
           $result["thmbname"]  = $this->getName("media");
        }

        return $result;
       
    }

    public function resizeImage()
    {             
        $a = getimagesize($this->uploads_path."/".$this->filename);

        if(!$a){
            $this->error = ERROR16." (".$this->filename.")";
   	    return false;
        }


        list($width, $height) = $a;
        $this->width = $width;
        $this->height = $height;

        $this->orig_ratio = $this->height/$this->width;


	if (function_exists('exif_read_data') && $a['mime'] == 'image/jpeg') {
           $exif = exif_read_data($this->uploads_path."/".$this->filename, "FILE,COMPUTED,ANY_TAG,IFD0,THUMBNAIL,COMMENT,EXIF", true);
           $orient = $exif['IFD0']['Orientation'];
        } else {
           $exif = false;
           $orient = false;
        }

        
        $res1 = $this->createThumbs();

        $res2 = $this->createImages();

        if ($res1===false || $res2 === false) {
           
            return false;
        } else {
            //var_dump($res1);
            //var_dump($res2);
            return array_merge($res1, $res2);
        }
    }

    function getName($type)
    {
        $name = substr($this->filename,0,strrpos($this->filename, "."));   

       
        if ($this->resizemode=="screenshot") {
            return $name.".jpg";
        }

        if ($this->resizemode=="crop") {
            return $name.".jpg";
        }

        if ($this->resizemode=="regenerate") {
            return $name.".jpg";
        }

        if ($this->resizemode=="replace_thumb") {
            return $name.".jpg";
        }

	if($this->resizemode=="new" || $this->resizemode=="replace"){
          if($this->namemode=="before"){
            $new_name = $this->new_id."_".$name.".jpg";
            $bigfile_name = $this->new_id."_".$name.".".$this->ext;
          }else{
            $new_name = $name."_".$this->new_id.".jpg";
            $bigfile_name = $name."_".$this->new_id.".".$this->ext;
         }
        }

        if ($type=="thumb") {
           return $new_name; 
        } else if ($type=="media") {
           return $bigfile_name; 
        }

    }

    function calcSize($ver_h, $hor_h, $max_w)
    {    
        $ver_h = intval($ver_h);
        $hor_h = intval($hor_h);
        $max_w = intval($max_w);
                                 
        $newwidth = $this->width;
        $newheight = $this->height;

	if ($this->orig_ratio>1) { //hor
          if ($hor_h < $this->height){
		$newheight = $hor_h;
		$newwidth = $hor_h / $this->orig_ratio;	 
	  }
	  if ($max_w < $newwidth){
		$newwidth = $max_w;
		$newheight = $max_w * $this->orig_ratio;	   	   
	  }
	} else { //ver
	   if ($ver_h < $this->height){
		$newheight = $ver_h;
		$newwidth = $ver_h / $this->orig_ratio;	 
	   }
   	}

        return	["height" => round($newheight), "width" => round($newwidth)];
    }

    function createImages()
    {                  
        if ($this->resizemode == 'replace_thumb') {
             return [];
        }

        $result = ["name" => "", "thmbname" => '', "ih" => 0, "iw" => 0, "sih" => 0, "siw" => 0, "lih" => 0, "liw" => 0 ];
        
        $result["name"]  = $this->getName("thumb");
        $result["thmbname"]  = $this->getName("thumb");

        if ($this->target == 'originals') {

            $newsizes = $this->calcSize($this->settings["resize_original_to_max_height"], $this->settings["resize_original_to_max_height"], $this->settings["resize_original_to_max_width"]);
            // var_dump($newsizes);echo "<br>";              
          
            if ($this->resize_image($this->uploads_path."".$this->filename, 
                                    $this->uploads_path."".$this->filename,
                                    0,0,0,0,
                                    $newsizes["height"],
                                    $newsizes["width"],
                                    $this->settings["original_quality"],false)) {
                //ECHO $this->uploads_path."".$this->filename."<br>";

                $result["ih"] = $newsizes["height"];
                $result["iw"] = $newsizes["width"];
            } else {
                $result = false;
                $this->error = ERROR17." (/images/)";
            }

            return $result;
        }


        $newsizes = $this->calcSize($this->settings["vert_image_height"], $this->settings["hor_image_height"], $this->settings["hor_image_max_width"]);
         
        if (($this->target=='' || $this->target=='images') && $newsizes["height"]) {

            $this->checkFolder("images");
            if ($this->resize_image($this->uploads_path."/".$this->filename, 
                                    $this->target_path."/images/".$this->getName("thumb"),
                                    0,0,0,0,
                                    $newsizes["height"],
                                    $newsizes["width"],
                                    $this->settings["image_quality"],false)) {

                $result["ih"] = $newsizes["height"];
                $result["iw"] = $newsizes["width"];
            } else {
                $result = false;
                $this->error = ERROR17." (/images/)";
            }
        
        }
        if (!$result) { return false;}

        $newsizes = $this->calcSize($this->settings["large_vert_image_height"], $this->settings["large_hor_image_height"], $this->settings["large_hor_image_max_width"]);

        if ($this->target=='largeimages' && $this->settings["large_vert_image_height"]==0 && $this->settings["large_hor_image_height"]==0) {
        
            $result["th"] = "delete ";
            @unlink($this->target_path."/largeimages/".$this->getName("thumb"));
            return $result;
        }

        if (($this->target=='' || $this->target=='largeimages') && $newsizes["height"]) {



            $this->checkFolder("largeimages");

            if ($this->resize_image($this->uploads_path."/".$this->filename, 
                                    $this->target_path."/largeimages/".$this->getName("thumb"),
                                    0,0,0,0,
                                    $newsizes["height"],
                                    $newsizes["width"],
                                    $this->settings["largeimage_quality"],false)) {
                $result["lih"] = $newsizes["height"];
                $result["liw"] = $newsizes["width"];
            } else {
                $result = false;
                $this->error = ERROR17." (/largeimages/)";
            }
        
        }
        if (!$result) { return false;}


        $newsizes = $this->calcSize($this->settings["small_vert_image_height"], $this->settings["small_hor_image_height"], $this->settings["small_hor_image_max_width"]);
        
        if ($this->target=='smallimages' && $this->settings["small_vert_image_height"]==0 && $this->settings["small_hor_image_height"]==0) {
        
            $result["th"] = "delete ";
            @unlink($this->target_path."/smallimages/".$this->getName("thumb"));
            return $result;
        }

        if (($this->target=='' || $this->target=='smallimages') && $newsizes["height"]) {

            $this->checkFolder("smallimages");
            if ($this->resize_image($this->uploads_path."/".$this->filename, 
                                    $this->target_path."/smallimages/".$this->getName("thumb"),
                                    0,0,0,0,
                                    $newsizes["height"],
                                    $newsizes["width"],
                                    $this->settings["smallimage_quality"],false)) {
                                    
                $result["sih"] = $newsizes["height"];
                $result["siw"] = $newsizes["width"];
            } else {
                $result = false;
                $this->error = ERROR17." (/smallimages/)";
            }
        
        }
        if (!$result) { return false;}
  
        return $result;
        
    }

    function createThumbs()
    {
       
        $result = ["th" => 0, "tw" => 0, "sth" => 0, "stw" => 0];


        $thumbheight = intval($this->settings["thumbnail_height"]);
        $thumbwidth = intval($this->settings["thumbnail_width"]);
        $thumbnail_background = $this->settings["thumbnail_background"];
        $thumbnail_backgroundRGB = sscanf($thumbnail_background, '0x%2x%2x%2x');
         
        $smallthumbheight = $this->settings["smallthumbnail_height"];
        $smallthumbwidth = $this->settings["smallthumbnail_width"];
        $smallthumbnail_background = $this->settings["thumbnail_background"];
        $smallthumbnail_backgroundRGB = sscanf($thumbnail_background, '0x%2x%2x%2x');

        $instagramheight = intval($this->settings["instagram_height"]);
        $instagramwidth = intval($this->settings["instagram_width"]);

    
        //thumbnails--------------------------------------------------------

         $src_dh_cms = 0;
         $src_dw_cms = 0;
         $src_y_cms = 0;
         $src_x_cms = 0;

         $src_dh = 0;
         $src_dw = 0;
         $src_y = 0;
         $src_x = 0;

        if($thumbheight!=0 && $thumbwidth==0){
    
            $thumbwidth = round($thumbheight/$this->orig_ratio); 
    
        }else if($thumbheight==0 && $thumbwidth!=0){
    
            $thumbheight = round($thumbwidth*$this->orig_ratio);
    
        }else if($thumbheight!=0 && $thumbwidth!=0){
    
            $d_ratio = $thumbheight/$thumbwidth;
             
            
            if($this->orig_ratio>$d_ratio){
             $w = $this->width/$thumbwidth;
             $src_dh = $this->height - $thumbheight*$w;
             $src_dw = 0;
             $src_y = 0;
             $src_x = 0;
             $src_y = $src_dh/2;
            
             $w140 = $this->width/$thumbwidth140;
             $src_dh140 = $this->height - $thumbheight140*$w140;
             $src_dw140 = 0;
             $src_y140 = 0;
             $src_x140 = 0;
             $src_y140 = $src_dh140/2;
            
            }else{
             $h = $this->height/$thumbheight;
             $src_dw = $this->width - $thumbwidth*$h;
             $src_dh = 0;
             $src_x = 0;
             $src_y = 0;
             $src_x = $src_dw/2;
            
             $h140 = $this->height/$thumbheight140;
             $src_dw140 = $this->width - $thumbwidth140*$h140;
             $src_dh140 = 0;
             $src_x140 = 0;
             $src_y140 = 0;
             $src_x140 = $src_dw140/2;
            
            
            }                 
       }
    
        if($thumbheight>=$this->height && $thumbwidth>=$this->width){
    
          $thumbheight = $this->height;
          $thumbwidth = $this->width;
          $src_dh = 0;
          $src_x = 0;
          $src_y = 0;
          $src_dw = 0;
       }
       if($thumbheight140>=$this->height && $thumbwidth140>=$this->width){
          $thumbheight140 = $this->height;
          $thumbwidth140 = $this->width;
          $src_dh140 = 0;
          $src_x140 = 0;
          $src_y140 = 0;
          $src_dw140 = 0;
       }
       //var_dump([$instagramheight,$instagramwidth]);
       if($instagramheight!=0 && $instagramwidth!=0){
            $d_ratio = 1;
             
            
            if($this->orig_ratio>$d_ratio){
             $w = $this->width/$instagramwidth;
             $src_dh_instagram = $this->height - $instagramheight*$w;
             $src_dw_instagram = 0;
             $src_y_instagram = 0;
             $src_x_instagram = 0;
             $src_y_instagram = $src_dh_instagram/2;
            
            }else{
             $h = $this->height/$instagramheight;
             $src_dw_instagram = $this->width - $instagramwidth*$h;
             $src_dh_instagram = 0;
             $src_x_instagram = 0;
             $src_y_instagram = 0;
             $src_x_instagram = $src_dw_instagram/2;
            
            }                 

       }
         //echo "thumb w=".$thumbwidth." thumb h=".$thumbheight."\n";
         //echo "thumb140 w=".$thumbwidth140." thumb h=".$thumbheight140."\n";
    
    //smallthumbnails------------------------------------------
    
       if($smallthumbheight!=0 && $smallthumbwidth==0){
    
           $smallthumbwidth = round($smallthumbheight/$this->orig_ratio); 
    
       }else if($smallthumbheight==0 && $smallthumbwidth!=0){
    
           $smallthumbheight = round($smallthumbwidth*$this->orig_ratio);
    
       }else if($smallthumbheight!=0 && $smallthumbwidth!=0){
    
         $d_ratio = $smallthumbheight/$smallthumbwidth;
    
        if($this->orig_ratio>$d_ratio){
         $w = $this->width/$smallthumbwidth;
         $s_src_dh = $this->height - $smallthumbheight*$w;
         $s_src_dw = 0;
         $s_src_y = 0;
         $s_src_x = 0;
         $s_src_y = $s_src_dh/2;
    
         //fwrite($f,"small vert ".$s_src_dh."\n");
        }else{
         $h = $this->height/$smallthumbheight;
         $s_src_dw = $this->width - $smallthumbwidth*$h;
         $s_src_dh = 0;
         $s_src_x = 0;
         $s_src_y = 0;
         $s_src_x = $s_src_dw/2;
    
         //fwrite($f,"small hor ".$s_src_dw."\n");
        }                 
       }
    
        if($smallthumbheight>=$this->height && $smallthumbwidth>=$this->width){
         //fwrite($f,"pic smaller then small thumb settings"."\n");
    
          $smallthumbheight = $this->height;
          $smallthumbwidth = $this->width;
          $s_src_dh = 0;
          $s_src_x = 0;
          $s_src_y = 0;
          $s_src_dw = 0;
       }
    
             //echo "new size thumb ".$thumbwidth."x".$thumbheight."\n";
             //echo "new size thumb140 ".$thumbwidth140."x".$thumbheight140."\n";
    
        $thumb_quality = $this->settings["thumbnail_quality"];
        
        if (($this->target=='' || $this->target=='thumbnails')) {

            $this->checkFolder("thumbnails");
          
            if ($this->resize_image($this->uploads_path."/".$this->filename, $this->target_path."/thumbnails/".$this->getName("thumb"),$src_x,$src_y,$src_dh,$src_dw,$thumbheight,$thumbwidth,$thumb_quality,false)){
        
                    $result["th"] = $thumbheight;
                    $result["tw"] = $thumbwidth;
            } else {
               $result = false;
                $this->error = ERROR17." (/thumbnails/)";
            }
            
            if (!$result) { return false;}
            
        
            $this->checkFolder("thumbnails_cms");
        
    	    //if ($this->resize_image($this->uploads_path."/".$this->filename, $this->target_path."/thumbnails_cms/".$this->getName("thumb"),$src_x_cms,$src_y_cms,$src_dh_cms,$src_dw_cms,$thumbheight_cms,$thumbwidth_cms,$thumb_quality,false)){
    	    if ($this->copy_resized_image($this->target_path."/thumbnails/".$this->getName("thumb"), $this->target_path."/thumbnails_cms/".$this->getName("thumb"),$thumbheight,$thumbwidth,$thumb_quality,false)){
        
            } else {
               $result = false;
                $this->error = ERROR17." (/thumbnails_cms/)";
            }
            if (!$result) { return false;}
        
        
               
	} // if target

        if (($this->target=='' || $this->target=='instagram')) {

            $this->checkFolder("thumbnails_instagram");
            //var_dump([$src_x_instagram,$src_y_instagram,$src_dh_instagram,$src_dw_instagram,$instagramheight,$instagramwidth]);
            if ($this->resize_image($this->uploads_path."/".$this->filename, $this->target_path."/thumbnails_instagram/".$this->getName("thumb"),$src_x_instagram,$src_y_instagram,$src_dh_instagram,$src_dw_instagram,$instagramheight,$instagramwidth,$thumb_quality,false)){
        
            } else {
                $result = false;
                $this->error = "Error creating /thumbnails_instagram/";
            }
            if (!$result) { return false;}


	} // if target


	//-----smallthumbnails
        if ($this->target=='smallthumbnails' && $smallthumbheight==0 && $smallthumbwidth==0) {
        
            $result["th"] = "delete ";
            @unlink($this->target_path."/smallthumbnails/".$this->getName("thumb"));
            return $result;
        }

        if (($this->target=='' || $this->target=='smallthumbnails') && ($smallthumbheight!=0 || $smallthumbwidth!=0)) {

           $this->checkFolder("smallthumbnails");

           //echo "new size sthumb ".$smallthumbwidth."x".$smallthumbheight."\n";

	   if ($this->resize_image($this->uploads_path."/".$this->filename, $this->target_path."/smallthumbnails/".$this->getName("thumb"),$s_src_x,$s_src_y,$s_src_dh,$s_src_dw,$smallthumbheight,$smallthumbwidth,$thumb_quality,false)){
                    $result["sth"] = $smallthumbheight;
                    $result["stw"] = $smallthumbwidth;
       	                             
           } else {
               $result = false;
                $this->error = ERROR17." (/smallthumbnails/)";
           }
           if (!$result) { return false;}
           
        }

        return $result;
    }

    function checkFolder($folder)
    {
	   if(!is_dir($this->target_path."/".$folder)){
	      mkdir($this->target_path."/".$folder,0755);
	   }

    }

    function rotate($source,$ext,$ort){
       $this->setMemoryForImage($source);
       $new_source = $source;
      //fwrite($log_file_link,$source."\n");
      //fwrite($log_file_link,"rot=".$ort."\n");
    
       	if($this->ext=="jpg"  || $this->ext=="JPG" || $this->ext=="jpeg"  || $this->ext=="JPEG"){
         	$source_img = imagecreatefromjpeg($new_source);
   	}else if($this->ext=="png"  || $this->ext=="PNG"){
     		$source_img = imagecreatefrompng($new_source);
   	}else if($this->ext=="gif"  || $this->ext=="GIF"){
     		$source_img = imagecreatefromgif($new_source);
   	}

        if(!empty($ort)) {
    		switch($ort) {
        	case 8:
            	$source_img = imagerotate($source_img,90,0);
            	break;
        	case 3:
            	$source_img = imagerotate($source_img,180,0);
            	break;
        	case 6:
            	$source_img = imagerotate($source_img,-90,0);
            	break;
    		}
	}
       $a = imagejpeg($source_img,$new_source,100);
       //fwrite($log_file_link,"res=".$a."\n");
    
      imagedestroy($source_img);
   }

    function convert($source, $target){
        $this->setMemoryForImage($source);

       	if(strtolower($this->ext)=="jpg" || strtolower($this->ext)=="jpeg") {
            if (copy($source, $target)) {
                return true;
            } else {
                
                $this->error = ERROR18." ".$this->originals;
                return false;
            }
   	}else if(strtolower($this->ext)=="png") {
     		$source_img = imagecreatefrompng($source);
   	}else if(strtolower($this->ext)=="bmp") {
     		$source_img = imagecreatefrombmp($source);
   	}else if(strtolower($this->ext)=="gif") {
     		$source_img = imagecreatefromgif($source);
   	}
   	
       $a = imagejpeg($source_img,$target,100);
       imagedestroy($source_img);

       return true;
   }

   

   function copy_resized_image($source,$target,$height,$width,$quality,$delete_source){
            
        $thumbheight_cms = 100;
        $thumbwidth_cms = 130;
      
     if (file_exists($source))
     {
          $thumb_ratio = $width/$height;
          $thumb_cms_ratio = $thumbwidth_cms/$thumbheight_cms;


          if ($thumb_ratio < $thumb_cms_ratio){
              $newheight = $thumbheight_cms;
              $newwidth = $newheight*$thumb_ratio;
          }else {
              $newwidth = $thumbwidth_cms;
              $newheight = $newwidth/$thumb_ratio;
          }


          $thumb = imagecreatetruecolor($newwidth, $newheight);
          $source_img = imagecreatefromjpeg($source);
          //imagecopyresized($thumb, $source_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
          imagecopyresampled($thumb, $source_img, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
          
          $a = imagejpeg($thumb,$target,$quality);
          return $a;
     } else {
          return false;
     }

   }
    
   function resize_image($source,$target,$src_x,$src_y,$src_dh,$src_dw,$newheight,$newwidth,$quality,$delete_source){
                  
     if (file_exists($source))
     {
       //$this->setMemoryForImage($source);

       list($width, $height) = getimagesize($source);
       $new_source = $source;
        
       // 
       fclose(fopen($target."_tmp","a")); 
       unlink($target."_tmp");
       //
       
    
       if($this->ext=="jpg"  || $this->ext=="JPG" || $this->ext=="jpeg"  || $this->ext=="JPEG"){
    	 $can_copy = true;
       }else{
    	 $can_copy = false;
       }
       
       if($height<=$newheight && $width<=$newwidth){
           
          if($can_copy){
             //fwrite($log_file_link,"copy ".$source."->".$target." ".$newwidth."x".$newheight."\n");
             copy($source,$target);
             chmod($target, 0777);
    
             $result = 1;
          }else{
                            
       	        $smallfile = ImageCreateTrueColor($width,$height);

       	       	if($this->ext=="jpg"  || $this->ext=="JPG" || $this->ext=="jpeg"  || $this->ext=="JPEG"){
                 		$source_img = imagecreatefromjpeg($new_source);
      	      	}else if($this->ext=="png"  || $this->ext=="PNG"){
                		$source_img = imagecreatefrompng($new_source);
       	       	}else if($this->ext=="gif"  || $this->ext=="GIF"){
                 		$source_img = imagecreatefromgif($new_source);
       	       	}
       	
        imagecopyresampled($smallfile, $source_img, 0, 0, 0, 0, $width,$height, $width, $height);
   
        $result = imagejpeg($smallfile,$target,$quality);

        chmod($target, 0777);
        imagedestroy($smallfile);
        imagedestroy($source_img);

   	if($source!=$target && $delete_source){
    		unlink($source);
   	}

      }

   }else{

   	$smallfile = ImageCreateTrueColor($newwidth,$newheight);

   	if($this->ext=="jpg"  || $this->ext=="JPG" || $this->ext=="jpeg"  || $this->ext=="JPEG"){
     		$source_img = imagecreatefromjpeg($new_source);
   	}else if($this->ext=="png"  || $this->ext=="PNG"){
     		$source_img = imagecreatefrompng($new_source);
   	}else if($this->ext=="gif"  || $this->ext=="GIF"){
     		$source_img = imagecreatefromgif($new_source);
   	}
   
   	/*
   	echo $new_source."\n";
   	echo $source_img."\n";
   	echo $target."\n";
        echo $src_x.", ".$src_y.", ".$newwidth.", ".$newheight.", ".($width-$src_dw).", ".($height-$src_dh)."\n";
        */

        imagecopyresampled($smallfile, $source_img, 0, 0, $src_x, $src_y, $newwidth,$newheight, $width-$src_dw, $height-$src_dh);
   
       $result = imagejpeg($source_img,"1.jpg",$quality);
       $result = imagejpeg($smallfile,$target,$quality);

        chmod($target, 0777);
        imagedestroy($smallfile);
        imagedestroy($source_img);

   	if($source!=$target && $delete_source){
          unlink($source);
   	}
    }	
     }else{
       $result = "not exist";
     }
   
   
     $a["result"] = $result;
     $a["height"] = $newheight;
     $a["width"] = $newwidth;

     return $a;
    }
  
    function setMemoryForImage( $filename ){
          $imageInfo = getimagesize($filename);
          $MB = 1048576;  // number of bytes in 1M
          $K64 = 65536;    // number of bytes in 64K
          $TWEAKFACTOR = 5;  // Or whatever works for you
          $memoryNeeded = round( ( $imageInfo[0] * $imageInfo[1]
                                                          * $imageInfo['bits']
                                                          * $imageInfo['channels'] / 8
                                         + $K64
                                      ) * $TWEAKFACTOR
                                    );
          //ini_get('memory_limit') only works if compiled with "--enable-memory-limit" also
          //Default memory limit is 8MB so well stick with that.
          //To find out what yours is, view your php.ini file.
          
          $memoryLimit = 8 * $MB;
          //echo " currently using " . ini_get('memory_limit') . " , " . $memoryLimit . " , ";
          //$memoryLimit = ini_get('memory_limit');
          if (function_exists('memory_get_usage') && memory_get_usage() + $memoryNeeded > $memoryLimit){
             $newLimit = $memoryLimitMB + ceil( ( memory_get_usage() + $memoryNeeded - $memoryLimit ) / $MB );
             //$newLimit = $newLimit + 1;
             if ($newLimit < 20) {
                       $newLimit +=20;
             }
             /*if ($newLimit > 128) {
                       $newLimit =128;
             }*/
             ini_set( 'memory_limit', $newLimit . 'M' );
             return true;
          }else {
             return false;
          }
    }


}
