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
 * 인코딩 목록 API : encoding files select
 */
$files = $AUTH -> encodingFilesSelect ( $token , $filesKey ) ;
$files = $files->encode;


$title = '인코딩 파일 목록';

include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;

$type = array
(
	'PRIV' => '비공개' ,
	'PUBLIC' => '공개'
) ;

$status = array
(
	'WAIT' => '대기',
	'SPLIT' => '분할',
	'WORK' => '인코딩',
	'DONE' => '완료',
	'ERROR' => '에러'
) ;


?>

<div class="div_main">
	<input type="hidden" id="accesstoken" value="<?=$token?>">
	<input type="hidden" id="fileKey" value="<?=$filesKey?>">
	<div class="item">
		<h3 id="filename">
			<?=$_GET['name']?>
		</h3>
		<form action="delete.php" method="POST">
			<div class="item_body">
				<table id="list">
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
							<th>가로</th>
							<th>세로</th>
							<th>워터마크</th>
							<th>위치</th>
							<th>상태</th>
							<th>생성 시간</th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty ( $files ) ) : ?>
						<tr>
							<td colspan="8"><span>파일이 없습니다.</span></td>
						</tr>
						<?php
						else :
							foreach ( $files as $k => $v ) :
						?>
							<tr>
								<td><input type='checkbox' name='ckbFiles' value='<?=$v->key?>'></td>
								<td><?=$v->width?></td>
								<td><?=$v->height?></td>
								<td><?=$v->watermark?></td>
								<td><?=$v->position?></td>
								<td><?=$status[$v->state]?></td>
								<td><?=$v->date_insert?></td>
							</tr>
						<?php
							endforeach ;
						endif ;
						?>
					</tbody>
				</table>
			</div>
			<div class="item">
				<h3> 인코딩 파일 삭제 : </h3>
				<button id="delete">삭제</button>
			</div>
		</form>
		<div class="item">
			<h3>썸네일 수정</h3>
			<form name="thumbnailUpload" enctype="multipart/form-data">
				<div><input type="file" name="thumbnail"></div>
				<br>
				<button  type="button" name="btnUpload">업로드 시작</button>
			</form>
		</div>
		<div class="item">
			<h3> 디폴트 플레이 설정 : </h3>
			<button id="pc" class="default">pc 설정</button>
			<button id="mobile" class="default">mobile 설정</button>
		</div>
		<div class="item">
			<h3> 미리보기 : </h3>
			<button href="player.php?filesKey=<?=$filesKey?>">플레이어</button>
			<button href="source.php?filesKey=<?=$filesKey?>">영상 소스</button>
		</div>
		<div class="item">
			<h3>추가 인코딩 : </h3>
			<?php
			$encode = $AUTH->encodeSelect () ;
			$encode = $encode->encode ;

			$watermark = $AUTH->watermarkSelect();
			$watermark = $watermark->Watermark;
			$position = $AUTH->watermarkPosition();
			$position = $position->Position;
			?>
			<div class="item-body">
				<form id="encoding" action="create.php" method="POST">
					<input id="filekey" type="text" value="<?=$filesKey?>" hidden>
					<table>
						<thead>
							<tr>
								<th><input type="checkbox" name="ckbAll"></th>
								<th>별칭</th>
								<th>가로</th>
								<th>세로</th>
								<th>비율</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ( $encode as $v ) : ?>
							<tr>
								<td><input type="checkbox" name="ckbEncoding" value="<?=$v->name?>"></td>
								<td><?=$v->name?></td>
								<td><?=$v->width?></td>
								<td><?=$v->height?></td>
								<td><?=$v->ratio?></td>
							</tr>
						<?php endforeach ; ?>
						</tbody>
					</table>
					<br>
					<div class="select">
						<div class="select-group">
							<h5>워터마크 설정</h5>
							<select name="watermark" id="watermark">
								<option value="">설정없음</option>
								<?php foreach ( $watermark as $v ) : ?>
								<option value="<?=$v->name?>"><?=$v->name?></option>
								<?php endforeach ; ?>
							</select>
						</div>
						<div class="select-group">
							<h5>워터마크 위치 설정</h5>
							<select name="position" id="position">
								<option value="">설정없음</option>
								<?php foreach ( $position as $v ) : ?>
								<option value="<?=$v?>"><?=$v?></option>
								<?php endforeach ; ?>
							</select>
						</div>
					</div>
					<button id="encode">인코딩 요청</button>
				</form>
			</div>
		</div>

	</div>
