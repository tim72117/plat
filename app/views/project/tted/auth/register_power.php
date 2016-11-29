<?php

$organizations = Plat\Member::where('user_id', $member->user_id)->where('project_id', $project->id)->first()->organizations;

$member->organizations()->sync($organizations->lists('id'));
