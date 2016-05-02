<?php

return [

    'init' => function($project) {

        $citys = DB::table('plat_public.dbo.secondary_school')->where('year', 103)->groupBy('cityname')->select('cityname')->get();

        return ['citys' => $citys, 'positions' => $project->positions];
    },

    'departments' => function() {

        $departments = DB::table('plat_public.dbo.secondary_school')->where('year', 103)->where('cityname', Input::get('cityname'))->orderBy('schtype', 'desc')->get();

        return ['departments' => $departments];

    },

];