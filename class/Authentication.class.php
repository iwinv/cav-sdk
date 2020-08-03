<?php
/*
 * ===========================================================================
 * Authentication 클래스 예제
 * ===========================================================================
 *
 * API서버로 접근 내용을 메소드로 구성해서 include해서 사용하면 됩니다.
 *
 * ---------------------------------------------------------------------------
 * 작성자: 리성림 <chenglin@smileserv.com>
 * 작성일: 2018년 04월 01일
 * ===========================================================================
 */

class Authentication
{
	/**
	 * @var string accesskey ID
	 */
	private static $accesskeyId ;
	/**
	 * @var string accesskey 비번
	 */
	private static $accesskeySecret ;
	/**
	 * @var string API서버 도메인
	 */
	private static $apiDomain ;
	/**
	 * @var string Token 요청주소
	 */
	public static $authenticationtUrl ;
	/**
	 * @var string 파일 관련 요청주소 ( 업로드 , 검색 , 수정 , 삭제 )
	 */
	public static $filesUrl ;
	/**
	 * @var string 폴더관련 요청주소 ( 생성 , 검색 )
	 */
	public static $foldersUrl ;
	/**
	 * @var string 블랙리스트 설정 주소
	 */
	public static $blackUrl ;
	/**
	 * @var string 다운로드 토큰 요청주소
	 */
	public static $downloadTokenUrl ;
	/**
	 * @var string 다운로드 주소
	 */
	public static $downloadUrl ;
	/**
	 * @var string 폴더키
	 */
	public $folderKey ;
	/**
	 * @var boolean 토큰 유효기간 초과할때 다시 토큰을 요청했는지
	 */
	private $countOvertime ;
	/**
	 * @var string Token
	 */
	public $token ;


	/**
	 * 클래스 생성자
	 * class 기준정보를 /inc/setting.php 파일에서 세팅한다
	 * @param array $setting 기준정보 ( accesskeyId : accesskey ID; accesskeySecret : accesskey 비번; apiDomain : API서버 도메인; folderKey : 폴더키; )
	 */
	function __construct ( $setting )
	{
		self::$accesskeyId = $setting['accesskeyId'] ;
		self::$accesskeySecret = $setting['accesskeySecret'] ;
		$domain = $setting['apiDomain'] ;
		if ( ! preg_match ( "/\/$/" , $domain ) )
			$domain .= '/' ;
		self::$apiDomain = $domain . $setting['version'] . '/' ;
		$this -> folderKey = $setting['folderKey'] ;

		self::$authenticationtUrl = self::$apiDomain . 'authorization' . '/' ;
		self::$filesUrl = self::$apiDomain . 'files' . '/' ;
		self::$foldersUrl = self::$apiDomain . 'folders' . '/' ;
		self::$blackUrl = self::$apiDomain . 'black' . '/' ;
		self::$downloadTokenUrl = self::$apiDomain . 'token' . '/' ;
		self::$downloadUrl = self::$apiDomain . 'download' . '/' ;

		$this -> countOvertime = FALSE ;
	}


	/**
	 * curl 방식으로 api 서버에 접근하기
	 * @param string $url api주소
	 * @param array $headers 헤더정보
	 * @param string $action HTTP ACTION ( GET , POST , PUT , DELETE )
	 * @return object return정보
	 */
	public static function curl ( $url , $headers , $action , $postData = array () )
	{
		$curl = curl_init () ;
		curl_setopt ( $curl , CURLOPT_URL , $url ) ;
		curl_setopt ( $curl , CURLOPT_HTTPHEADER , $headers ) ;
		curl_setopt ( $curl , CURLOPT_RETURNTRANSFER , true ) ;
		curl_setopt ( $curl , CURLOPT_BINARYTRANSFER , true ) ;
		curl_setopt ( $curl , CURLOPT_REFERER , $_SERVER['SERVER_NAME'] ) ; //client 서버 도메인
		if ( $action == 'POST' )
		{
			$o = '' ;
			foreach ( $postData as $k => $v )
			{
				$o .= "$k=" . urlencode ( $v ) . "&" ;
			}
			$postData = substr ( $o , 0 , -1 ) ;
			curl_setopt ( $curl , CURLOPT_POST , 1 ) ;
			curl_setopt ( $curl , CURLOPT_POSTFIELDS , $postData ) ;
		}
		else
			curl_setopt ( $curl , CURLOPT_CUSTOMREQUEST , $action ) ;
		$re = curl_exec ( $curl ) ;

		curl_close ( $curl ) ;
		return $re ;
	}


