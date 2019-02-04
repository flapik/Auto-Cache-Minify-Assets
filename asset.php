<?php
$p=explode('?',substr($_SERVER['REQUEST_URI'],1));
$q=(count($p)>1)?explode('&',strtolower($p[1])):[];

header('Content-Type: '.(substr(strrchr($p[0],'.'),1)==='js'?'application/javascript':'text/css'));

//Auto Cache
$lm=filemtime($p[0]);//lastModified
$ef=md5_file($p[0]);//etagFile
$eh=(isset($_SERVER['HTTP_IF_NONE_MATCH'])?trim($_SERVER['HTTP_IF_NONE_MATCH']):false);//etagHeader
header('Last-Modified: '.gmdate('D, d M Y H:i:s',$lm).' GMT');
header('Etag: '.$ef);
header('Cache-Control: public');
if(@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])==$lm||$eh==$ef) {
	header('HTTP/1.1 304 Not Modified');
	exit;
}

//Content
$b=file_get_contents($p[0]);//Buffer
if(in_array('minify',$q)===true) {
	$regex=['\Q/*\E[\s\S]+?\Q*/\E','(?:http|ftp)s?://(*SKIP)(*FAIL)|//.+','^\s+|\R\s*'];//Multiline comment,Singleline comment,Whitespace
	foreach($regex as $k=>$e) {
		$b=preg_replace('~'.$e.'~m','',$b);
	}
}
echo $b;
exit;