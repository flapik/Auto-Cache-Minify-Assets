<?php
if(file_exists($_SERVER['QUERY_STRING'])){
	$p=explode('?',substr($_SERVER['REQUEST_URI'],1));
	$q=explode('&',$p[1]);
	//Auto Cache
	$lastModified=filemtime($p[0]);
	$etagFile=md5_file($p[0]);
	$ifModifiedSince=(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])?$_SERVER['HTTP_IF_MODIFIED_SINCE']:false);
	$etagHeader=(isset($_SERVER['HTTP_IF_NONE_MATCH'])?trim($_SERVER['HTTP_IF_NONE_MATCH']):false);
	header('Last-Modified: '.gmdate('D, d M Y H:i:s',$lastModified).' GMT');
	header('Etag: '.$etagFile);
	header('Cache-Control: public');
	if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lastModified||$etagHeader==$etagFile) {
		header('HTTP/1.1 304 Not Modified');
		exit;
	}
	//Content
	$buffer=file_get_contents($p[0]);
	if(in_array('minify',$q)===true) {
		$regex=['\Q/*\E[\s\S]+?\Q*/\E','(?:http|ftp)s?://(*SKIP)(*FAIL)|//.+','^\s+|\R\s*'];//Multiline comment,Singleline comment,Whitespace
		foreach($regex as $k=>$e) {
			$buffer = preg_replace('~'.$e.'~m', '', $buffer);
		}
	}
	echo $buffer;
	exit;
}