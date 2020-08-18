<?php

/**
 * 기본 설정을 불러옵니다.
 */
include_once '../inc/config.inc' ;

/*
 * Token 생성
 */
$AUTH -> getToken () ;

$title = 'Encode';

include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;

$encode = $AUTH->encodeSelect () ;

?>


<div class="div_main">

	<input type="hidden" id="token" value="<?= $AUTH -> token ; ?>">

	<div class="item">
		<h3>인코딩 화질 목록 :</h3>
		<div class="item_body">
			<table>
				<thead>
					<tr>
						<th><input type="checkbox" name="ckbAll"></th>
						<th>별칭</th>
						<th>가로(픽셀)</th>
						<th>세로(픽셀)</th>
						<th>비율</th>
						<th>생성시간</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty ( $encode->encode ) ) : ?>
					<td colspan="5">결과가 없습니다.</td>
					<?php else : ?>
					<?php foreach ( $encode->encode as $k => $v ) : ?>
					<tr>
						<td><input type="checkbox" name="ckbEncode" value="<?=$v->name?>"></td>
						<td><?=$v->name?></td>
						<td><?=$v->width?></td>
						<td><?=$v->height?></td>
						<td><?=$v->ratio?></td>
						<td><?=$v->date_insert?></td>
					</tr>
					<?php endforeach ; ?>
					<?php endif ; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="item">
		<h3>인코딩 추가 :</h3>
		<div class="item_body">
				<div class="input-group">
					<label for="name">별칭</label>
					<input id="name" type="text" name="name">
				</div>
				<div class="input-group">
					<label for="width">가로(픽셀)</label>
					<input id="width" type="number" name="width">
				</div>
				<div class="input-group">
					<label for="height">세로(픽셀)</label>
					<input id="height" type="number" name="height">
				</div>
			<div class="input-group">
				<button type="button" name="add">추 가</button>
			</div>
		</div>
	</div>

	<div class="item">
		<h3>인코딩 삭제 :</h3>
		<div class="item_body">
			<button type="button" name="delete">삭제</button>
		</div>
	</div>

</div>

<!-- jQuery -->
<script src="<?=DOMAIN ?>/jquery.min.js" type="text/javascript"></script>

<script>
(function(){
	var $tr = $('tbody > tr') ,
		$encodes = $('input[name="ckbEncode"]') ,
		token = $('#token').val() ;

	// 체크박스 전체 선택 / 해제
	$('input[name="ckbAll"]').on('click',function(){
		if ( $(this).is(':checked') )
			$('input[name="ckbEncode"]').each(function(){
				$(this).prop('checked',true);
			});
		else
			$('input[name="ckbEncode"]').each(function(){
				$(this).prop('checked',false);
			});
	});


	$('button[name="delete"]').on('click',function(){
		var target = [] ;
		$encodes.each(function(){
			if ( $(this).is(':checked') )
				target.push($(this).val());
		});

		if ( target.length == 0 )
		{
			alert('삭제할 인코딩 화질을 선택하시기 바랍니다.');
			return;
		}

		$.ajax({
			url: "./delete.php" ,
			type: "POST" ,
			data: {
				"encodes" : target ,
				"token" : token
			} ,
			success: function(data){
				alert(data.Result);
				location.reload();
			},
			error: function(e){
				alert(e.responseText);
				location.reload();
			}
		});


	});

	$('button[name="add"]').on('click',function(){

		var $input = $(this).parents('.item_body').find('input') ,
			data = {} ;

		$input.each(function(k,v){
			var input = $(v);
			data[input.attr('name')] = input.val();
		});

		for( var [name, val] of Object.entries(data) ){
			if ( val == '' )
			{
				alert('추가할 인코딩 화질을 입력해주시기 바랍니다.');
				return;
			}
			if ( name == 'width' || name == 'height' )
			{
				val = Number(val);
				if ( 100 > val || val > 5000 || val % 2 == 1 )
				{
					alert('인코딩 값은 짝수만 가능하고 100 ~ 5000 사이만 입력 가능합니다.');
					return;
				}
			}
			else
			{
				if ( val.match(/[^a-zA-Z0-9ㄱ-ㅎㅏ-ㅣ가-힣]/) || 2 > val.length || val.length > 20 )
				{
					alert('별칭은 2~20글자로 특수문자 제외 한글 영어 숫자만 입력가능합니다.');
					return;
				}
			}
		}

		$.ajax({
			url : "./create.php" ,
			type: "POST" ,
			data : {
				"encode" : data ,
				"token" : token
			} ,
			success : function(data){
				alert(data.Result);
				location.reload();
			},
			error: function(e){
				alert(e.responseText);
				location.reload();
			}
		})
	});

})();
</script>