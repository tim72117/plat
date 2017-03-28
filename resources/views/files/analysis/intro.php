<?php
$doc = ShareFile::with('isFile')->whereHas('isFile', function($query) {
    $query->where('files.type', 7);
})->where(function($query) {
    $inGroups = Auth::user()->inGroups->pluck('id');
    $query->where('target', 'group')->whereIn('target_id', $inGroups);
})->first();

if ($doc) {
    $fileProvider = app\library\files\v0\FileProvider::make();
    $intent_key = $fileProvider->doc_intent_key('open', $doc->id, 'app\\library\\files\\v0\\AnalysisFile');
    ?>
    <script type="text/javascript">
    $(document).ready(function() {
        location.replace('/file/<?=$intent_key?>/open');
    });
    </script>
    <?php
}
?>



<div class="ui loading segment" style="min-height:500px">載入中</div>