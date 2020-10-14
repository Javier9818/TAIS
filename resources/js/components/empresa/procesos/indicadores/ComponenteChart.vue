<template>
  <section>
      <canvas id="myChart" width="400" height="400"></canvas>
  </section>
</template>

<script>
  import Chart from 'chart.js';
  export default {
    props:['dataChart'],
    data() {
      return {
        
      }
    },
    methods: {
        
    },
    mounted(){
        let { dataChart } = this
        var ctx = document.getElementById('myChart');
        var mixedChart = new Chart(ctx, {
            type: 'bar',
            data: {
                datasets: [
                    {
                        label: 'Indicador',
                        data: dataChart.map((e,i) => { 
                            if(!isNaN(e.value))
                                return parseFloat(e.value)                            
                        }),
                        // this dataset is drawn below
                        order: 2,
                        backgroundColor: dataChart.map((e,i) => { 
                            if(!isNaN(e.value))
                                return  i%2 === 0 ? 'rgba(255, 95, 15, 1)': 'rgba(19, 15, 255, 1)'                       
                        }),
                    }, 
                    {
                        label: 'Linea base',
                        data: dataChart.map((e,i) => { 
                            if(!isNaN(e.value))
                                return parseFloat(e.lineaBase)                            
                        }),
                        borderColor: [
                            'rgba(255, 15, 15, 1)'
                        ],
                        type: 'line',
                        // this dataset is drawn on top
                        order: 1
                    }
                ],
                labels: dataChart.map((e,i) => { 
                            if(!isNaN(e.value))
                                return e.month                            
                        })
            },
            options: {}
        });
    }
  }
</script>
