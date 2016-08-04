<?php
//打开指定目录
header("content-type:text/html;charset='utf-8'");
function readDirectory($path) {
 $handle = opendir ( $path );
 while ( ($item = readdir ( $handle )) !== false ) {
 //.和..这两个特殊目录
  if ($item != "." && $item != "..") {
   if (is_file ( $path . "/" . $item )) {
    $arr ['file'] [] = $item;
   }
   if (is_dir ( $path . "/" . $item )) {
    $arr ['dir'] [] = $item;
   }
  }
 }
 closedir ( $handle );
 return $arr;
}
$arr=readDirectory("file");
print_r($arr);
 ?>

 
 
 
 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>游戏体育在线-gsonline</title>
<!-- <link rel="shortcut icon" href="favicon.ico" type="image/x-icon"/> -->
<link rel="icon" href="favicon.png" type=" image/png" />
</head>
<body>
</body>
</html>