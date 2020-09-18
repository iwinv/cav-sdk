<?php
include_once '../../inc/config.inc' ;
$AUTH -> getToken () ;


$fileKey = $_POST['fileKey'] ;
if ( ! $fileKey )
	exit ( 'No encodeKey' ) ;

$encodeKey = $_POST['key'] ;
if ( ! $encodeKey )
	exit ( 'No encodeKey' ) ;

$type = $_POST['type'] ;
if ( ! $type )
	exit ( 'No type' ) ;

$token = $AUTH -> token ;
if ( ! $token )
	exit ( 'No token' ) ;

/*
* 디폴트 플레이 설정 API : encoding file defaultplay update
*/
$re = $AUTH->encodingFileDefaultplayUpdate ( $token , $fileKey , $encodeKey , $type ) ;
if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( isset ( $re -> Result ) )
		echo json_encode ( $re -> Result , JSON_UNESCAPED_UNICODE ) ;
	else
		echo 'Encoding file defaultplay update error' ;
else
	echo 'Encoding file defaultplay update error' ;
exit ;