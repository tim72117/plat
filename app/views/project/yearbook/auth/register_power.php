<?php

$organizations = Project\Yearbook\User::find(Auth::user()->id)->works->unique()->lists('sch_id');

$member->organizations()->attach($organizations);
