<?php
/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../inc/config.inc' ;

$target = $_POST['target'] ;
if ( empty ( $target ) )
	exit ( 'No target' ) ;

$token = $_POST['token'] ;
if ( ! $token )
	exit ( 'No token' ) ;

$branch = $_POST['branch'] ;
if ( ! $branch )
	exit ( 'No branch' ) ;

/*
 * 블랙리스트 생성 API : blackCreate
 */
$re = $AUTH -> blackCreate ( $token , $branch , $target ) ;
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
