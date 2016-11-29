<?php

return [
    'citysAndSchools' => function() {
        $citys = DB::table('plat_public.dbo.lists')->where('type', 'city')->select('name', 'code')->orderBy('sort')->get();

        $schools = DB::table('plat.dbo.organizations AS organizations')
            ->leftJoin('plat.dbo.organization_details AS detial', 'organizations.id', '=', 'detial.organization_id')
            ->whereIn('detial.grade', [0, 1])
            ->select('organizations.id', 'detial.id AS code', 'detial.name', 'detial.cityname')
            ->orderBy('detial.year', 'desc')
            ->get();

        return ['citys' => $citys, 'schools' => $schools];
    }
];