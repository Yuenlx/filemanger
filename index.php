<?php
header ( 'content-type:text/html;charset=utf-8' );
date_default_timezone_set ( 'Asia/Shanghai' );
require_once 'dir.func.php';
require_once 'file.func.php';
require_once 'common.func.php';
$path = "file";
$path = @$_REQUEST ['path'] ? $_REQUEST ['path'] : $path;
if (isset ( $_REQUEST ['path'] )) {
	$path = $_REQUEST ['path'] ? $_REQUEST ['path'] : "file";
}
$info = readDirectory ( $path );
if (! $info) {
	echo "<script>alert('没有文件或目录！！！');location.href='index.php';</script>";
}

$redirect = "index.php?path={$path}";

if (isset ( $_REQUEST ['act'] )) {
	$act = $_REQUEST ['act'];
}
if (isset ( $_REQUEST ['filename'] )) {
	$filename = $_REQUEST ['filename'];
}
if (isset ( $_REQUEST ['dirname'] )) {
	$dirname = $_REQUEST ['dirname'];
}

if (isset ( $act )) {
	if ($act == "创建文件") {
		// 调用创建文件 的方法
		$mes = createFile ( $filename, $path . "/" . $filename );
		alertMes ( $mes, $redirect );
	} elseif ($act == "showContent") {
		// 查看文件内容
		$content = file_get_contents ( $filename );
		if (strlen ( $content )) {
			// echo "<textarea readonly='readonly' rows='10' cols='100'>{$content}</textarea>";
			// 高亮显示PHP代码
			$newContent = highlight_string ( $content, true );
			// highlight_file($filename);
			$str = <<<EOF
		<table width='100%' bgcolor='pink' cellspacing='0' cellpading='1'>
			<tr>
				<td>{$newContent}</td>
			</tr>
		</table>
EOF;
			echo $str;
		} else {
			alertMes ( "文件为空！请先编辑文件再查看~", $redirect );
		}
	} elseif ($act == "editContent") {
		// echo $filename;
		$content = file_get_contents ( $filename );
		// echo $content;
		$str = <<<EOF
		<form action="index.php?act=doEdit" method="post">
			<textarea name="content" id="content">{$content}</textarea><br />
			<input type="hidden" name="filename" value="{$filename}"/>
			<input type="hidden" name="path" value="{$path}"/>		
			<input type="submit" value="保存修改"/><hr color="red"/>
		</form>
EOF;
		echo $str;
	} elseif ($act == "doEdit") {
		// 修改文件内容的操作
		$content = $_REQUEST ['content'];
		// echo $content;
		if (file_put_contents ( $filename, $content )) {
			$mes = "文件修改成功！";
		} else {
			$mes = "文件修改失败！";
		}
		alertMes ( $mes, $redirect );
	} elseif ($act == "renameFile") {
		
		$str = <<<EOF
		<form action="index.php?act=doRename" method="post">
		请填写新文件名：<input type="text" name="newname" placeholder="重命名"/>
				  <input type="submit" value="重命名" />
				  <input type="hidden" name="filename" value="{$filename}" />
		</form>
EOF;
		echo $str;
	} elseif ($act == "doRename") {
		// 实现重命名
		$newname = $_REQUEST ['newname'];
		$mes = renameFile ( $filename, $newname );
		alertMes ( $mes, $redirect );
	} elseif ($act == "delFile") {
		$mes = delFile ( $filename );
		alertMes ( $mes, $redirect );
	} elseif ($act == "downFile") {
		$mes = downFile ( $filename );
	} elseif ($act == "copyFolder") {
		$str = <<<EOF
		<form action="index.php?act=doCopyFolder" method="post">
		将文件夹复制到：<input type="text" name="dstname" placeholder="将文件夹复制到"/>
				  <input type="submit" value="复制文件夹" />
				  <input type="hidden" name="dirname" value="{$dirname}" />
				  <input type="hidden" name="path" value="{$path}" />
		</form>
EOF;
		echo $str;
	} elseif ($act == "doCopyFolder") {
		$dstname = $_REQUEST ['dstname'];
		$mes = copyFolder ( $dirname, $path . "/" . $dstname . "/" . basename ( $dirname ) );
		alertMes ( $mes, $redirect );
	} elseif ($act == "renameFolder") {
		$str=<<<EOF
		<form action="index.php?act=doRenameFolder" method="post">
		请填写新文件夹名称：<input type="text" name="newname" placeholder="请填写新文件夹名称"/>
				  <input type="submit" value="重命名文件夹" />
				  <input type="hidden" name="dirname" value="{$dirname}" />
				  <input type="hidden" name="path" value="{$path}" />
		</form>
EOF;
		echo $str;
	}elseif ($act == "doRenameFolder") {
		$newname=$_REQUEST['newname'];
// 		echo $newname."--".$dirname."--".$path;
		$mes=renameFolder($dirname,$path."/".$newname);
		alertMes($mes, $redirect);
	}elseif ($act=="cutFolder"){
		$str=<<<EOF
		<form action="index.php?act=doCutFolder" method="post">
		将文件夹剪切到：<input type="text" name="dstname" placeholder="将文件夹剪切到"/>
				  <input type="submit" value="剪切文件夹" />
				  <input type="hidden" name="dirname" value="{$dirname}" />
				  <input type="hidden" name="path" value="{$path}" />
		</form>
EOF;
		echo $str;
	}elseif ($act=="doCutFolder"){
		$dstname=$_REQUEST['dstname'];
		$mes=cutFolder($dirname,$path."/".$dstname);
		alertMes($mes, $redirect);
	}elseif($act=="创建文件夹"){
	$mes=createFolder($path."/".$dirname);
	alertMes($mes,$redirect);
    }elseif($act=="delFolder"){
	//完成删除文件夹的操作
// 	echo "文件夹被删除了";
	$mes=delFolder($dirname);
	alertMes($mes,$redirect);
	}elseif ($act=="copyFile"){
		$str=<<<EOF
		<form action="index.php?act=doCopyFile" method="post">
		将文件复制到：<input type="text" name="dstname" placeholder="将文件复制到"/>
				  <input type="submit" value="复制文件" />
				  <input type="hidden" name="filename" value="{$filename}" />
				  <input type="hidden" name="path" value="{$path}" />
		</form>
EOF;
		echo $str;
	}elseif ($act=="doCopyFile"){
		$dstname=$_REQUEST['dstname'];
 		$mes=copyFile($filename,$path."/".$dstname);
		alertMes($mes,$redirect);
	}elseif ($act=="cutFile"){
		$str=<<<EOF
		<form action="index.php?act=doCutFile" method="post">
		将文件剪切到：<input type="text" name="dstname" placeholder="将文件剪切到"/>
				  <input type="submit" value="剪切文件" />
				  <input type="hidden" name="filename" value="{$filename}" />
				  <input type="hidden" name="path" value="{$path}" />
		</form>
EOF;
		echo $str;
	}elseif ($act=="doCutFile"){
		$dstname=$_REQUEST['dstname'];
 		$mes=cutFile($filename,$path."/".$dstname);
		alertMes($mes,$redirect);
	}elseif ($act=="上传文件"){
		$fileInfo=$_FILES['myFile'];
		$mes=uploadFile($fileInfo,$path);
		alertMes($mes, $redirect);
	}
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>文件管理器</title>
<link rel="stylesheet" href="cikonss.css" />
<script src="jquery-ui/js/jquery-1.10.2.js"></script>
<script src="jquery-ui/js/jquery-ui-1.10.4.custom.js"></script>
<script src="jquery-ui/js/jquery-ui-1.10.4.custom.min.js"></script>
<link rel="stylesheet" href="jquery-ui/css/ui-lightness/jquery-ui-1.10.4.custom.css"  type="text/css"/>
<style type="text/css">

<style type="text/css">
body, p, div, ul, ol, table, dl, dd, dt {
	margin: 0;
	padding: 0;
}

textarea {
	width: 100%;
	height: 140px;
}

a {
	text-decoration: none;
}

ul, li {
	list-style: none;
	float: left;
}

#top {
	width: 100%;
	height: 78px;
	margin: 0 auto;
	background: #E2E2E2;
}

