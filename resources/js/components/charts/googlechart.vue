<template>
    <div id="linechart" style="position: relative; height:70vh;" class="m-2"></div>
</template>

<script>
    export default {

        name: "google",
        mounted() {

            console.log('Component mounted.')

            axios.get(myUrl + '/api/readings', ).
            then( success => {
                console.log(this)
                this.data = success.data;
                console.log(this.data);

                this.rows = [];

                let a = 0
            
                success.data.forEach(element => {
                    if (true){
                    //if(a<5){
                        let temp = [];

                        temp.push(new Date(element.time*1000));
                        temp.push(element.v1);

                        this.rows.push(temp);

                        /*
                        this.yAxisData.push(element.v1)
                        this.xAxisData.push(new Date(element.time*1000).toLocaleTimeString())
                        */
                        a++
                        }
                    });

                //this.setupMaterialGraph();
                this.setupGraph();
            });
            
        },
        data: function(){
            return {
                data: undefined,
                rows: undefined,
            }
        },
        methods:{
            setupGraph(){
                google.charts.load('current', {'packages':['corechart']});
                google.charts.setOnLoadCallback(this.drawChart);
            },
            drawChart(){
                var data = new google.visualization.DataTable();
                
                data.addColumn('date', 'Timestamp');
                data.addColumn('number', 'Voltagem');
                
                data.addRows(this.rows);
                var options = {
                    title: 'voltagem',
                    curveType: 'function',
                    subtitle: 'em volts',
                    explorer: { actions: ['dragToZoom', 'rightClickToReset'], axis: 'horizontal' }
                };

                var chart = new google.visualization.LineChart(document.getElementById('linechart'));

                chart.draw(data, options);
            },
            setupMaterialGraph(){
                google.charts.load('current', {'packages':['line']});
                google.charts.setOnLoadCallback(this.drawMaterialChart);
            },
            drawMaterialChart() {
                var data = new google.visualization.DataTable();   
                
                data.addColumn('date', 'Timestamp');
                data.addColumn('number', 'Voltagem');
                
                data.addRows(this.rows);
                var options = {
                    chart: {
                        curveType: 'function',
                        title: 'voltagem',
                        subtitle: 'em volts',
                    },
                    /*
                    width: 1000,
                    height: 650*/
                };

                var chart = new google.charts.Line(document.getElementById('linechart_material'));

                chart.draw(data, google.charts.Line.convertOptions(options));
            }
        }
    }
</script>