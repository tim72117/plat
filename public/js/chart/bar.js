var bar = {
    chart: {
        type: 'bar'
    },
    title:{
        text: ''
    },
    subtitle: {
        text: [],
        align: 'center',
        style:{
            //fontSize: '14px',
            fontWeight: 'bold',
            color: '#333333'
        },
        
        y: 380
    },
    xAxis: {
        categories: [],
        labels: {
            x: 20,
            align: 'left'
        },
        title: {
            text: [],
            align: 'low',
            style:{
                //fontSize: '14px',
                fontWeight: 'bold',
                color: '#333333'
            },
            x: -10,
        }
                
    },
    yAxis: {
        min: 0,
        title: {
            text: '次數',            
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