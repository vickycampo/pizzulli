<?php
function listdirsBackup($dir)
{
       
       $i = 0;
       $folders = Array();
       $d = opendir($dir);
      	
       while ($file = readdir($d))
       {       
               if ($file == '.' || $file == '..') continue;
               if (is_dir($dir.'/'.$file))
               {    
		    $folders[$i++] = $file;
		    
                    continue;
               }
		}
	return 	$folders;
}

function copyFiles($newdata,$olddata,$folder){
       $d = opendir($olddata."/".$folder);      	
       while ($file = readdir($d))
       {        
         if ($file == '.' || $file == '..') continue;
         if (is_file($olddata.'/'.$folder.'/'.$file))
         {   
	   echo $olddata."/".$folder."/".$file."  =>  ".$newdata."/".$folder."/".$file."<br>";
           copy($olddata."/".$folder."/".$file,$newdata."/".$folder."/".$file);
         }
       }
}
function copydata($dir,$bk){
   full_copy($dir,$bk);
}
function full_copy( $source, $target )
    {
        if ( is_dir( $source ) )
        {
            @mkdir( $target );
           
            $d = dir( $source );
           
            while ( FALSE !== ( $entry = $d->read() ) )
            {
                if ( $entry == '.' || $entry == '..' )
                {
                    continue;
                }
               
                $Entry = $source . '/' . $entry;           
                if ( is_dir( $Entry ) )
                {
                    full_copy( $Entry, $target . '/' . $entry );
                    continue;
                }
                $zu = array_splice(explode("/",$target),-1,1);

                //if ($zu[0]=="PRESS" || $zu[0]=="images" || $zu[0]=="smallimages" || $zu[0]=="thumbnails" || $zu[0]=="smallthumbnails" || $zu[0]=="thumbnails140" || $zu[0]=="pdf"){
                   copy( $Entry, $target . '/' . $entry );
                //}
            }
           
            $d->close();
        }else
        {
            copy( $source, $target );
             
        }
    }


?>