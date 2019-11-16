<template>
    <div>
        <div id="plotly-container" style="position: relative; height:70vh;" class="m-2"></div>
        <div>
            <input type="checkbox" id="spline-checkbox" v-model="spline" @change="refresh">
            <label for="spline-checkbox">spline = {{spline}}</label>

            <input class="ml-5" type="checkbox" id="toString-checkbox" v-model="toString" @change="refresh">
            <label for="toString-checkbox">toString = {{toString}}</label>

            <input class="ml-5" type="checkbox" id="timestampsRepetidos-checkbox" v-model="timestampsRepetidos" @change="refresh">
            <label for="timestampsRepetidos-checkbox">timestampsRepetidos = {{timestampsRepetidos}}</label>
        </div>
    </div>
</template>

<script>
    import Chart from 'chart.js';
    export default {

        name: "plotly",
        data: function(){
            return {
                spline: false,
                toString: false,
                timestampsRepetidos: false,

                data: undefined,
                timestamps: undefined,
                yAxisData: undefined,
                xAxisData: undefined,
                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,
            }
        },
        mounted() {

            console.log('Component mounted.')

            axios.get(myUrl + '/api/readings', ).
            then( success => {
                console.log(this)
                this.data = success.data;
                console.log(this.data);

                this.yAxisData = [];
                this.xAxisData = [];
                this.timestamps = [];

                success.data.forEach(element => {
                    //if(true){
                    if(!this.timestamps.includes(element.time) && !this.timestampsRepetidos){
                        this.yAxisData.push(element.v1);

                        if (this.yMax < element.v1) this.yMax = element.v1;

                        if (this.yMin > element.v1) this.yMin = element.v1;
                        
                        //this.xAxisData.push(new Date(element.time*1000))
                        //this.xAxisData.push(new Date(element.time*1000).toLocaleTimeString())

                        this.xAxisData.push(
                            (this.toString) ? new Date(element.time*1000).toLocaleTimeString() : new Date(element.time*1000)
                        )

                        this.timestamps.push(element.time);
                    }
                });

                this.setupGraph();
            });
            
        },
        methods:{
            setupGraph(){
                let shape = (this.spline) ? 'spline' : 'linear'
                let nticks = (this.toString) ? 10 : 0

                var trace = {
                    x: this.xAxisData,
                    y: this.yAxisData,
                    mode: 'lines',
                    name: 'voltagem',
                    line: {shape: shape},
                };
                var data = [ trace ];
                var layout = {
                    title:'voltagem registada',
                    xaxis:{
                        nticks: nticks,
                    }
                    };
                Plotly.newPlot('plotly-container', data, layout, {responsive: true});
                /*
                let TESTER = document.getElementById('plotly-container');
                Plotly.plot( 
                    TESTER, [{
                        x: this.xAxisData,
                        y: this.yAxisData,
                        mode: 'lines',
                        name: 'voltagem' }], 
                        {margin: { t: 0 } },
                        { title:'Adding Names to Line and Scatter Plot'}
                );
                */
            },
            refresh(){
                console.log('refresh!')

                this.yAxisData = [];
                this.xAxisData = [];
                this.timestamps = [];

                this.data.forEach(element => {
                    //if(true){
                    if(!this.timestamps.includes(element.time)){
                        this.yAxisData.push(element.v1);

                        if (this.yMax < element.v1) this.yMax = element.v1;

                        if (this.yMin > element.v1) this.yMin = element.v1;
                        
                        this.xAxisData.push(
                            (this.toString) ? new Date(element.time*1000).toLocaleTimeString() : new Date(element.time*1000)
                        )
                        if(!this.timestampsRepetidos){
                            this.timestamps.push(element.time);
                        }   
                    }
                });

                this.setupGraph();

            }    
        }
    }
</script>