<?php

return [

    'init' => function($project) {

        $citys = DB::table('plat_public.dbo.lists')->where('type', 'city')->select('name', 'code')->get();

        return ['citys' => $citys, 'positions' => $project->positions];
    },

    'schools' => function() {

        $schools = DB::table('plat.dbo.organizations AS organizations')
            ->leftJoin('plat.dbo.organization_details AS details', 'organizations.id', '=', 'details.organization_id')
            ->where(function($query) {
                $query->whereIn('details.grade', [2, 3, 4, 'B', 'C'])->orWhereNull('details.grade');
            })
            ->where('details.citycode', Input::get('city_code'))
            ->select('organizations.id', 'details.name', 'details.sysname')
            ->orderBy('details.year', 'desc')
            ->get();

        return ['schools' => $schools];

    },

];