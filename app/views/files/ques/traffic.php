
<div ng-controller="trafficController">
    <div id="container"></div>
</div>
<script src="/js/highstock.js"></script>

<script>
app.controller('trafficController', function($scope, $http, $filter) {

    $scope.solve = function(report) {
        $http({method: 'POST', url: 'getTraffic', data:{}})
        .then(function(response) {
            draw(response.data);
        });
    };
    $scope.solve();

    function draw(data) {
        Highcharts.stockChart('container', {
            xAxis: {
                type: 'datetime',
                labels: {
                    format: '{value:%Y-%m-%d}'
                },
                tickInterval: 3600 * 1000 * 24 * 2
            },
            yAxis: {
                min: 0,
                title: {
                    text: '回收數'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true,
                        style: {
                            textShadow: '0 0 3px white, 0 0 3px white'
                        }
                    },
                    enableMouseTracking: false
                }
            },
            legend: {
                enabled: true,
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'middle',
                borderWidth: 0
            },
            series: [{
                name: '填答完成',
                pointInterval: 1000 * 60 * 60 * 24,
                data: data.receives
            }]
        });
    }
});
</script>