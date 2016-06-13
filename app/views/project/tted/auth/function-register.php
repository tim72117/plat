<?php

return [

    'citys' => function() {

        $citys = DB::table('plat_public.dbo.lists')
            ->where('type', 'city')
            ->select('name', 'code')
            ->get();

        return ['citys' => $citys];
    },

    'schools' => function() {

        if (Input::get('position') == 1) {
            $schools = DB::table('plat_public.dbo.university_school')
                ->where('year', 103)
                ->where('citycode', Input::get('city_code'))
                ->select('id', 'name')
                ->get();
        }

        if (Input::get('position') == 2) {
            $schools = DB::table('plat_public.dbo.row_20151023_200635_34isc')
                ->where('C409', Input::get('city_code'))
                ->select('C413 AS id', 'C414 AS name')
                ->get();
        }

        return ['schools' => $schools];

    },

    'positions' => function($project) {

        return ['positions' => $project->positions];
    },

];