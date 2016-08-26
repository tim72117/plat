<?php

return [

    'citys' => function() {

        $citys = DB::table('plat_public.dbo.lists')->where('type', 'city')->select('name', 'code')->orderBy('sort')->get();

        return ['citys' => $citys];
    },

    'schools' => function() {

        if (Input::get('position') == 1) {
            $schools = DB::table('plat.dbo.organizations AS organizations')
                ->leftJoin('plat.dbo.organization_details AS detial', 'organizations.id', '=', 'detial.organization_id')
                ->where('detial.citycode', Input::get('city_code'))
                ->whereIn('detial.grade', [0, 1])
                ->select('organizations.id', 'detial.name')
                ->orderBy('detial.year', 'desc')
                ->get();
        }

        if (Input::get('position') == 2) {
            $schools = DB::table('plat.dbo.organizations AS organizations')
                ->leftJoin('plat.dbo.organization_details AS detial', 'organizations.id', '=', 'detial.organization_id')
                ->where('detial.citycode', Input::get('city_code'))
                ->whereIn('detial.grade', [3, 4, 5, 6, 7, 8, 'K', 'W', 'X', 'Y', 'Z', 'M', 'S'])
                ->select('organizations.id', 'detial.name')
                ->orderBy('detial.year', 'desc')
                ->orderBy('detial.grade', 'desc')
                ->get();
        }

        return ['schools' => $schools];

    },

    'positions' => function($project) {

        return ['positions' => $project->positions];
    },

];