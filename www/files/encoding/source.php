<?php
include_once '../../inc/config.inc' ;

$AUTH -> getToken () ;

$filesKey = $_GET['filesKey'] ;
if ( ! $filesKey )
	exit ( 'No files key' ) ;

$token = $AUTH -> token ;
if ( ! $token )
	exit ( 'No token' ) ;




$title = '인코딩 영상 소스 리스트';

include_once INC . DIRECTORY_SEPARATOR . 'header.inc' ;


/*
* 인코딩 소스 url select API : encoding list url select
*/
$re = $AUTH -> encodingVideoSelect ( $token , $filesKey , 'list' ) ;

if ( ! isset ( $re->list ) )
	exit ( '에러' ) ;
?>
<link href="https://vjs.zencdn.net/7.8.4/video-js.css" rel="stylesheet" />
<link href="https://unpkg.com/@silvermine/videojs-quality-selector/dist/css/quality-selector.css" rel="stylesheet">
<div class="div_main">
	<div class="item">
		<div class="item_body">
			<table id="list">
				<thead>
					<tr>
						<th>화질</th>
						<th>소스 링크</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $re->list as $quality => $url ) : ?>
					<tr>
						<td><?=$quality?></td>
						<td><?=$url?></td>
					</tr>
					<?php endforeach ; ?>
				</tbody>
			</table>
		</div>
	</div>

	<div class="item">
		<h3>video js</h3>
		<video class="video-js" width="640" height="360" controls preload="metadata">
			<?php foreach ( $re->list as $quality => $url ) : ?>
			<source src="<?=$url?>" type="application/x-mpegURL" label="<?=$quality?>"></source>
			<?php endforeach ; ?>
		</video>
	</div>
</div>

<script src="https://vjs.zencdn.net/7.8.4/video.js"></script>
<script src="https://unpkg.com/@silvermine/videojs-quality-selector/dist/js/silvermine-videojs-quality-selector.min.js"></script>
<script>
	(function(){
		videojs(document.querySelector('video'),{
			controlBar:{
				children:['playToggle','progressControl','volumePanel','qualitySelector','fullscreenToggle',]
			}
		});
	})();
</script>