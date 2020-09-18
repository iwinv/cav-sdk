<?php
include_once '../../inc/config.inc' ;


$AUTH -> getToken () ;

$fileKey = $_POST['fileKey'] ;
if ( ! $fileKey )
	exit ( 'No file key' ) ;

$encoding = $_POST['encoding'] ;
if ( ! $encoding )
	exit ( 'No encoding' ) ;

$watermark = $_POST['watermark'] ;
$position = $_POST['position'] ;
if ( ! empty ( $watermark ) ^ ! empty ( $position ) )
	exit ( 'Invalid parameter' ) ;

$token = $AUTH -> token ;
if ( ! $token )
	exit ( 'No token' ) ;

/*
* 파일 인코딩 요청 API : encoding files create
*/
$re = $AUTH->encodingFilesCreate ( $token , $fileKey , $encoding , $watermark , $position ) ;
if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( isset ( $re -> Result ) )
		echo json_encode ( $re -> Result , JSON_UNESCAPED_UNICODE ) ;
	else
		echo 'Encoding files create error' ;
else
	echo 'Encoding files create error' ;
exit ;