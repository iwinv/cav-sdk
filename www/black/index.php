<?php
/**
 * 기본 설정을 불러옵니다.
 */
include_once '../inc/config.inc' ;

/*
 * Token 생성
 */
$AUTH -> getToken () ;

$title = 'Blacklist';

include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;

$black = $AUTH->blackSelect () ;

?>


<div class="div_main">

	<input type="hidden" id="token" value="<?= $AUTH -> token ; ?>">

	<div class="item">
		<h3>블랙리스트 목록 :</h3>
		<div class="item_body">
			<table>
				<thead>
					<tr>
						<th><input type="checkbox" name="ckbAll"></th>
						<th>종류</th>
						<th>타겟</th>
						<th>적용범위</th>
						<th>생성시간</th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty ( $black->black ) ) : ?>
					<td colspan="5">결과가 없습니다.</td>
					<?php else : ?>
					<?php foreach ( $black->black as $k => $v ) : ?>
					<tr>
						<td><input type="checkbox" name="ckbRecords" value="<?=$v->target?>"></td>
						<td><?=$v->branch?></td>
						<td><?=$v->target?></td>
						<td><?=$v->range?></td>
						<td><?=$v->date_insert?></td>
					</tr>
					<?php endforeach ; ?>
					<?php endif ; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="item">
		<h3>블랙리스트 추가 :</h3>
		<div class="item_body">
			<p>
				<div class="radio-group">
					<label for="referer">레퍼러</label><input id="referer" type="radio" name="branch" value="referer">
				</div>
				<div class="radio-group">
					<label for="ip">아이피</label><input id="ip" type="radio" name="branch" value="ip">
				</div>
				<div class="radio-group">
					<label for="agent">에이전트</label><input id="agent" type="radio" name="branch" value="agent">
				</div>
			</p>
			<div class="input-group">
				<span></span> <input type="text" name="blackadd">
				<button type="button" name="blackadd">추 가</button>
			</div>
		</div>
	</div>

	<div class="item">
		<h3>블랙리스트 삭제 :</h3>
		<div class="item_body">
			<button type="button" name="blackdelete">삭제</button>
		</div>
	</div>

</div>

<!-- jQuery -->
<script src="<?=DOMAIN ?>/jquery.min.js" type="text/javascript"></script>

<script>
(function(){
	var $records = $('input[name="ckbRecords"]') ,
		token = $('#token').val() ;

	// 체크박스 전체 선택 / 해제
	$('input[name="ckbAll"]').on('click',function(){
		if ( $(this).is(':checked') )
			$('input[name="ckbRecords"]').each(function(){
				$(this).prop('checked',true);
			});
		else
			$('input[name="ckbRecords"]').each(function(){
				$(this).prop('checked',false);
			});
	});


	$('button[name="blackdelete"]').on('click',function(){
		var target = [] ;
		$records.each(function(){
			if ( $(this).is(':checked') )
				target.push($(this).val());
		});

		if ( target.length == 0 )
		{
			alert('삭제할 레코드를 선택하시기 바랍니다.');
			return;
		}

		$.ajax({
			url: "./delete.php" ,
			type: "POST" ,
			data: {
				"records" : target ,
				"token" : token
			} ,
			success: function(data){
				console.log('성공',data);
				alert(data);
				window.location.reload();
			},
			error: function(res){
				console.log('실패',res.responseJSON,res.responseText,res);
			}
		});


	});

	$('button[name="blackadd"]').on('click',function(){
		var target = $('input[name=blackadd]').val() ,
			branch = $('input:checked[name=branch]').val() ;

		if ( target.length == 0 )
			alert('추가할 블랙리스트를 입력해주시기 바랍니다.');

		$.ajax({
			url : "./create.php" ,
			type: "POST" ,
			data : {
				"branch" : branch ,
				"target" : target ,
				"token" : token
			} ,
			success : function(data){
				console.log('성공',data);
				alert(data);
				window.location.reload();
			},
			error: function(res){
				console.log('실패',res.responseText);
			}
		})
	});

})();
</script>