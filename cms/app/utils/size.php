<?php
    $backup_count = intval($_GET["backup_count"]);
    $f = '../../data_cms/';
    $io = popen ( '/usr/bin/du -shb ' . $f, 'r' );
    $size = fgets ( $io, 4096);
    
    $size = substr ( $size, 0, strpos ( $size, "\t" ) );
    pclose ( $io );
    //echo 'Directory: ' . $f . ' => Size: ' . $size;
    echo json_encode(["size" => isa_convert_bytes_to_specified($size*($backup_count+1), 'G',2). " G"]);
    //echo json_encode(["size" => formatBytes($size*2,2)]);
    //echo json_encode(["size" => $size*2]);


function isa_convert_bytes_to_specified($bytes, $to, $decimal_places = 1) {
    $formulas = array(
        'K' => number_format($bytes / 1024, $decimal_places),
        'M' => number_format($bytes / 1048576, $decimal_places),
        'G' => number_format($bytes / 1073741824, $decimal_places)
    );
    return isset($formulas[$to]) ? $formulas[$to] : 0;
}

function formatBytes($bytes, $precision = 2) { 
    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

    $bytes = max($bytes, 0); 
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
    $pow = min($pow, count($units) - 1); 

    // Uncomment one of the following alternatives
    // $bytes /= pow(1024, $pow);
    // $bytes /= (1 << (10 * $pow)); 

    return round($bytes, $precision) . ' ' . $units[$pow]; 
} 
?>