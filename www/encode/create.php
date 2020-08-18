<?php
/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../inc/config.inc' ;


$encode = $_POST['encode'] ;
if ( ! $encode )
	exit ( 'No encode' ) ;


foreach ( $encode as $k => $v )
	if ( empty ( $v ) )
		exit ( 'No ' . $k ) ;
	else
		$$k = $v ;



$token = $_POST['token'] ;
if ( ! $token )
	exit ( 'No token' ) ;


/*
 * 인코딩 생성 API : encodeCreate
 */
$re = $AUTH -> encodeCreate ( $token , $name , $width , $height ) ;
if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( isset ( $re -> Result ) )
		echo json_encode ( $re -> Result , JSON_UNESCAPED_UNICODE ) ;
	else
		echo 'Encode create error' ;
else
	echo 'Encode create error' ;
exit ;
