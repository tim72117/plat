
<div ng-cloak ng-controller="internController">

    <div class="ui basic segment" ng-class="{loading: loading}">

        <table class="ui collapsing celled structured table" >
            <thead>
                <tr>
                    <th colspan="2">實習時間</th>
					<th ng-repeat="internYear in internYears">{{internYear}}</th>
                </tr>
            </thead>
            <tbody>
            	<tr>
                    <td colspan="2">實習人數</td>
                    <td id="{{ ::$id }}" ng-repeat="internYear in internYears" ng-click="$event.stopPropagation();popupPie('實習人數',$id,internYear,internYear,0,internData[internYear].allIntern,$event)">{{internData[internYear].allIntern}}</td>
                </tr>
                <tr>
                    <td rowspan="17">師資歷程</td>
                    <td>100年報考教檢</td>
<!--                     <td ng-repeat="internYear in internYears" ng-click="drawpie2()" id="{{ ::$id }}">{{internData[internYear]['100'][0]}}</td> -->
                    <td ng-repeat="internYear in internYears" id="{{ ::$id }}" ng-click="$event.stopPropagation();popupPie('報考教檢',$id,100,internYear,1,internData[internYear]['100'][0],$event)">{{internData[internYear]['100'][0]}}</td>
                </tr>
                <tr ng-repeat-start="processYear in processYears">
                    <td ng-if="processYear>100">{{processYear}}年報考教檢</td>
                    <td ng-if="processYear>100" ng-repeat="internYear in internYears" id="{{ ::$id }}" ng-click="$event.stopPropagation();popupPie('報考教檢',$id,processYear,internYear,1,internData[internYear][processYear][0],$event)">{{internData[internYear][processYear][0]}}</td>
                </tr>
                <tr>
                    <td>{{processYear}}年報考教檢並通過</td>
                    <td ng-repeat="internYear in internYears" id="{{ ::$id }}" ng-click="$event.stopPropagation();popupPie('報考教檢並通過',$id,processYear,internYear,2,internData[internYear][processYear][1],$event)">{{internData[internYear][processYear][1]}}</td>
                </tr>
                <tr>
                    <td>{{processYear}}年首次任教</td>
                    <td ng-repeat="internYear in internYears" id="{{ ::$id }}" ng-click="$event.stopPropagation();popupPie('首次任教',$id,processYear,internYear,3,internData[internYear][processYear][2],$event)">{{internData[internYear][processYear][2]}}</td>
                </tr>
                <tr ng-repeat-end="processYear in processYears">
                    <td>截至{{processYear}}年任教情況</td>
                    <td ng-repeat="internYear in internYears" id="{{ ::$id }}" ng-click="$event.stopPropagation();popupPie('任教情況',$id,processYear,internYear,4,internData[internYear][processYear][3],$event)">{{internData[internYear][processYear][3]}}</td>
                </tr>
                <!-- <tr>
                    <td colspan="2">流失人數/比例</td>
                    <td ng-repeat="internYear in internYears"></td>
                </tr> -->
            </tbody>
        </table>
    </div>
</div>
<script src="/js/jquery-ui/1.11.4/jquery-ui.min.js"></script>
<script src="/js/Highcharts-4.1.8/js/highcharts.js"></script>
<script src="/js/chart/struct/pie-intern.js"></script>
<!--<script src="/js/chart/struct/donut-intern.js"></script>-->
<link rel="stylesheet" href="/js/jquery-ui/1.11.4/jquery-ui.min.css" />