	/**
	 * 인증토큰 요청
	 * @return array 인증토큰 ( RequestID : 요청번호 ; Token : 인증토큰 ; Result : 결과 메시지 )
	 */
	public function getToken ()
	{
		$accesskeySecret = password_hash ( self::$accesskeySecret , PASSWORD_DEFAULT ) ;
		$headers[] = 'AccesskeyID:' . self::$accesskeyId ;
		$headers[] = "AccesskeySecret:{$accesskeySecret}" ;
		$headers[] = 'RemoteAddr:' . $_SERVER['REMOTE_ADDR'] ;
		$reqToken = self::curl ( self::$authenticationtUrl , $headers , 'GET' ) ;
		if ( ! empty ( $reqToken ) )
		{
			$reqToken = json_decode ( $reqToken ) ;
			if ( isset ( $reqToken -> Token ) )
			{
				$this -> token = $reqToken -> Token ;
				return $reqToken -> Token ;
			}
			else
				echo "<script>alert('인증 토큰 생성시 오류발생했습니다.');</script>" ;
		}
		else
			echo "<script>alert('인증 토큰 생성시 오류발생했습니다.');</script>" ;
	}


	/**
	 * 파일 리스트 검색
	 * @param string $token 인증토큰
	 * @param string $folderKey 폴더키
	 * @return array 파일 정보list ( RequestID : 요청번호 ; Files : 파일 list ; Result : 결과 메시지 )
	 */
	public function filesListSelect ( $token = '' , $folderKey = '' )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : '' ) ;
		if ( ! $_token )
			return 'No token' ;

		$key = $folderKey ? $folderKey : ($this -> folderKey ? $this -> folderKey : NULL ) ;
		if ( ! $key )
			return 'No token key' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$filesUrl . $key . '?keyType=list' , $headers , 'GET' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ; // $re -> files
	}


	/**
	 * 폴더 생성
	 * @param string $token 인증토큰
	 * @param string $folderKey 폴더키
	 * @param string $folderName 폴더명
	 * @return array 폴더키 ( RequestID : 요청번호 ; FolderKey : 폴더키 ; Result : 결과 메시지 )
	 */
	public function folderCreate ( $token = '' , $folderKey = '' , $folderName )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : '' ) ;
		if ( ! $_token )
			return 'No token' ;

		$key = $folderKey ? $folderKey : ($this -> folderKey ? $this -> folderKey : '' ) ;
		if ( ! $key )
			return 'No token key' ;

		if ( ! $folderName )
			return 'No folder name' ;

		$headers[] = 'Authorization:' . $_token ;
		$postData = array () ;
		$postData['folderName'] = $folderName ;
		$re = self::curl ( self::$foldersUrl . $key , $headers , 'POST' , $postData ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ; // $re -> folders
	}

	/**
	 * 목록 조회
	 * @param string $token 인증토큰
	 * @param string $action list검색 ( list )
	 * @param string $folderKey 폴더키 ( list검색 경우에 상위 폴더키  )
	 * @return array 목록 정보 list ( RequestID : 요청번호 ; Folders : 목록 정보 list ; Result : 결과 메시지 )
	 */
	public function foldersSelect ( $token = '' , $action = '' , $folderKey = '' )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : '' ) ;
		if ( ! $_token )
			return 'No token' ;

		$key = $folderKey ? $folderKey : ($this -> folderKey ? $this -> folderKey : NULL ) ;
		if ( ! $key )
			return 'No folder key' ;

		$headers[] = 'Authorization:' . $_token ;
		if ( $action == 'list' )
			$re = self::curl ( self::$foldersUrl . $key . '?action=' . $action , $headers , 'GET' ) ;
		else
			$re = self::curl ( self::$foldersUrl . $key , $headers , 'GET' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ; // $re -> folders
	}

	/**
	 * 파일이름 수정
	 * @param string $token 인증토큰
	 * @param  string $filesName 파일 키
	 * @param  string $filesName 파일 명
	 * @return array 결과 정보 ( RequestID : 요청번호 ; Result : 결과 메시지 )
	 */
	public function filesNameUpdate ( $token = '' , $filesKey , $filesName )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		if ( ! $filesKey )
			return 'No files key' ;

		if ( ! $filesName )
			return 'No files name' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$filesUrl . $filesKey . '?action=name&filesName=' . urlencode ( $filesName ) , $headers , 'PUT' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ , $filesKey , $filesName ) ;
	}
	/**
	 * 파일 삭제 ( 멀티 )
	 * @param string $token 인증토큰
	 * @param array $filesKeys 파일 키 ( 멀티 )
	 * @return array 결과 정보 ( RequestID : 요청번호 ; Result : 결과 메시지 )
	 */
	public function filesDelete ( $token = '' , $filesKeys )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : '' ) ;
		if ( ! $_token )
			return 'No token' ;
		if ( ! $filesKeys )
			return 'No files key' ;
		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$filesUrl . json_encode ( $filesKeys ) , $headers , 'DELETE' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ , $filesKeys ) ;
	}

	/**
	 * 태그 수정
	 * @param string $token 인증토큰
	 * @param  string $filesKey 파일 키
	 * @param  string $tag 태그내용
	 * @return array 결과 정보 ( RequestID : 요청번호 ; Result : 결과 메시지 )
	 */
	public function tagUpdate ( $token = '' , $filesKey , $tag = '' )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		if ( ! $filesKey )
			return 'No files key' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$filesUrl . $filesKey . '?action=tag&tag=' . urlencode ( $tag ) , $headers , 'PUT' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ , $filesKey , $tag ) ;
	}

	/**
	 * 다운로드 주소 요청
	 * @param string $token 인증토큰
	 * @param string $filesKey 파일 키
	 * @return array 다운로드url ( RequestID : 요청번호 ; Url : 다운로드url ; Result : 결과 메시지 )
	 */
	public function downloadTokenSelect ( $token = '' , $filesKey )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		if ( ! $filesKey )
			return 'No files key' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$downloadTokenUrl . $filesKey , $headers , 'GET' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ , $filesKey ) ; // $re -> url
	}

	/**
	 * 결과 처리
	 * @param array 결과정보
	 * @param string $functionName 호출function 이름
	 * @param string $param1 파라미터
	 * @param string $param2 파라미터
	 * @return array 디코드 된 결과정보
	 */
	public function returnMsg ( $response , $functionName , $param1 = '' , $param2 = '' )
	{
		if ( ! $response )
			return FALSE ;
		$response = json_decode ( $response ) ;
		if ( ! isset ( $response -> Result ) )
			return FALSE ;
		if ( $this -> overtime ( $response -> Result ) )
		{
			$param = array () ;
			if ( $param1 )
				array_push ( $param , $param1 ) ;
			if ( $param2 )
				array_push ( $param , $param2 ) ;
			/**
			 * 세 토큰로 다시 function 호출하기
			 */
			return $this -> countOvertime ? NULL : call_user_func_array ( array ( $this , $functionName ) , $param ) ;
		}
		return $response ;
	}

	/**
	 *  토큰 유효시간 초과할때 다시요청 ( 한번만 )
	 * @param string $msg API return 메시지
	 * @return bool TRUE:다시요청 완료 ; FALSE:거절 ( 이미 다시요청했음 )
	 */
	public function overtime ( $msg )
	{
		if ( $msg == 'InvalidToken.Expired' )
		{
			$reqToken = json_decode ( $this -> getToken () ) ;
			$this -> countOvertime = TRUE ;
			return TRUE ;
		}
		else
		{
			$this -> countOvertime = FALSE ;
			return FALSE ;
		}
	}


	/**
	 * 블랙리스트 조회
	 * @param string $token 인증토큰
	 * @return array 블랙리스트 ( RequestID : 요청번호 ; black : 블랙리스트  ; Result : 결과 메시지 )
	 */
	public function blackSelect ( $token = '' )
	{
		$_token = $token ? $token : ( $this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$blackUrl , $headers , 'GET' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ;
	}


	/**
	 * 블랙리스트 등록
	 * @param string $token 인증토큰
	 * @param string $branch 종류
	 * @param string $black 블랙리스트
	 * @return array 블랙리스트 ( RequestID : 요청번호 ; Result : 결과 메시지 )
	 */
	public function blackCreate ( $token = '' , $branch , $black )
	{
		$_token = $token ? $token : ( $this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$blackUrl , $headers , 'POST' , array ( 'branch'=> $branch , 'black' => $black ) ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ;
	}


	/**
	 * 블랙리스트 삭제
	 * @param string $token 인증토큰
	 * @param string $data 데이터
	 * @return array 블랙리스트 ( RequestID : 요청번호 ; Result : 결과 메시지 )
	 */
	public function blackDelete ( $token = '' , $data )
	{
		$_token = $token ? $token : ( $this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$blackUrl . '&blacklist=' . json_encode ( $data )  , $headers , 'DELETE' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ;
	}

	/**
	 * 파일별 토큰설정 ( 멀티 )
	 * @param string $token 인증토큰
	 * @param array $filesKey 파일 키 ( 멀티 )
	 * @param string $type 토큰 종류
	 * @param string $value 토큰 설정 값 ( 암호 토큰일 경우 각각 파일키로 암호화 후 )
	 * @return array 결과
	 */
	public function downloadTokenUpdate ( $token = '' , $filesKeys , $type , $value )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		if ( $type == 'password' )
		{
			$enc = array () ;
			foreach ( $filesKeys as $v )
				array_push ( $enc , self::encrypt ( $v , $value ) ) ;
			$value = str_replace ( '=' , '' , base64_encode ( json_encode ( $enc ) ) ) ;
		}


		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl
		(
			self::$downloadTokenUrl . json_encode ( $filesKeys ) . '&type=' . strtoupper ( $type ) . '&value=' . $value
				, $headers , 'PUT'
		) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ;
	}

	/**
	 * 토큰 발급
	 * @param string $token 인증토큰
	 * @param string $fileKey 토큰을 생성할 파일키
	 * @return array 결과 ( RequestID : 요청번호 ; Token : 생성된 토큰값 ; Result : 결과 메시지 )
	 */
	public function downloadTokenCreate ( $token , $fileKey )
	{
		$_token = $token ? $token : ($this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$downloadTokenUrl . $fileKey , $headers , 'POST' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ;
	}

	/**
	 * 토큰 삭제
	 * @param string $token 인증토큰
	 * @param string $fileKey 파일키
	 * @param string $downloadTokens 삭제할 토큰
	 * @return array 결과 ( RequestID : 요청번호 ; Token : 생성된 토큰값 ; Result : 결과 메시지 )
	 */
	public function downloadTokenDelete ( $token , $fileKey , $downloadTokens )
	{
		$_token = $token ? $token : ( $this -> token ? $this -> token : NULL ) ;
		if ( ! $_token )
			return 'No token' ;

		$headers[] = 'Authorization:' . $_token ;
		$re = self::curl ( self::$downloadTokenUrl . $fileKey . '&downloadTokens=' . json_encode ( $downloadTokens ) , $headers , 'DELETE' ) ;
		return $this -> returnMsg ( $re , __FUNCTION__ ) ;
	}

	/*
	 * 암호 토큰을 위한 AES암호화
	 * @param string $fileKey 파일 키
	 * @param string 입력한 비밀번호
	 * @return string 암호화된 비밀번호
	 */
	static public function encrypt ( $fileKey , $pwd )
	{
		$split = str_split ( $fileKey , strlen ( $fileKey ) / 2 ) ;

		$key = array_shift ( $split ) ;
		$iv = array_pop ( $split ) ;

		return urlencode ( openssl_encrypt ( $pwd , 'AES-256-CBC' , $key , OPENSSL_RAW_DATA , $iv ) ) ;
	}
}
