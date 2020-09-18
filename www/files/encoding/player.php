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
* 인코딩 video select API 플레이어 포함 : encoding video select
*/
$re = $AUTH -> encodingVideoSelect ( $token , $filesKey , 'token' ) ;
if ( ! $re )
	exit ( 'Encoding video select error' ) ;

echo $re;