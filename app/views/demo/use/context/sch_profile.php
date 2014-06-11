
<style>
.sch-profile td {
    border-bottom: 1px solid #999;
}    
</style>
<table cellpadding="3" cellspacing="0" border="0" width="1200" class="sch-profile" style="margin:10px 0 0 10px">
    <tr>
        <th>編號</th>
        <th>學校</th>
        <th>姓名</th>
        <th>職稱</th>
        <th width="150">電話</th>
        <th width="100">傳真</th>
        <th width="30">學校人員</th>
        <th width="30">高一、專一新生</th>
        <th width="30">高二、專一學生</th>
        <th width="30">高二、專二導師</th>
        <th width="30">高二、專二家長</th>
        
    </tr>
<?
Config::set('demo.project', 'use');

//$group = Group::with('users.contact','users.schools')->find(1);

$group = Cache::remember('group', 5, function() {
    return Group::with('users.contact','users.schools')->find(1);
});

foreach($group->users as $user){
    
    echo '<tr>';
    echo '<td>'.$user->id.'</td>';
    echo '<td>';
    foreach($user->schools as $school){
        echo $school->sname;
    }
    echo '</td>';
    echo '<td>'.$user->username.'</td>';
    
    if( !is_null($user->contact) ){

    echo '<td>'.$user->contact->title.'</td>';
    echo '<td>'.$user->contact->tel.'</td>';
    echo '<td>'.$user->contact->fax.'</td>';
    echo '<td>'.$user->contact->schpeo.'</td>';
    echo '<td>'.$user->contact->senior1.'</td>';
    echo '<td>'.$user->contact->senior2.'</td>';
    echo '<td>'.$user->contact->tutor.'</td>';
    echo '<td>'.$user->contact->parent.'</td>';
        
    }else{
        echo '<td colspan="8"></td>';
    }

    echo '</tr>';

}

?>

    
</table>