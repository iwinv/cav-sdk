<?php
/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../../inc/config.inc' ;

// 파라미터 확인
foreach ( array ( 'pwd' , 'url' , 'name' , 'fileKey' ) as $v )
	if ( empty ( $_POST[$v] ) )
		exit ( 'No ' . $v ) ;

$token = explode ( '/' , parse_url ( $_POST['url'] ) ['path'] ) ;
$token = array_pop ( $token ) ;

// 암호화 후 응답
$token = json_decode ( base64_decode ( $token ) , true ) ['token'] ;
$enc = $AUTH::encrypt ( $_POST['fileKey'] , $_POST['pwd'] ) ;

$file = $AUTH::curl
(
	$_POST['url']
		, array ( 'Content-Type' => 'application/json' )
		, 'POST'
		, array ( 'password' => $enc )
) ;
$fail = json_decode ( $file ) ;

if ( $fail )
{
	echo json_encode ( $fail , JSON_UNESCAPED_UNICODE ) ;
}
else
{
	header ( 'Content-Type: application/force-download' ) ;
	header ( 'Content-Disposition: attachment; filename="' . $_POST['name'] . '"' ) ;
	header ( 'Content-Transfer-Encoding: binary' ) ;
	header ( 'Pragma: no-cache' ) ;
	header ( 'Content-Length: ' . strlen ( $file ) ) ;

	echo $file;
}