<?php
$doc = ShareFile::with('isFile')->whereHas('isFile', function($query) {
    $query->where('files.type', 7);
})->where(function($query) {
    $query->where('target', 'user')->where('target_id', Auth::user()->id);
})->first();
?>

<script type="text/javascript">
$(document).ready(function(){
		
	$('#btn1').mousedown(function(){
		//location.replace("index_1.php");
		location = "search.php";
	});
	$('#btn2').mousedown(function(){
		location.replace('/doc/<?= $doc->id ?>/open');
	});
});
</script>
<style>

#page_center {
	background-image: url(/analysis/use/images/chose.jpg);
	background-repeat: no-repeat;
	height:500px;
}
#btn1,#btn2 {
	background:none;
	border:none;
}

</style>

<div id="page" style="width:960px">

	<div id="banner"><img src="/analysis/use/images/banner.png" /></div>
	<div id="page_center">

		<div style="position:relative">

			<button id="btn1" style="content:'';height:400px;width:400px"></button>
			<button id="btn2" style="content:'';height:400px;width:400px;float:right"></button>

		</div>

	</div>

</div>