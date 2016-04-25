var pie = {
    chart: {
        type: 'pie',
        height:500,
    },
    colors: ["#7cb5ec", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#698b22",
                      "#8b795e", "#8b8378", "#458b74", "#838b8b", "#00008b", "#cd3333", "#7ac5cd", "#66cd00", "#ee7621", "#ff7256",
                      "#cdc8b1", "#bcee68", "#9bcd9b"],
    title: {
        text: ''
    },
    tooltip: {
        pointFormat: '{series.name}: <b>{point.y}</b>'
    },
     plotOptions: {
        pie: {
            allowPointSelect: true,
            cursor: 'pointer',
            dataLabels: {
                enabled: true,
                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                style: {
                    color: (Highcharts.theme && Highcharts.theme.contrastTextColor) || 'black'
                }
            }
        }
    },
    credits: {
        enabled: false
    },
    series: []
}