#navi a {
	display: block;
	width: 48px;
	height: 48px;
}

#tb-tdc {
	color: white;
	font-family: 黑体;
}

#main {
	margin: 0 auto;
	border: 2px solid #ABCDEF;
}

.small {
	width: 25px;
	height: 25px;
	border: 0;
}
</style>
<script type="text/javascript">
function show(dis){
	document.getElementById(dis).style.display="block";
	}
	

function showDetail(t,filename){
	$("#showImg").attr("src",filename);
	$("#showDetail").dialog(
			{
				height:"auto",
				width:"auto",
				position:{my:"center",at:"center",collision:"fit"},
				modal:false,
				draggable:true,
				resizable:true,
				title:t,
				show:"slide",
				hide:"puff"
				}
			);
}

function delFile(filename){
	if(window.confirm("您确定要删除文件 "+filename+" 吗？删除后无法恢复哦！")){
		location.href="index.php?act=delFile&filename="+filename;
		}
	
}

function goBack(back){
	location.href="index.php?path="+back;
}

function notReady(mess){
	window.alert(mess);
}


function delFolder(dirname,path){
	if(window.confirm("您确定要删除嘛?删除之后无法恢复哟!!!")){
		location.href="index.php?act=delFolder&dirname="+dirname+"&path="+path;
	}
}
</script>
</head>
<body>
	<div id="showDetail" style="display: none">
		<img src="" id="showImg" alt="" />
	</div>
	<h1>文件管理器 </h1>
	<div id="top">
		<ul id="navi">
			<li><a href="index.php" title="主目录"><span
					style="margin-left: 8px; margin-top: 0px; top: 4px;"
					class="icon icon-small icon-square"><span class="icon-home"></span></span></a></li>

			<li><a href="#" onclick="show('createFile')" title="新建文件"><span
					style="margin-left: 8px; margin-top: 0px; top: 4px;"
					class="icon icon-small icon-square"><span class="icon-file"></span></span></a></li>

			<li><a href="#" onclick="show('createFolder')" title="新建文件夹"><span
					style="margin-left: 8px; margin-top: 0px; top: 4px;"
					class="icon icon-small icon-square"><span class="icon-folder"></span></span></a></li>

			<li><a href="#" onclick="show('uploadFile')" title="上传文件"><span
					style="margin-left: 8px; margin-top: 0px; top: 4px;"
					class="icon icon-small icon-square"><span class="icon-upload"></span></span></a></li>

					<?php $back=($path=="file")?"file":dirname($path);?>
			<li><a href="#" title="返回上级目录"
				onclick="goBack('<?php echo $back;?>')"><span
					style="margin-left: 8px; margin-top: 0px; top: 4px;"
					class="icon icon-small icon-square"><span class="icon-arrowLeft"></span></span></a></li>
		</ul>
	</div>
	<form action="index.php" method="post" enctype="multipart/form-data">
		<table width="100%" border="1" cellpadding="5" cellspacing="0"
			bgcolor="#9cf" align="center">

			<tr id="createFile" style="display: none;">
				<td>请输入文件名称</td>
				<td><input type="text" name="filename" /> <input type="hidden"
					name="path" value="<?php echo $path;?>" /> <input type="submit"
					name="act" value="创建文件" /></td>
			</tr>

			<tr id="createFolder" style="display: none;">
				<td>请输入文件夹名称</td>
				<td><input type="text" name="dirname" /> <input type="hidden"
					name="path" value="<?php echo $path;?>" /> <input type="submit"
					name="act" value="创建文件夹" /></td>
			</tr>

			<tr id="uploadFile" style="display: none;">
				<td>请选择要上传的文件</td>
				<td>
				<input type="file" name="myFile" /> 
				<input type="submit" name="act" value="上传文件" /></td>
			</tr>


			<tr>
				<td id="tb-tdc">编号</td>
				<td id="tb-tdc">名称</td>
				<td id="tb-tdc">类型</td>
				<td id="tb-tdc">大小</td>
				<td id="tb-tdc">可读</td>
				<td id="tb-tdc">可写</td>
				<td id="tb-tdc">可执行</td>
				<td id="tb-tdc">创建时间</td>
				<td id="tb-tdc">修改时间</td>
				<td id="tb-tdc">访问时间</td>
				<td id="tb-tdc">操作</td>
			</tr>
			<?php
			if (@$info ['file']) {
				$i = 1;
				foreach ( $info ['file'] as $val ) {
					$p = $path . "/" . $val;
					?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $val;?></td>
				<td><?php $src=filetype($path."/".$val)=="file"? "file_ico.png":"folder_ico.png";?><img
					class="small" src="images/<?php echo $src;?>" title="文件" alt="文件" /></td>
				<td><?php echo transByte(filesize($path."/".$val));?></td>
				<td><?php $src=is_readable($path."/".$val)?"correct.png":"error.png";?><img
					class="small" src="images/<?php echo $src;?>" alt="" /></td>
				<td><?php $src=is_writeable($path."/".$val)?"correct.png":"error.png";?><img
					class="small" src="images/<?php echo $src;?>" alt="" /></td>
				<td><?php $src=is_executable($path."/".$val)?"correct.png":"error.png";?><img
					class="small" src="images/<?php echo $src;?>" alt="" /></td>
				<td><?php echo date("Y-m-d H:i:s",filectime($path."/".$val));?></td>
				<td><?php echo date("Y-m-d H:i:s",filemtime($path."/".$val));?></td>
				<td><?php echo date("Y-m-d H:i:s",fileatime($path."/".$val));?></td>

				<td>
				<?php
					// 取出文件扩展名
					@$ext = strtolower ( end ( explode ( ".", $val ) ) );
					$imageExt = array (
							"gif",
							"jpg",
							"jpeg",
							"png" 
					);
					if (in_array ( $ext, $imageExt )) {
						?>
					<a href="#" onclick="showDetail('<?php echo $val;?>','<?php echo $p;?>')"><img class="small" src="images/show.png" alt="" title="查看" /></a>|
				<?php
					} else {
						?>
					<a href="index.php?act=showContent&path=<?php echo $path;?>&filename=<?php echo $path."/".$val;?>"><img class="small" src="images/show.png" alt="" title="查看" /></a>| 
				<?php }?>
					<a href="index.php?act=editContent&path=<?php echo $path;?>&filename=<?php echo $path."/".$val;?>"><img class="small" src="images/edit.png" alt="" title="修改" /></a>| 
					<a href="index.php?act=renameFile&path=<?php echo $path;?>&filename=<?php echo $p;?>"><img class="small" src="images/rename.png" alt="" title="重命名" /></a>| 
					<a href="index.php?act=copyFile&path=<?php echo $path;?>&filename=<?php echo $p;?>"><img class="small" src="images/copy.png" alt="" title="复制" /></a>|
					<a href="index.php?act=cutFile&path=<?php echo $path;?>&filename=<?php echo $p;?>"><img class="small" src="images/cut.png" alt="" title="剪切" /></a>| 
					<a href="#" onclick="delFile('<?php echo $p;?>')"><img class="small" src="images/delete.png" alt="" title="删除" /></a>| 
					<a href="index.php?act=downFile&path=<?php echo $path;?>&filename=<?php echo $p;?>"><img class="small" src="images/download.png" alt="" title="下载" /></a>
				</td>
			</tr>	
			
			
			
			<?php
					$i ++;
				}
			}
			?>	
			
			
