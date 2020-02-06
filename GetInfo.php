<?php

ini_set('display_errors', true);error_reporting(E_ALL ^ E_NOTICE);ini_set ('log_errors', true);

/**
 * BanckEnd data manager
 */
$getInfo = new GetInfo ();
if ( ( $getInfo->getMethod() == 'GET') || ( $getInfo->getMethod() == 'get'))
{
     $getInfo->getInfo();
}
else
{
     $getInfo->getErrorMessage();
}

class GetInfo
{
     private $method;
     private $request;
     private $input;

     private $inforXML;
     private $path;
     private $folderPath;
     private $sortedInfo = [];
     private $callerUrl;
     function __construct( )
     {
          // security Check
          $this->method = $_SERVER['REQUEST_METHOD'];
          $this->request = $_SERVER['REQUEST_URI'];
          $this->input = json_decode(file_get_contents('php://input'),true);
          $this->callerUrl = $_SERVER['HTTP_REFERER'];

          // header('Content-Type: application/json');
          // print_r(json_encode($_SERVER));

          $this->path = "cms/data/";
          $this->folderPath = "cms/data_cms/";
          $this->infoxml = simplexml_load_file($this -> path . "info.xml" );
     }
     private function analizeInfo ()
     {
          foreach ( $this->infoxml as $key => $section )
          {
               if ( $section->attributes()['parent_id'] == -1 )
               {
                    $id = intval($section->attributes()['id']);
                    $parent_id = $id;
                    foreach ( $section->attributes() as $key => $value )
                    {
                         $this->sortedInfo['section'][$id][$key] = $this->getValue( $section->attributes()[$key][0] );
                    }
                    //CHECK IF THERE IS A FOLDER THAT CORRESPONDS FOR THIS SUBSECTION
                    $dir = $this->folderPath . $this->sortedInfo['section'][$id]['name'];
                    $this->sortedInfo['section'][$id]['dir'] = $dir;
                    $this->sortedInfo['section'][$id]['files'] = $this->getFiles ( $dir );


                    foreach ( $section as $single )
                    {
                         if ( isset ( $single->attributes()['vimeo_id'] ) )
                         {
                              $id = (string)$single->attributes()['vimeo_id'][0];
                         }
                         else
                         {
                              $id = intval($single->attributes()['id']);
                         }

                         foreach ( $single->attributes() as $key => $value )
                         {
                              $this->sortedInfo['sub-section'][$parent_id][$id][$key] = $this->getValue ( $single->attributes()[$key][0] );
                         }
                         $subdir = $dir . '/' . $this->sortedInfo['sub-section'][$parent_id][$id]['name'];
                         $this->sortedInfo['sub-section'][$parent_id][$id]['dir'] = $subdir;
                         $this->sortedInfo['sub-section'][$parent_id][$id]['files'] = $this->getFiles ( $subdir );

                         $this->sortedInfo['sub-section'][$parent_id][$id]['directoryList'] = $this->getDir( $subdir );
                         foreach ($single as $element )
                         {

                              if ( isset ( $element->attributes()['id'] ))
                              {
                                   $kid_id = intval($element->attributes()['id']);
                              }
                              else
                              {
                                   $kid_id = uniqid ( );
                              }

                              foreach ( $element->attributes() as $key => $value )
                              {

                                   $this->sortedInfo['element'][$parent_id][$id][$kid_id][$key] = $this->getValue ( $element->attributes()[$key][0] );
                                   //get the description directory files
                                   if ( is_dir ( $subdir . '/DESCRIPTION' ) )
                                   {
                                        $this->sortedInfo['element'][$parent_id][$id][$kid_id]['files'] = $this->getFiles( $subdir . '/DESCRIPTION' );
                                   }
                              }


                         }

                    }
               }
          }

     }
     public function getMethod ()
     {
          return ($this->method) ;
     }
     public function getInfo ()
     {
          $this->analizeInfo();
          header('Content-Type: application/json');
          print_r(json_encode($this->sortedInfo));
     }
     public function getErrorMessage ()
     {
          $error['type'] = 'error';
          $error['message'] = 'Invalid request method';
          header('Content-Type: application/json');
          print_r(json_encode($error));
     }
     private function getValue ( $element )
     {
          $temp = (array)$element;
          $temp_01 = $temp[0];
          return ( $temp_01 );
     }
     private function getFiles ( $dir )
     {
          //get the folders and files required
          unset ( $files );
          if ( is_dir ( $dir ) )
          {
               $files = scandir($dir);
               foreach ( $files as $i => $file )
               {
                    if (( $file != '.' ) && ( $file != '..' ) && ( $file != '...' ))
                    {
                         $fullPath = $dir .'/'. $file;
                         if ( is_file ( $fullPath ) )
                         {
                              if ( strpos($file , '.json' ) > -1 )
                              {
                                   $fileContent = file_get_contents( $fullPath );
                                   $filesJSON[$file] = json_decode($fileContent);
                              }
                              else if ( strpos($file , '.xml' ) > -1 )
                              {
                                   $fileContent = simplexml_load_file( $fullPath );
                                   $json = json_encode($fileContent);
                                   $filesJSON[$file] = json_decode($json,TRUE);
                              }

                         }
                    }
               }
          }
          else
          {
               $files = [];
          }

          return ( $filesJSON );

     }
     private function getDir ( $dir )
     {
          $dirs = [];
          unset ( $files );
          if ( is_dir ( $dir ) )
          {
               $files = scandir($dir);
               foreach ( $files as $i => $file )
               {
                    if (( $file != '.' ) && ( $file != '..' ) && ( $file != '...' ))
                    {
                         $fullPath = $dir .'/'. $file;

                         if ( is_dir ( $fullPath ) )
                         {

                              $dirs[] = $fullPath;
                         }
                    }
               }
          }
          return ( $dirs );
     }
}
