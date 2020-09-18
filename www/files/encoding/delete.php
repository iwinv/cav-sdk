<?php
include_once '../../inc/config.inc' ;

$AUTH -> getToken () ;

$keys = $_POST['keys'] ;
if ( ! $keys )
	exit ( 'No keys' ) ;

$token = $AUTH -> token ;
if ( ! $token )
	exit ( 'No token' ) ;

/*
* 인코딩 파일 삭제 API : encoding files delete
*/
$re = $AUTH->encodingFilesDelete ( $token , $keys ) ;
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