</div>

</body>
<!-- jQuery -->
<script src="<?=DOMAIN?>/jquery.min.js" type="text/javascript"></script>
<script>
	(function(){

		var $list = $('#list');
		var $encoding = $('#encoding');
		var $upload = $('form[name=thumbnailUpload]') ;


		$('.default').each(function(k,v){
			$(v).click(function(){
				var type = $(this).prop('id') ,
					fileKey = $('#fileKey').val() ,
					$checked = $list.find('input:checked:not([name=ckbAll])') ,
					key = '';
				if ( $checked.length != 1 )
				{
					alert('디폴트 플레이 설정할 화질을 하나만 선택하시기 바랍니다.');
					return;
				}

				$checked.each(function(k,v){
					key = $(v).val();
				});

				$.ajax({
					url:'default.php',
					type:'POST',
					data: { type , key , fileKey } ,
					success:function(data){
						alert(data);
						location.reload();
					},
					error:function(e){
						alert(e.responseText);
						location.reload();
					}
				});
			});
		});


		$upload.find('button').click(function(){
			var file = $upload.find('input[type=file]').get(0).files ;
			if (file.length == 0)
			{
				alert('썸네일 이미지를 선택해주시기 바랍니다.');
				return;
			}

			file = new File ( file , file[0].name , { type : file[0].type } ) ;

			var form = new FormData ($upload.get(0));
			form.delete('thumbnail');
			form.append('thumbnail',file);

			$.ajax({
				url: '<?=$AUTH::$filesUrl . $filesKey . '&token=' . $AUTH->token . '&action=thumbnail'?>' ,
				type: 'PUT' ,
				data: form ,
				cache : false ,
				processData : false ,
				contentType : false ,
				success:function(data){
					alert(data.Result);
					location.reload();
				},
				error:function(e){
					alert(e.responseText);
					location.reload();
				}
			});
		});

		$('button[href]').click(function(){
			location.href = $(this).attr('href') ;
		});

		[$list,$encoding].forEach(function(v){
			v.find('input[name="ckbAll"]').click(function(){
				v.find('input[type=checkbox]').prop('checked', $(this).prop('checked') );
			});
		});

		$('#delete').click(function(e){
			e.preventDefault();
			var $checked = $list.find('input:checked:not([name=ckbAll])') ,
				keys = [] ;
			if ( $checked.length == 0 )
			{
				alert('삭제할 인코딩 파일을 선택해주시기 바랍니다.');
				return;
			}

			$checked.each(function(k,v){
				keys.push($(v).val());
			});


			$.ajax({
				url:'delete.php',
				type:'POST',
				data: { keys } ,
				success:function(data){
					alert(data);
					location.reload();
				},
				error:function(e){
					alert(e.responseText);
					location.reload();
				}
			});
		});

		$('#encode').click(function(e){
			e.preventDefault();
			var $checked = $encoding.find('input:checked'),
				encoding = [] ,
				watermark = $('#watermark').val() ,
				position = $('#position').val() ;
			if ( $checked.length == 0 )
			{
				alert('추가로 인코딩할 화질을 선택해주시기 바랍니다.');
				return;
			}
			$checked.each(function(k,v){
				encoding.push($(v).val());
			});

			if ( watermark != '' ^ position != '' )
			{
				alert ( '워터마크 설정시 워터마크와 위치 둘다 선택해야 합니다.' ) ;
				return;
			}

			var $form = $(this).parents('form') ;

			$.ajax({
				url: $form.attr('action'),
				type:$form.attr('method'),
				data:{
					encoding:encoding,
					fileKey:$('#filekey').val(),
					watermark:watermark,
					position:position
				},
				success:function(data){
					alert(data);
					location.reload();
				},
				error:function(e){
					alert(e.responseText);
					location.reload();
				}
			});
		});


	})();

</script>
</html>
