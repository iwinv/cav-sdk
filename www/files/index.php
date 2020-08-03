<?php
/*
 * ===========================================================================
 * 파일 관련 API 예제
 * ===========================================================================
 *
 * 파일목록 : "files list select" API를 통해서 파일 정보를 API서버에서 불러옵니다.
 * 파일 업로드 : "files upload" API를 통해서 파일을 API서버로 업로드 합니다.
 * 파일 이름 수정 : "files name update" API를 통해서 파일이 름을 수정합니다.
 * 파일 태그 수정 : "files tag update" API를 통해서 파일 태그를 수정합니다.
 * 파일 삭제 : "files delete" API를 통해서 파일를 삭제합니다.
 * 파일 다운로드 토큰 : "download url select" API를 통해서 파일 다운로드 주소를 불러옵니다.
 *
 * ---------------------------------------------------------------------------
 * 작성자: 리성림 <chenglin@smileserv.com>
 * 작성일: 2018년 06월 07일
 * ===========================================================================
 */


/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../inc/config.inc' ;

/*
 * 폴더키를 지정할수 있습니다.
 */
isset ( $_GET['key'] ) ? $AUTH -> folderKey = $_GET['key'] : '' ;

/*
 * Token 생성
 */
$AUTH -> getToken () ;


$title = 'Files - Demo' ;
include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;

$type = array
(
	'PRIV' => '비공개' ,
	'TIME' => '시간' ,
	'COUNT' => '횟수' ,
	'PASSWORD' => '암호' ,
	'PUBLIC' => '무제한'
) ;

?>

	<div class="div_main">

		<input type="hidden" id="token" value="<?= $AUTH -> token ; ?>">


		<div class="item">
			<?php

			/*
			* 파일 가져오기 API : files list select
			*/
			$filesList = $AUTH -> filesListSelect ( $AUTH -> token ) ;
			if ( isset ( $filesList -> Files ) )
				$filesList = $filesList -> Files ;
			else
			{
				$filesList = '' ;
				echo "<script>	alert( '파일 조회 할때 오류가 발생했습니다.' );</script>" ;
			}
			?>
			<h3>파일 목록 : </h3>

			<div class="item_body">
				<table name="filesTable">
				<colgroup>
					<col width="5%">
					<col width="20%">
					<col width="10%">
					<col width="10%">
					<col width="20%">
					<col width="10%">
					<col width="20%">
					<col width="20%">
				</colgroup>
				<thead>
				<tr>
					<th><input type="checkbox" name="ckbAll"></th>
					<th>파일이름</th>
					<th>파일 크기</th>
					<th>토큰 타입</th>
					<th>토큰 제한값</th>
					<th>태그</th>
					<th>생성 시간</th>
					<th>토큰</th>
				</tr>
				</thead>
				<tbody>
					<?php if ( $filesList ) : ?>
						<?php
						foreach ( $filesList as $k => $v ) :

							// html 이스케이프
							if ( htmlspecialchars ( $v -> name ) != $v -> name ) $v -> name = htmlspecialchars ( $v -> name ) ;

							// 너무길면 줄이기
							foreach ( array ( 'name' , 'value' , 'tag' ) as $vv )
							{
								if ( $vv == 'name' )
									$name = $v -> $vv ;
								if ( strlen ( $v -> $vv ) > 20 )
									$v -> $vv = substr ( $v -> $vv , 0 , 20 ) . '...' ;
							}


						?>
						<tr>
							<td><input type='checkbox' name='ckbFiles' value='<?=$k?>'></td>
							<td title='<?=$v -> name?>'><?=$v -> name?></td>
							<td><?=$v -> size?></td>
							<td><?=$type[$v -> type]?></td>
							<td><?=$v -> value?></td>
							<td><?=$v -> tag?></td>
							<td><?=$v -> date_insert?></td>
							<td><a href='token/list.php?filesKey=<?=$k?>&name=<?=$name?>'>리스트</a></td>
						</tr>
						<?php endforeach ; ?>
					<?php else : ?>
					<tr>
						<td colspan="8"><span>파일이 없습니다.</span></td>
					</tr>

					<?php endif ; ?>
				</tbody>
				</table>
			</div>
		</div>


	<div>
		<div class="item">
			<h3> 파일 업로드 : </h3>
			<div class="item_body">
				<form name="formUpload" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="token" value="<?= $AUTH -> token ?>">
					<span>태그 : </span>
					<input type="text" name="tag" class="tag" value="original,free">
					<div>
						<input type="file" name="files[]"  id="filesFile" multiple="multiple" >
					</div>
				</form>
				<button  type="button" name="btnUpload">업로드 시작</button>
				<progress name="progressBar" value="0" max="100"> </progress>
				<span class="time"></span>
			</div>
		</div>
	</div>
	<div>
		<div class="item">
			<h3> 이름 수정 : </h3>
			<div class="item_body">
				<input type="text" name="nameUpdate"></input>
				<button type="button" name="nameUpdate">수 정</button>
			</div>
		</div>
		<div class="item">
			<h3> 태그 수정 : </h3>
			<div class="item_body">
				<input type="text" name="tagUpdate"></input>
				<button type="button" name="tagUpdate">수 정</button>
			</div>
		</div>
		<div class="item">
			<h3> 파일 삭제 : </h3>
			<div class="item_body">
				<button type="button" name="filesDelete">삭 제</button>
			</div>
		</div>
		<div class="item">
			<h3> 토큰 설정 : </h3>
			<div class="item_body">
				<form name="token" action="token/setting.php" method="post">
					<div class="radio-group">
						<label for="count">토큰 (횟수)</label><input id="count" type="radio" name="type" value="count">
					</div>
					<div class="radio-group">
						<label for="time">토큰 (시간)</label><input id="time" type="radio" name="type" value="time">
					</div>
					<div class="radio-group">
						<label for="password">토큰 (암호)</label><input id="password" type="radio" name="type" value="password">
					</div>
					<div class="radio-group">
						<label for="priv">비공개</label><input id="priv" type="radio" name="type" value="priv">
					</div>
					<div class="radio-group">
						<label for="public">무제한</label><input id="public" type="radio" name="type" value="public">
					</div>

					<div class="input-group">
						<span></span> <input type="text" name="value" disabled>
						<button type="button" name="tokenUpdate">수 정</button>
					</div>
				</form>
			</div>
		</div>
	</div>


	</div>