<!-- 读取目录操作 -->			
		<?php
		if (@$info ['dir']) {
			@$i = ($i == null) ? 1 : $i;
			foreach ( $info ['dir'] as $val ) {
				$p = $path . "/" . $val;
				?>
			<tr>
				<td><?php echo $i;?></td>
				<td><?php echo $val;?></td>
				<td><?php $src=filetype($path."/".$val)=="file"? "file_ico.png":"folder_ico.png";?><img
					class="small" src="images/<?php echo $src;?>" title="文件" alt="文件" /></td>
				<td><?php $sum=0;echo transByte(dirSize($p));?></td>
				<td><?php $src=is_readable($path."/".$val)?"correct.png":"error.png";?><img
					class="small" src="images/<?php echo $src;?>" alt="" /></td>
				<td><?php $src=is_writeable($path."/".$val)?"correct.png":"error.png";?><img
					class="small" src="images/<?php echo $src;?>" alt="" /></td>
				<td><?php $src=is_executable($path."/".$val)?"correct.png":"error.png";?><img
					class="small" src="images/<?php echo $src;?>" alt="" /></td>
				<td><?php echo date("Y-m-d H:i:s",filectime($path."/".$val));?></td>
				<td><?php echo date("Y-m-d H:i:s",filemtime($path."/".$val));?></td>
				<td><?php echo date("Y-m-d H:i:s",fileatime($path."/".$val));?></td>

				<td>
					<a href="index.php?path=<?php echo $p;?>"><img class="small" src="images/show.png" alt="" title="查看" /></a>| 
					<a href="#" onclick="notReady('暂不提供文件夹修改功能！')"><img class="small" src="images/edit.png" alt="" title="修改" /></a>| 
					<a href="index.php?act=renameFolder&path=<?php echo $path;?>&dirname=<?php echo $p;?>"><img class="small" src="images/rename.png" alt="" title="重命名" /></a>| 
					<a href="index.php?act=copyFolder&path=<?php echo $path;?>&dirname=<?php echo $p;?>"><img class="small" src="images/copy.png" alt="" title="复制" /></a>| 
					<a href="index.php?act=cutFolder&path=<?php echo $path;?>&dirname=<?php echo $p;?>"><img class="small" src="images/cut.png" alt="" title="剪切" /></a>|
					<a href="#"  onclick="delFolder('<?php echo $p;?>','<?php echo $path;?>')"><img class="small" src="images/delete.png"  alt="" title="删除"/></a>| 
					<a href="#" onclick="notReady('暂不提供文件夹下载功能！')"><img class="small" src="images/download.png" alt="" title="下载" /></a>
				</td>
			</tr>	
			<?php
				$i ++;
			}
		}
		?>		
			
			
			
			
			
			
			
				
			
		</table>
	</form>
</body>
</html>