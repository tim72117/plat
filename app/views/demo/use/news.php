	<tr>
		<td class="header2" align="center">資訊公告</td>
  	</tr>  	
	<tr>
    	<td class="intd" align="center">
		<? foreach ($sql_post as $sql_str) {
			echo html_entity_decode($sql_str->news, ENT_COMPAT);
			echo $sql_str->date;
			echo '<hr>'; 
		} ?>
        </td>
    </tr>
    <tr>
    	<td class="intd" align="center">
		<? foreach ($sql_note as $sql_str) {
			echo html_entity_decode($sql_str->news, ENT_COMPAT);
			echo $sql_str->date;
			echo '<hr>'; 
		} ?>
        </td>
    </tr>