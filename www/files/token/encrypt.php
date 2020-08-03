<?php

/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../../inc/config.inc' ;

// 파라미터 확인
foreach ( array ( 'pwd' , 'fileKey' ) as $v )
	if ( empty ( $_POST[$v] ) )
		exit ( 'No ' . $v ) ;

// 암호화 후 응답
$token = json_decode ( base64_decode ( $_POST['token'] ) , true ) ['token'] ;
$enc = $AUTH::encrypt ( $token , $_POST['pwd'] ) ;

if ( $enc )
	echo $enc ;
else
	exit ( 'Fail encrypt' ) ;
