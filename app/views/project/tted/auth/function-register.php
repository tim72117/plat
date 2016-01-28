<?php

return [

    'departments' => function() {

        $citys = DB::table('public.dbo.university_school')
            ->where('year', 103)
            ->whereNotNull('cityname')
            ->groupBy('cityname')
            ->select('cityname')
            ->get();

        $schools = DB::table('public.dbo.university_school')
            ->where('year', 103)
            ->orderBy('cityname', 'ASC', 'id')
            ->groupBy('cityname', 'name', 'id', 'type')
            ->select('id', 'name', 'type', 'cityname')
            ->get();

        return ['citys' => $citys, 'schools' => $schools];
    },

];