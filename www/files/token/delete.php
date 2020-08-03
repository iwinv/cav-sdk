<?php
/*
 * ===========================================================================
 * 파일 다운로드 토큰 delete API 예제
 * ===========================================================================
 *
 * 파일 다운로드 토큰 delete : "download token delete" API통해서 파일 다운로드 주소를 불러옵니다.
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

if ( ! $_POST['downloadTokens'] )
	exit ( 'No tokens' ) ;

if ( ! $_POST['fileKey'] )
	exit ( 'No fileKey' ) ;

$token = $AUTH -> getToken();

$re = $AUTH -> downloadTokenDelete ( $token , $_POST['fileKey'] , $_POST['downloadTokens'] ) ;

if ( $re )
	if ( isset ( $re -> Error ) )
		echo 'Download token delelte error' ;
	else if ( isset ( $re -> Result ) )
		echo json_encode($re -> Result) ;
	else
		echo 'Download token delelte error' ;
else
	echo 'Download token delelte error' ;
exit ;
?>
