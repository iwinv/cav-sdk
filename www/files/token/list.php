<?php

include_once '../../inc/config.inc' ;

$AUTH -> getToken () ;

$filesKey = $_GET['filesKey'] ;
if ( ! $filesKey )
	exit ( 'No files key' ) ;

$token = $AUTH -> token ;
if ( ! $token )
	exit ( 'No token' ) ;

/*
* 파일 다운로드 url API : download url select
*/
$re = $AUTH -> downloadTokenSelect ( $token , $filesKey ) ;




$title = 'file token';

include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;


$tt = array ( 'PUBLIC' => '무제한' , 'PASSWORD' => '암호' ) ;

$type = array
(
	'PRIV' => '비공개' ,
	'TIME' => '시간(분)' ,
	'COUNT' => '횟수' ,
	'PASSWORD' => '암호' ,
	'PUBLIC' => '무제한'
) ;

//array_pop($re -> downloadToken)


?>

	<div class="div_main">
		<input type="hidden" id="accesstoken" value="<?=$token?>">
		<input type="hidden" id="fileKey" value="<?=$filesKey?>">
		<div class="item">
			<h3 id="filename"><?=$_GET['name']?></h3>
			<div class="item_body">
			<?php if ( ! isset ( $re -> downloadToken ) ) : ?>
				<div><p>비공개 파일입니다.</p></div>
			<?php elseif ( in_array ( $re->type , array ( 'PUBLIC' ) ) ) : ?>
				<div>
					<p><?=$type[$re->type]?> 토큰</p>
					<input type="hidden" value="<?=array_pop($re -> downloadToken)?>">
					<button class="link">링크복사</button>
					<button id="<?=strtolower ( $re->type )?>_download">다운로드</button>
				</div>
			<?php
				elseif ( in_array ( $re->type , array ( 'PASSWORD' ) ) ) :
			?>
				<div>
					<p><?=$type[$re->type]?> 토큰</p>
					<form action="password_download.php" method="post">
						<label for="pwd">암호 입력</label>
						<input name="pwd" id="pwd" type="password">
						<input type="hidden" name="url" value="<?=array_pop($re -> downloadToken)?>">
						<input type="hidden" name="name" value="<?=$_GET['name']?>">
						<input type="hidden" name="fileKey" value="<?=$filesKey?>">
						<br><br>
						<button type="submit">다운로드</button>
					</form>
				</div>
			<?php elseif ( in_array ( $re->type , array ( 'COUNT', 'TIME' ) ) ) : ?>
				<h3>토큰 만료시 1주일 후 자동 삭제됩니다.</h3>
				<table name="filesTable">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="10%">
					<col width="15%">
					<col width="12%">
					<col width="10%">
				</colgroup>
				<thead>
					<tr>
						<th><input type="checkbox" name="ckbAll"></th>
						<th>타입</th>
						<th>제한</th>
						<th>생성 시간</th>
						<th>링크복사</th>
						<th>다운로드</th>
					</tr>
				</thead>
				<tbody>
				<?php
				foreach ( $re->downloadToken as $v ) :
					if ( $re -> type == 'TIME' )
					{
						$time = strtotime ( $v -> date_insert ) + $v -> value * 60 - time() ;
						$v -> value = $time > 0 ? $time . '초' : '만료' ;
					}

				?>
					<tr>
						<td><input type="checkbox" name="ckbFiles" value="<?=$v -> token?>"></td>
						<td><?=$type[$re -> type]?></td>
						<td><?=$v -> value?></td>
						<td><?=$v -> date_insert?></td>
						<td><button class="link">링크복사</button></td>
						<td><button class="download">다운로드</button></td>
					</tr>
				<?php endforeach ; ?>
				</tbody>
				</table>
			</div>
			<div class="item">
				<button id="create">발급</button>
				<button id="delete">삭제</button>
			</div>
			<?php endif ; ?>
		</div>
	</div>

</body>
<!-- jQuery -->
<script src="<?=DOMAIN?>/jquery.min.js" type="text/javascript"></script>
<script>

$('#public_download').on('click',function(){
	var url = $(this).siblings('input').val();
	location.href = url;
});

$('button.link').on('click',function(){

	var token = $(this).parents('tr').find('input').val() ;
	var url = '<?=$AUTH::$downloadUrl?>' ;

	if ( token == undefined )
		url = $(this).siblings('input').val();
	else
		url += token;

	var temp = document.createElement ( 'textarea' ) ;
	document.body.appendChild ( temp ) ;
	temp.value = url ;
	temp.select () ;
	document.execCommand ( 'copy' ) ;
	temp.remove () ;

	alert ('복사 되었습니다.') ;
});


$('button.download').on('click',function(){
	var url = '<?=$AUTH::$downloadUrl?>' + $(this).parents('tr').find('input').val();
	location.href = url;
});

$('#create').on('click',function(){
	$.ajax({
		url : 'create.php' ,
		type : 'post',
		data : {
			token : $('#accesstoken').val() ,
			fileKey : $('#fileKey').val()
		},
		success : function(data){
			console.log('성공',data)
//			window.location.reload();
		},
		error : function(e){
			console.log('실패',e.responseText)
		}
	});
});

$('#delete').on('click',function(){
	if ( $('input[name=ckbFiles]:checked').length == 0 )
	{
		alert('삭제할 토큰을 선택하시기 바랍니다.');
		return ;
	}

	var keys = [];
	$('input[name=ckbFiles]:checked').each(function(i,e){
		keys.push(e.value);
	});

	$.ajax({
		url : 'delete.php' ,
		type : 'post',
		data : {
			downloadTokens : keys ,
			token: $('#accesstoken').val() ,
			fileKey : $('#fileKey').val()
		},
		success : function(data){
			console.log('성공',data);
//			window.location.reload();
		},
		error : function(e){
			console.log('실패',e.responseText);
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

</script>
</html>