<script>
app.controller('internController', function($scope, $http, $filter, $timeout, $document) {
	$scope.processYears = ['100','101','102','103'];
	$scope.internYears = ['991','992','1001','1002','1011','1012','1021','1022','1031'];

	/*$scope.internData = {
		'991': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'992': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1001': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1002': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1011': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1012': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1021': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1022': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]},
		'1031': {allIntern: '5306', '100':[5144,4006,2670,2670],'101':[817,341,218,2888],'102':[450,98,55,2943],'103':[288,67,36,2979]}
	};*/

    //test data
    /*$scope.frequence = {
        '男大':1000,'男研':700,'女大':2000,'女研':1606
    };
    /*$scope.frequence = {
        '無報考':162,'有報考':{'男大':950,'男研':650,'女大':1840,'女研':1604}
    };*/
    /*$scope.frequence = {
        '無報考':162,'未通過':1138,'通過':{'男大':900,'男研':650,'女大':1700,'女研':756}
    };*/
    /*$scope.frequence = {
        '未取證':1300,'非任教':1336,'任教':{'正式':{'男大':400,'男研':85,'女大':500,'女研':350},'公校代理':{'男大':500,'男研':350,'女大':400,'女研':85}}
    };*/
    $scope.getInternPieData = function(intern_year, type_key) {
        /*( requestForCount = countService.getCount('get_intern_piedata', {process_year: process_year, type_key: type_key}) ).then(
            function( newResoult ) {*/
                var frequence = {'男大':1000,'男研':700,'女大':2000,'女研':1606};
                return frequence;
                //return newResoult;
            /*},
            function( errorMessage ) {
                console.warn( "Request for pie data was rejected." );
                console.info( "Error:", errorMessage );
            }
        );*/
    };
    $scope.getPieData = function(process_year, intern_year, type_key) {
        /*( requestForCount = countService.getCount('get_piedata', {process_year: process_year, intern_year: intern_year, type_key: type_key}) ).then(
            function( newResoult ) {*/
                if (type_key==1) {
                    var frequence = {'無報考':162,'有報考':{'男大':950,'男研':650,'女大':1840,'女研':1604}};
                } else if (type_key==2) {
                    var frequence = {'無報考':162,'報考未通過':2222,'報考通過':{'男大':950,'男研':650,'女大':1840,'女研':1604}};
                } else {
                    var frequence = {'未取證':1300,'非任教':1336,'任教':{'正式':{'男大':400,'男研':85,'女大':500,'女研':350},'公校代理':{'男大':500,'男研':350,'女大':400,'女研':85}}};
                }
                return frequence;
                //return newResoult;
            /*},
            function( errorMessage ) {
                console.warn( "Request for pie data was rejected." );
                console.info( "Error:", errorMessage );
            }
        );*/
    };

    $scope.drawPie = function(target,pieData) {
        //var pieData = $scope.frequence;
        var pie_size = 250;
        //pie.series = [];
        var series = {
            type:'pie',
            name: target,
            colorByPoint: true,
            data: [],
            size: pie_size,
            showInLegend: true,
            dataLabels: {
                enabled: true,
            },
        };

        for(var j in pieData) {
            series.data.push({
                name: j,
                y: pieData[j] | 0
            });
        }

        //pie.series.push(series);
        //pie.title.text = target;
        //area.highcharts(pie);
        return(series);

    };

    $scope.drawCrossPie = function(pieData) {
        //donut.series=[];
        var column_series = [];
        var row_series = [];
        for(var i in pieData){
            var temp_total = 0;
            //if number
            if(!isNaN(pieData[i])){
                column_series.push({name: i, y: pieData[i]});
                row_series.push({name: i, y: pieData[i]});
            }else{
                for(var j in pieData[i]){
                    row_series.push({name: j, y: pieData[i][j]});
                    temp_total = temp_total+pieData[i][j]*1.0;
                }
                column_series.push({name: i, y: temp_total});
            }
        }

        var series=[{
                name: '欄變數',
                data: column_series,
                showInLegend: true,
                size: '60%',
                dataLabels: {
                    formatter: function () {
                        var new_name = this.point.name;
                        if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                        return this.y > 5 ? new_name  : null;
                    },
                color: '#ffffff',
                distance: -50
                }

            },{
                name: '列變數',
                data: row_series,
                showInLegend: true,
                size: '80%',
                innerSize: '60%',
                dataLabels: {
                    formatter: function () {
                        // display only if larger than 1
                        var new_name = this.point.name;
                        if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                        return this.y > 1 ? '<b>' + new_name + ':</b> ' + this.y + '%' : null;
                    }
                }
            }];
        return series;
    };

    $scope.drawThreePie = function(pieData) {
        var first_series = [];
        var second_series = [];
        var third_series = [];
        for(var i in pieData){
            var temp_total_second = 0;
            //if number
            if(!isNaN(pieData[i])){
                first_series.push({name: i, y: pieData[i]});
                second_series.push({name: i, y: pieData[i]});
                third_series.push({name: i, y: pieData[i]});
            }else{
                for(var j in pieData[i]){
                    var temp_total_third = 0;
                    if(!isNaN(pieData[i])){
                        second_series.push({name: j, y: pieData[i][j]});
                        third_series.push({name: j, y: pieData[i][j]});
                        temp_total_second = temp_total_second+pieData[i][j]*1.0;
                    }else{
                        for(var k in pieData[i][j]){
                            third_series.push({name: k, y: pieData[i][j][k]});
                            temp_total_second = temp_total_second+pieData[i][j][k]*1.0;
                            temp_total_third = temp_total_third+pieData[i][j][k]*1.0;
                        }
                        second_series.push({name: j, y: temp_total_third});
                    }
                }
                first_series.push({name: i, y: temp_total_second});
            }
        }
        var series=[{
                name: '第一層',
                data: first_series,
                showInLegend: true,
                size: '40%',
                dataLabels: {
                    formatter: function () {
                        var new_name = this.point.name;
                        if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                        return this.y > 5 ? new_name  : null;
                    },
                color: '#ffffff',
                distance: -30
                }

            },{
                name: '第二層',
                data: second_series,
                showInLegend: true,
                size: '80%',
                innerSize: '40%',
                dataLabels: {
                    formatter: function () {
                        // display only if larger than 1
                        var new_name = this.point.name;
                        if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                        return this.y > 1 ? '<b>' + new_name + ':</b> ' + this.y + '%' : null;
                    },
                color: '#AAAAAA',
                shadow: false,
                distance: -40
                }
            },{
                name: '第三層',
                data: third_series,
                showInLegend: true,
                size: '90%',
                innerSize: '80%',
                dataLabels: {
                    formatter: function () {
                        // display only if larger than 1
                        var new_name = this.point.name;
                        if(new_name.length>7) new_name = new_name.substring(0,7) + '...';
                        return this.y > 1 ? '<b>' + new_name + ':</b> ' + this.y + '%' : null;
                    }
                }
            }];
        return series;
    }

    //$scope.popupPie = function(target,id,process_year,intern_year,type_key) {

    $scope.popupPie = function(target,id,process_year,intern_year,type_key,box_value,event) {
        if (box_value <= 0 || box_value == undefined) {
            return false;
        }
        var popup = $('#pie-container');
        if ($scope.place) {
            $scope.place.popup('destroy');
            pie.series=[];
            //popup.parent().popup('destroy');
        };

        var place = $(event.target);
        $scope.place = place;
        console.log(place);
        var html = $('<div id="pie-container"></div>');
        var data = {'intern_year':intern_year,'process_year':process_year,'type_key':type_key};

        $http({method: 'POST', url: 'get_intern_detail', data:{data:data}})
            .success(function(data, status, headers, config) {
                console.log(data);
                if (type_key==0) {
                    pie.series.push($scope.drawPie(target,data));
                } else if(type_key==1){
                    pie.series = $scope.drawCrossPie(data);
                } else {
                    pie.series = $scope.drawThreePie(data);
                }
                pie.title.text = target;
                html.highcharts(pie);
                $(place).popup({
                    target:    $(place),
                    position: 'right center',
                    on:       'click',
                    html:  html
                });
                $(place).popup('show');
                $document.on('click', function() {
                    $(place).popup('destroy');
                });
            }).error(function(e){
                console.log(e);
        }); return;
        //html.highcharts($scope.drawPie(target,$scope.frequence));
        //pie.series = $scope.drawCrossPie(target,$scope.frequence);
        /*if (type_key==0){
            pie.series.push($scope.drawPie(target,$scope.getInternPieData(intern_year,type_key)));
        }else if(type_key==1 || type_key==2){
            pie.series = $scope.drawCrossPie($scope.getPieData(process_year,intern_year,type_key));
        }else{
            pie.series = $scope.drawThreePie($scope.getPieData(process_year,intern_year,type_key));
        }*/
        /*pie.title.text = target;
        html.highcharts(pie);
        $(place).popup({
            target:    $(place),
            position: 'right center',
            on:       'click',
            html:  html
        });
        $(place).popup('show');
        $document.on('click', function() {
            $(place).popup('destroy');
        });*/
    };

    $scope.getCount = function() {
        $scope.loading = true;
        $http({method: 'POST', url: 'get_intern_count', data:{internYears:$scope.internYears}})
           .success(function(data, status, headers, config) {
                console.log(data);
               $scope.internData = data;
               $scope.loading = false;
           }).error(function(e){
               console.log(e);
           });
    };

    $scope.getCount();
})
</script>