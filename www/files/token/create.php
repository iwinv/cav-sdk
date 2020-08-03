<?php
/*
 * ===========================================================================
 * 파일 다운로드 토큰 create API 예제
 * ===========================================================================
 *
 * 파일 다운로드 토큰 create : "download token create" API통해서 파일 다운로드 주소를 불러옵니다.
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

$fileKey = $_POST['fileKey'] ;
if ( ! $fileKey )
	exit ( 'No file key' ) ;

$token = $AUTH -> getToken();

/*
* 파일 다운로드 토큰 create API : download token create
*/
$re = $AUTH -> downloadTokenCreate ( $token , $fileKey ) ;

if ( $re )
	if ( isset ( $re -> Error ) )
		echo 'Download token create error' ;
	else if ( isset ( $re -> downloadToken ) )
		echo json_encode ( $re -> Result ) ;
	else
		echo 'Download token create error' ;
else
	echo 'Download token create error' ;
exit ;
?>