</body>

<!-- jQuery -->
<script src="<?=DOMAIN ?>/jquery.min.js" type="text/javascript"></script>

<script>

$ ( document ).ready ( function () {

	$('form[name=token]').find('.radio-group > input').on('click',function(){
		var $input = $(this).parents('form').find('.input-group > input') ,
			$span = $(this).parents('form').find('span') ;

		$input.prop('disabled',false) ;
		if ( $(this).val() == 'password' )
		{
			$input.prop('type','password') ;
			$span.html('암호');
		}
		else if ( $(this).val() == 'count' )
		{
			$input.prop('type','number') ;
			$span.html('횟수');
		}
		else if ( $(this).val() == 'time' )
		{
			$input.prop('type','number') ;
			$span.html('시간 (분)');
		}
		else
		{
			$input.prop('disabled',true) ;
			$span.html('');
		}
	});
	$('form[name=token] > .input-group').find('input').on('keydown',function(e){
		if ( e.keyCode == 13 )
		{
			e.preventDefault();
			$('button[name=tokenUpdate]').trigger('click');
		}
	});

	$('button[name=tokenUpdate]').on('click',function(){
		var $form = $(this).parents('form') ,
			$radio = $form.find('input:checked[type=radio]') ,
			$input = $form.find('input[name=value]') ,
			$files = $('input[name=ckbFiles]:checked') ,
			keys = [] ,
			data = { token : $("#token").val() } ;

			if ( $files.length == 0 )
			{
				alert('파일을 선택 해주시기 바랍니다.');
				return;
			}
			$('input[name=ckbFiles]:checked').each(function(i,e){
				keys.push($(e).val());
			});
			Object.assign(data, {files:keys} );

			if ( $input.prop('disabled') )
				Object.assign(data, {type:$radio.val()}) ;
			else
				Object.assign(data, { type:$radio.val() , value:$input.val() }) ;

			if ( data.type == undefined )
			{
				alert('토큰을 설정해주시기 바랍니다.');
				return;
			}

			$.ajax({
				url : $form.prop('action') ,
				data : data ,
				type: $form.prop('method') ,
				success:function(data){
					console.log('성공',data);
					window.location.reload();
					alert('다운로드 토큰이 설정되었습니다.');
				},
				error:function(e){
					console.log('에러',e.responseText);
				}
			});

	});


	$ ( 'input[name="ckbAll"]' ).on ( "click" , function () {
		 if ( $ ( this ).is ( ':checked' ) )
		{
			$ ( 'input[name="ckbFiles"]' ).each ( function () {
				$ ( this ).prop ( "checked" , true ) ;
			} ) ;
		 }
		else
		{
			$ ( 'input[name="ckbFiles"]' ).each ( function () {
				$ ( this ).prop ( "checked" , false ) ;
			} ) ;
		}
	 } ) ;


	/*
	* 파일 업로드 API : files upload
	*/
	$( "button[name=btnUpload]" ) . click ( function () {

		var fileInput = $ ( '#filesFile' ).get ( 0 ).files ;

		if ( fileInput.length == 0 )
		{
			alert ( "업로드할 파일을 선택해주세요." ) ;
			return ;
		}

		// 업로드 파일이름 encode
		var fileSend = new File ( fileInput , btoa(encodeURIComponent(fileInput[0].name)) , { type : fileInput[0].type } );
		var form = new FormData ( document.getElementsByName ( "formUpload" )[0] ) ;
		form.delete('files[]');
		form.append('files[]',fileSend);

		var size = (fileSend.size/1024/1024/1024).toFixed(2);

		$.ajax ( {
			url : "<?= $AUTH::$filesUrl . $AUTH -> folderKey ?>" ,
			type : "POST" ,
			data : form ,
			dataType : "json" ,
			cache : false ,
			processData : false ,
			contentType : false ,
			xhr : function () {
				myXhr = $.ajaxSettings.xhr () ;
				var t = 0;
				if ( myXhr.upload ) { // check if upload property exists
					myXhr.upload.addEventListener ( 'progress' , function ( e ) {
						t++;
						$('span.time')[0].innerHTML = t;
						var progressBar = $ ( "progress[name=progressBar]" ) ;
						progressBar.prop ( 'max' , e.total ) ;
						progressBar.val ( e.loaded ) ;
					} , false ) ;
				}
				return myXhr ;
			} ,
			success : function ( data )	{
				if ( typeof ( data . Result ) == "undefined" )
				{
					alert ( "Upload error" ) ;
				}
				else
				{
					alert ('upload success');
					window.location.reload() ;
				}

			} ,
			error : function ( e ) {
				alert ( e.responseText ) ;
			}
		} ) ;
	});

	/*
	* 파일 삭제 API : files delete
	*/
	$ ( "button[name=filesDelete]" ).click ( function () {
		var checked = $ ( "input[name=ckbFiles]:checked" ) ;
		if ( checked.length < 1 )
		{
			alert ( "선택된 파일이 없습니다." ) ;
			return ;
		}
		var filesKeys = [ ] ;
		checked.each ( function () {
			filesKeys.push ( $ ( this ).val () ) ;
		} ) ;

		function deleteFiles(filesKeys){
			$.ajax ( {
				url : "delete.php" ,
				type : "post" ,
				dataType : "json" ,
				data : {
					filesKeys : filesKeys ,
					token : $ ( "#token" ) . val()
				} ,
				success : function ( data )
				{
					alert ( data ) ;
					window.location.reload() ;
				} ,
				error : function ( e )
				{
					alert("파일 삭제중에 문제가 발생했습니다.");
					console.log( e.responseText );
				}
			} ) ;
		}
		deleteFiles(filesKeys);
	} ) ;

	/*
	* 파일이름 수정 API : files name update
	*/
	$ ( "button[name=nameUpdate]" ).click ( function () {
		var checked = $ ( "input[name=ckbFiles]:checked" ) ;
		if ( checked.length < 1 )
		{
			alert ( "선택된 파일이 없습니다." ) ;
			return ;
		}
		if ( checked.length > 1 )
		{
			alert ( "파일 하나만 선택하세요." ) ;
			return ;
		}
		var filesKey = checked.val () ;
		var nameInput = $ ( "input[name=nameUpdate]" ) ;
		var name = nameInput.val () ;
		$.ajax ( {
			url : "updateName.php" ,
			type : "post" ,
			dataType : "json" ,
			data : {
				filesKey : filesKey ,
				filesName : name ,
				token : $ ( "#token" ) . val()
			} ,
			success : function ( data , status )
			{
				alert ( data ) ;
				if ( data == "Files name update success" )
					window.location.reload() ;
			} ,
			error : function ( e )
			{
				alert("파일 이름 수정중에 문제가 발생했습니다.");
				console.log ( e.responseText ) ;
			}
		} ) ;
	} ) ;

	/*
	* 파일 태그 수정 API : files tag update
	*/
	$ ( "button[name=tagUpdate]" ) . click ( function () {
		var checked = $ ( "input[name=ckbFiles]:checked" ) ;
		if ( checked.length < 1 )
		{
			alert ( "선택된 파일이 없습니다." ) ;
			return ;
		}
		if ( checked.length > 1 )
		{
			alert ( "파일를 하나만 선택하세요." ) ;
			return ;
		}
		var filesKey = checked.val () ;
		var tagInput = $ ( "input[name=tagUpdate]" ) ;
		var tag = tagInput.val () ;
		var json = {
				"filesKey" : filesKey ,
				"tag" : tag ,
				"token" : $ ( "#token" ) . val()
		};
		json = JSON.stringify(json);
		$.ajax ( {
			url : "updateTag.php" ,
			type : "post" ,
			dataType : "json" ,
			data : {json} ,
			success : function ( data )
			{
				alert ( data ) ;
				if ( data == "Files tag update success" )
					window.location.reload() ;
			} ,
			error : function ( e )
			{
				alert("태그 수정중에 문제가 발생했습니다.");
				console.log ( e.responseText ) ;
			}
		} ) ;
	} ) ;
} ) ;









</script>
</html>
