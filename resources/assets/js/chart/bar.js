var bar = {
    chart: {
        type: 'bar',
        marginTop: 20
    },
    colors: ["#7cb5ec", "#90ed7d", "#f7a35c", "#8085e9", "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#698b22",
                      "#8b795e", "#8b8378", "#458b74", "#838b8b", "#00008b", "#cd3333", "#7ac5cd", "#66cd00", "#ee7621", "#ff7256",
                      "#cdc8b1", "#bcee68", "#9bcd9b"],
    title:{
        text: ''
    },
    tooltip: {
        formatter: function() {
            return '次數: '+this.point.val;
        }
    },
    legend : {
        title: {
            text: ''
        }
    },
    xAxis: {
        categories: [],
        labels: {
            x: 20,
            align: 'left'
        },
        title: {
            align: 'high',
            offset: 0,
            rotation: 0,
            style:{
                fontWeight: 'bold'
            },
            x: 0,
            y: -10
        }

    },
    yAxis: {
        min: 0,
        max: 100,
        title: {
            text: '百分比',
        },
        labels: {
            formatter: function() {
                return (this.isLast ? this.value + '%' : this.value);
            }
        }
    },
    plotOptions: {
        bar: {
            maxPointWidth: 30,
            dataLabels: {
                enabled: false
            }
        }
    },
    credits: {
        enabled: false
    },
    series: []
};