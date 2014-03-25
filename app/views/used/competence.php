<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Question Management</title>

<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
<!--[if lt IE 9]>
<script src="js/html5shiv.js"></script>
<![endif]-->

<script type="text/javascript">
$(document).ready(function(){
	$('.head').on('click','input',function(e){
		c = $(e.currentTarget);
		target = c.attr('target');
		
		if( !c.is(':checked') )
		$(e.delegateTarget).parent().find('input').each(function(){
			if( $(this).attr('target')==target ){
				$(this).prop('checked',false);
			}
		});
	});	
});
</script>
<style>
html,body {
	height: 100%;
	font-family: 微軟正黑體;
}
body {
	margin: 0;
	padding: 0;
}
.td {
	float: left;
	text-align: center;
}
.head {
	background-color: rgba(255,0,0,0.6);
}
</style>
</head>

<body>
<div class="td" style="width:1060px;height:20px"></div>
<div class="td" style="width:50px;height:20px">中央</div>
<div class="td" style="width:50px;height:20px">縣市</div>
<div class="td" style="width:50px;height:20px">學校</div>
<div style="clear:both"></div>
<form action="competence/save" method="post">
	<input type="submit" value="存檔" />
<?
$doc = new DOMDocument();
$question = DB::table('question')->where('CID','51100')->select('QID','question_label','qtree_level','qtype','qtree_parent','spss_name','competence')->get();
foreach($question as $q){
	//echo '<tr>';
	//echo '<td>'.$q->QID.'</td>';	
	//echo '<td>'.$q->question_label.'</td>';	
	//echo '<td><input type="checkbox" /></td>';	
	//echo '<td><input type="checkbox" /></td>';
	//echo '<td><input type="checkbox" /></td>';
	//echo '</tr>';
	$n_competence = ('100' & $q->competence)=='100';
	$c_competence = ('010' & $q->competence)=='010';
	$s_competence = ('001' & $q->competence)=='001';

	if( $q->qtree_level==0 ){	
		
		$label = $doc->createElement( 'div', $q->question_label==''?'&nbsp;':$q->question_label );
		$label->setAttribute( "style", 'border-bottom:1px solid #000;width:1000px;float:left' );
		
		$div = $doc->createElement( 'div', '' );
		$div->setAttribute( "style", 'width:1200px;margin:10px' );
		$div->setAttribute( "xml:id", $q->spss_name );
					
		$div_checkbox = $doc->createElement( 'div', '' );
		$div_checkbox->setAttribute( "style", 'float:right;width:150px' );
		if( $q->qtype=='head' )
		$div_checkbox->setAttribute( "class", 'head' );
		
		$checkbox1 = $doc->createElement( 'input', '' );
		$checkbox1->setAttribute( "type", 'checkbox' );
		$checkbox1->setAttribute( "name", $q->QID.'_n' );
		$checkbox1->setAttribute( "value", '1' );
		$checkbox1->setAttribute( "target", 'n' );
		if( $q->competence=='' || $n_competence )
		$checkbox1->setAttribute( "checked", 'checked' );
		$checkbox1->setAttribute( "style", 'float:left;width:50px;margin:0' );
		$checkbox2 = $doc->createElement( 'input', '' );
		$checkbox2->setAttribute( "type", 'checkbox' );	
		$checkbox2->setAttribute( "name", $q->QID.'_c' );
		$checkbox2->setAttribute( "value", '1' );
		$checkbox2->setAttribute( "target", 'c' );
		if( $q->competence=='' || $c_competence )
		$checkbox2->setAttribute( "checked", 'checked' );
		$checkbox2->setAttribute( "style", 'float:left;width:50px;margin:0' );
		$checkbox3 = $doc->createElement( 'input', '' );
		$checkbox3->setAttribute( "type", 'checkbox' );	
		$checkbox3->setAttribute( "name", $q->QID.'_s' );
		$checkbox3->setAttribute( "value", '1' );
		$checkbox3->setAttribute( "target", 's' );
		if( $q->competence=='' || $s_competence )
		$checkbox3->setAttribute( "checked", 'checked' );
		$checkbox3->setAttribute( "style", 'float:left;width:50px;margin:0' );
		$div_checkbox->appendChild($checkbox1);
		$div_checkbox->appendChild($checkbox2);
		$div_checkbox->appendChild($checkbox3);
			
		$div->appendChild($label);
		
		//if( $q->qtype!=='head' )
		$div->appendChild($div_checkbox);
		
			
		$div_clear = $doc->createElement( 'div', '' );
		$div_clear->setAttribute( "style", 'clear:both' );
		$div->appendChild($div_clear);
		
		$doc->appendChild($div);
	}
	if( $q->qtree_level>=1 ){
		$label = $doc->createElement( 'div', $q->question_label==''?'&nbsp;':$q->question_label );
		$label->setAttribute( "style", 'border-bottom:1px solid #000;width:'.(1000-50*$q->qtree_level).'px;float:left' );
		
		$div = $doc->createElement( 'div', '' );
		$div->setAttribute( "style", 'width:'.(1200-50*$q->qtree_level).'px;margin:10px;margin-left:50px' );
		$div->setAttribute("xml:id", $q->spss_name);
		
		$div_checkbox = $doc->createElement( 'div', '' );
		$div_checkbox->setAttribute( "style", 'float:right;width:150px' );
		if( $q->qtype=='head' )
		$div_checkbox->setAttribute( "class", 'head' );
		
		$checkbox1 = $doc->createElement( 'input', '' );
		$checkbox1->setAttribute( "type", 'checkbox' );
		$checkbox1->setAttribute( "name", $q->QID.'_n' );
		$checkbox1->setAttribute( "value", '1' );
		$checkbox1->setAttribute( "target", 'n' );
		if( $q->competence=='' || $n_competence )
		$checkbox1->setAttribute( "checked", 'checked' );
		$checkbox1->setAttribute( "style", 'float:left;width:50px;margin:0' );
		$checkbox2 = $doc->createElement( 'input', '' );
		$checkbox2->setAttribute( "type", 'checkbox' );	
		$checkbox2->setAttribute( "name", $q->QID.'_c' );
		$checkbox2->setAttribute( "value", '1' );
		$checkbox2->setAttribute( "target", 'c' );
		if( $q->competence=='' || $c_competence )
		$checkbox2->setAttribute( "checked", 'checked' );
		$checkbox2->setAttribute( "style", 'float:left;width:50px;margin:0' );
		$checkbox3 = $doc->createElement( 'input', '' );
		$checkbox3->setAttribute( "type", 'checkbox' );	
		$checkbox3->setAttribute( "name", $q->QID.'_s' );
		$checkbox3->setAttribute( "value", '1' );
		$checkbox3->setAttribute( "target", 's' );
		if( $q->competence=='' || $s_competence )
		$checkbox3->setAttribute( "checked", 'checked' );
		$checkbox3->setAttribute( "style", 'float:left;width:50px;margin:0' );
		$div_checkbox->appendChild($checkbox1);
		$div_checkbox->appendChild($checkbox2);
		$div_checkbox->appendChild($checkbox3);
			
		$div->appendChild($label);
		//if( $q->qtype!=='head' )
		$div->appendChild($div_checkbox);
		
		$parent = $doc->getElementById($q->qtree_parent);
		if( is_object($parent) ){
			
			$div_clear = $doc->createElement( 'div', '' );
			$div_clear->setAttribute( "style", 'clear:both' );
			$div->appendChild($div_clear);
		
			$parent->appendChild($div);
		}else{
			
		}
	}
}
echo $doc->saveHTML();
?>
</form>
</body>
</html>