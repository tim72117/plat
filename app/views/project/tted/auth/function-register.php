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
            $subQuery = DB::table('rows.dbo.row_20160629_171704_qe7nn')
                ->select(DB::raw('C1181,C1187,C1188,ROW_NUMBER() Over (Partition By C1187 Order by C1183 desc) AS sort'));

            $schools = DB::table(DB::raw("({$subQuery->toSql()}) AS tmp"))
                ->mergeBindings($subQuery)
                ->where('tmp.sort',1)
                ->where('tmp.C1181', Input::get('city_name'))
                ->select('tmp.C1187 AS id', 'tmp.C1188 AS name')
                ->orderBy('tmp.C1187')
                ->get();
        }

        return ['schools' => $schools];

    },

    'positions' => function($project) {

        return ['positions' => $project->positions];
    },

];