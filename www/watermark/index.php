<?php

/*
 * 프레임워크 파일을 불러옵니다.
 */
include_once '../inc/config.inc' ;

/*
 * Token 생성
 */
$AUTH -> getToken () ;

$title = 'watermark' ;
include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;

?>

	<div class="div_main">
		<input type="hidden" id="token" value="<?= $AUTH -> token ; ?>">
		<div class="item">
			<?php
			/*
			* 워터마크 조회 API : watermark select
			*/
			$watermarkList = $AUTH -> watermarkSelect ( $AUTH -> token ) ;
			if ( isset ( $watermarkList -> Watermark ) )
				$watermarkList = $watermarkList -> Watermark ;
			else
			{
				$watermarkList = '' ;
				echo "<script>	alert( '파일 조회 할때 오류가 발생했습니다.' );</script>" ;
			}
			?>
			<h3>파일 목록 : </h3>

			<div class="item_body">
				<table name="watermarkTable">
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
					<th>별칭</th>
					<th>가로(픽셀)</th>
					<th>세로(픽셀)</th>
					<th>생성 시간</th>
				</tr>
				</thead>
				<tbody>
					<?php if ( $watermarkList ) : ?>
						<?php foreach ( $watermarkList as $k => $v ) : ?>
						<tr>
							<td><input type='checkbox' name='ckbWatermark' value='<?=$v->name?>'></td>
							<td title='<?=$v -> name?>'><?=$v -> name?></td>
							<td><?=$v -> width?></td>
							<td><?=$v -> height?></td>
							<td><?=$v -> date_insert?></td>
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
			<h3> 워터마크 업로드 : </h3>
			<div class="item_body">
				<form name="formUpload" method="POST" enctype="multipart/form-data">
					<input type="hidden" name="token" value="<?= $AUTH -> token ?>">
					<div class="input-group">
						<label>별칭</label>
						<input type="text" name="name">
					</div>
					<br>
					<div>
						<input type="file" name="watermark"  id="watermark">
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
			<h3> 워터마크 삭제 : </h3>
			<div class="item_body">
				<button type="button" name="watermarkDelete">삭 제</button>
			</div>
		</div>
	</div>


	</div>

</body>

<!-- jQuery -->
<script src="<?=DOMAIN ?>/jquery.min.js" type="text/javascript"></script>

<script>

$ ( document ).ready ( function () {

	$ ( 'input[name="ckbAll"]' ).on ( "click" , function () {
		 if ( $ ( this ).is ( ':checked' ) )
		{
			$ ( 'input[name="ckbWatermark"]' ).each ( function () {
				$ ( this ).prop ( "checked" , true ) ;
			} ) ;
		 }
		else
		{
			$ ( 'input[name="ckbWatermark"]' ).each ( function () {
				$ ( this ).prop ( "checked" , false ) ;
			} ) ;
		}
	 } ) ;


	/*
	* 워터마크 업로드 API : watermark upload
	*/
	$( "button[name=btnUpload]" ) . click ( function () {

		var fileInput = $ ( '#watermark' ).get ( 0 ).files ;

		if ( fileInput.length == 0 )
		{
			alert ( "업로드할 파일을 선택해주세요." ) ;
			return ;
		}

		// 업로드 파일이름 encode
		var fileSend = new File ( fileInput , fileInput[0].name , { type : fileInput[0].type } );
		var form = new FormData ( document.getElementsByName ( "formUpload" )[0] ) ;
		form.delete('watermark');
		form.append('watermark',fileSend);

		for ( var pair of form.entries() )
		{
			if ( pair[1] == '' )
			{
				alert('별칭을 입력해주시기 바랍니다.');
				return;
			}
		}
		$.ajax ( {
			url : "<?= $AUTH::$watermarkUrl ?>" ,
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
					location.reload() ;
				}

			} ,
			error : function ( e ) {
				alert ( e.responseText ) ;
			}
		} ) ;
	});

	/*
	* 워터마크 삭제 API : watermark delete
	*/
	$ ( "button[name=watermarkDelete]" ).click ( function () {
		var checked = $ ( "input[name=ckbWatermark]:checked" ) ;

		if ( checked.length < 1 )
		{
			alert ( "선택된 파일이 없습니다." ) ;
			return ;
		}

		var watermarks = [ ] ;
		checked.each ( function () {
			watermarks.push ( $ ( this ).val () ) ;
		} ) ;

		$.ajax ( {
			url : "delete.php" ,
			type : "post" ,
			dataType : "json" ,
			data : {
				watermarks : watermarks ,
				token : $ ( "#token" ) . val()
			} ,
			success : function ( data )
			{
				alert ( data ) ;
				location.reload() ;
			} ,
			error : function ( e )
			{
				alert("파일 삭제중에 문제가 발생했습니다.");
				console.log( e.responseText );
			}
		} ) ;
	} ) ;
} ) ;

</script>
</html>
