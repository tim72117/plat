<?php

$positions = array_keys(array_filter(Input::get('user.positions')));

count($positions) > 0 && $user->positions()->attach($positions);

$member->organizations()->attach(Input::get('user.work.organization_id'));
