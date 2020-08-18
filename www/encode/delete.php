<?php
/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../inc/config.inc' ;


$deleteArray = $_POST['encodes'] ;
if ( empty ( $deleteArray ) )
	exit ( 'No encodes' ) ;

$token = $_POST['token'] ;
if ( ! $token )
	exit ( 'No token' ) ;


/*
 * 인코딩 삭제 API : encode delete
 */
$re = $AUTH -> encodeDelete ( $token , $deleteArray ) ;
if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( isset ( $re -> Result ) )
		echo json_encode ( $re -> Result , JSON_UNESCAPED_UNICODE ) ;
	else
		echo 'Encode delete error' ;
else
	echo 'Encode delete error' ;
exit ;