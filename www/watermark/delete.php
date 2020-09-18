<?php
/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../inc/config.inc' ;

$deleteArray = $_POST['watermarks'] ;
if ( empty ( $deleteArray ) )
	exit ( 'No watermarks' ) ;

$token = $_POST['token'] ;
if ( ! $token )
	exit ( 'No token' ) ;

/*
 * 워터마크 삭제 API : watermark delete
 */
$re = $AUTH -> watermarkDelete ( $token , $deleteArray ) ;
if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( isset ( $re -> Result ) )
		echo json_encode ( $re -> Result , JSON_UNESCAPED_UNICODE ) ;
	else
		echo 'Blacklist delete error' ;
else
	echo 'Blacklist delete error' ;
exit ;