<?php

$member->organizations()->attach(Input::get('user.work.organization_id'));

$user->positions()->attach(Input::get('user.work.position'));
