<?php
/*
 * ===========================================================================
 * 다운로드 토큰 수정 API 예제
 * ===========================================================================
 *
 * 다운로드 토큰 수정 : "download token update" API를 통해서 다운로드 토큰을 수정합니다.
 *
 * ---------------------------------------------------------------------------
 * 작성자: 리성림 <chenglin@smileserv.com>
 * 작성일: 2018년 06월 07일
 * ===========================================================================
 */


/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../../inc/config.inc' ;

$token = $_POST['token'] ;
if ( ! $token )
	exit ( 'No token' ) ;

$type = $_POST['type'] ;
if ( ! $type )
	exit ( 'No type' ) ;

$value = isset ( $_POST['value'] ) ? $_POST['value'] : NULL ;
if ( in_array ( $_POST['type'] , array ( 'count' , 'time' , 'password' ) ) && empty ( $value ) )
	exit ( 'No token value' ) ;

$filesKey = $_POST['files'] ;
if ( ! $filesKey )
	exit ( 'No files key' ) ;


/*
* 다운로드 토큰 수정 API : download token update
*/
$re = $AUTH -> downloadTokenUpdate ( $token , $filesKey , $type , $value ) ;

if ( $re )
	if ( isset ( $re -> Error ) )
		echo json_encode ( $re -> RequestID . ' : ' . $re -> Message , JSON_UNESCAPED_UNICODE ) ;
	else if ( isset ( $re -> Result ) )
		echo json_encode ( $re -> Result , JSON_UNESCAPED_UNICODE ) ;
	else
		echo 'Update download token error' ;
else
	echo 'Update download token error' ;
exit ;
?>
