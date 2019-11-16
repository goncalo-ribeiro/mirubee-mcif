<template>
    <div class="chart-container" style="position: relative; height:70vh;">
        <!--
        <div id="chartjs" style="height:70vh" class="m-a">
            hi there
        </div>
        -->
        <canvas id="myChart"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js';
    export default {

        name: "chartjs",
        mounted() {

            console.log('Component mounted.')

            axios.get(myUrl + '/api/readings', ).
            then( success => {
                console.log(this)
                this.data = success.data;
                console.log(this.data);

                this.yAxisData = [];
                this.xAxisData = [];

                success.data.forEach(element => {
                    this.yAxisData.push(element.v1)

                    if (this.yMax < element.v1) this.yMax = element.v1;

                    if (this.yMin > element.v1) this.yMin = element.v1;
                    
                    this.xAxisData.push(new Date(element.time*1000).toLocaleTimeString())
                });

                this.setupGraph();
            });
            
        },
        data: function(){
            return {
                data: undefined,
                yAxisData: undefined,
                xAxisData: undefined,
                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,
            }
        },
        methods:{
            setupGraph(){
                var ctx = document.getElementById('myChart').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        responsive: true,
                        labels: this.xAxisData,
                        datasets: [{
                            label: 'voltagem',
                            data: this.yAxisData,
                            backgroundColor: 'rgba(255, 99, 132, 0.4)',
					        borderColor: 'rgba(255, 99, 132, 1)',
                        }]
                    },
                    options: {
                        maintainAspectRatio: false,
                        scales: {
                            xAxes:[{
                                autoSkip: true,
                            }],
                        }
                        
                    }
                });
            }
        }
    }
</script>