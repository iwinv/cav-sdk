<?php
include_once '../../inc/config.inc' ;

$AUTH -> getToken () ;

$filesKey = $_GET['filesKey'] ;
if ( ! $filesKey )
	exit ( 'No files key' ) ;

$token = $AUTH -> token ;
if ( ! $token )
	exit ( 'No token' ) ;




$title = '인코딩 영상 플레이어';

include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;


/*
* 인코딩 플레이어 url API : encoding video url select
*/
$re = $AUTH -> encodingVideoSelect ( $token , $filesKey , 'video' ) ;
if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( ! isset ( $re -> Result ) )
		echo 'Encoding files create error' ;
else
	echo 'Encoding files create error' ;
exit ;


echo $re;