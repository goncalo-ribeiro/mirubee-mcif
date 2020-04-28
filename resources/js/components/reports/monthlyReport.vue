<template>
    <div>
        <h3 class="mb-2">
            {{month}} {{year}} Report
        </h3>
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="device">site</label>
                <select id="select-site-report" class="form-control" v-on:input="selectedSiteId = $event.target.value; getMonthlyReadings()">
                    <option selected="selected" disabled value="0">choose the desired site</option>
                    <option v-for="(site, index) in sites" :key="index" :value="site.id">{{site.name}}</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="parameter">unit</label>
                <select id="select-unit-report" class="form-control" v-on:input="selectedUnit = $event.target.value; setupSeriesandDrawGraph()">
                    <option v-for="(unit, index) in units" :key="index" :value="index" 
                        :disabled="isUnitDisabled(unit)">
                        {{unit.name}}
                    </option>
                </select>
            </div>
        </div>
        <div id="echart-report" style="height:60vh" class="m-a">
        </div>
    </div>
        <!--
        <router-link class="nav-link" to="/reports/">back</router-link>
        <router-link class="nav-link" :to="{ name: 'reportsList', params: { year: year, meses: monthsByYear[year]}}">back</router-link>
        -->

</template>

<script>
var moment = require('moment');
export default {
        name: "relatorio-mensal",
        mounted() {
            console.log('Component mounted.');
            this.getSites();
            this.setupEchartGraph();          
        },
        activated(){
            console.log('monthly report activated!');
            this.reset();
        },
        data: function(){
            return {
                readings: {},
                eChart: undefined,

                sites: [],
                selectedSiteId: 0,

                selectedUnit: 0,
                units: [
                    {name:'energy consumption (kWa)', nameDB:'e'},
                    {name:'consumption costs (€)', nameDB:'€'},
                    {name:'voltage (V)', nameDB:'v'},
                    {name:'current (A)', nameDB:'i'},
                    {name:'apparent power (kVA)', nameDB:'p'},
                    {name:'active power (kW)', nameDB:'a'},
                    // {name:'inductive reactive power', nameDB:'v'},      //?
                    // {name:'capacitive reactive power', nameDB:'v'},     //?
                    {name:'frequency (Hz)', nameDB:'q'},
                    {name:'power factor', nameDB:'f'},
                    // {name:'Harmonics A', nameDB:'v'},                   //?
                    // {name:'Harmonics V', nameDB:'v'},                   //?
                    // {name:'CO2', nameDB:'v'},                           //?
                    // {name:'Active Energy', nameDB:'v'},                 //?
                    // {name:'Reactive Energy', nameDB:'v'}                //?
                ],
                monthMapper: {'january': 0,'february': 1,'march': 2,'april': 3,'may': 4,'june': 5,'july ': 6,'august': 7,'september': 8,'october': 9,'november': 10,'december': 11},

                series: [],

                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,

                colors:['rgb(255, 70, 131)', 'rgb(70, 255, 131)', 'rgb(70, 131, 255)'],
            }
        },
        props:{
            month: String,
            year: Number, 
        },
        methods:{
            isUnitDisabled(unit){
                // console.log('isUnitDisabled', unit.nameDB, this.getSiteById(this.selectedSiteId));
                if(unit.nameDB == '€'){
                    if (this.getSiteById(this.selectedSiteId) != null){
                        if (this.getSiteById(this.selectedSiteId).tariff == null){
                            console.log('true', this.getSiteById(this.selectedSiteId)!= null, this.getSiteById(this.selectedSiteId).tariff != null)
                            return true;
                        }
                    }
                }
                return false;
            },

            getSiteById(siteId){
                for (let i = 0; i < this.sites.length; i++) {
                    
                    if (this.sites[i].id == siteId){
                        // console.log('return site', this.sites[i]);
                        return this.sites[i];
                    }
                }
                // console.log('return site null');
                return null;
            },

            reset(){
                $("#select-site-report").val("0").change();
                this.selectedSiteId = 0;
                this.selectedUnit = 0;
                $("#select-unit-report").val("0").change();

                this.clear();
            },
            getSites(){
                axios.get(myUrl+"/api/sites")//, { headers: { Accept: 'application/json'}})
                .then(response =>{
                    console.log(response);

                    this.sites = response.data.data;
                    this.readings = {};
                    this.sites.forEach(site => {
                        this.readings[site.name] = [];
                    });
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's site retrieval)", { icon : 'cancel', type: 'error'});
                })        
            },
            getMonthlyReadings(){
                let site = this.getSiteById(this.selectedSiteId);
                if(this.readings[site.name],length){
                    console.log('readings already retrieved for this site')
                    return;
                }

                let start = new Date(this.year, this.monthMapper[this.month], 1);
                let end = new Date(this.year, (this.monthMapper[this.month])+1, 0);

                console.log(start, end)
                console.log(start.getTime(), end.getTime());

                axios.get(myUrl + '/api/sites/' + this.selectedSiteId + '/readings/' + start.getTime()/1000 + '/' + end.getTime()/1000 )
                .then( success => {
                    this.readings[site.name] = success.data.readings;
                    
                    this.setupSeriesandDrawGraph();
                });
            },
            setupSeriesandDrawGraph(){
                console.log('setupSeriesandDrawGraph');
                if(this.selectedSiteId == 0){
                    return;
                }
                
                this.series = [];
                
                this.yMax = Number.NEGATIVE_INFINITY;
                this.yMin = Number.POSITIVE_INFINITY;

                let unit = this.units[this.selectedUnit].nameDB;

                if(unit == 'e'){
                    this.setupEnergyConsumptionSeries(unit);
                }else{
                    if (unit == '€'){
                        this.setupConsumptionCostsSeries(unit);
                    }else{
                        this.setupRegularSeries(unit);
                    }
                }

                // this.refreshEnergyConsumptionEchartSeries();
            },

            setupEnergyConsumptionSeries(unit){
                let site = this.getSiteById(this.selectedSiteId);
                let endDate = new Date(this.year, (this.monthMapper[this.month])+1, 0);
                let lastDayOfMonth = endDate.getDate();

                let consumptionHashMap = {};
                for (let i = 1; i <= lastDayOfMonth; i++) {
                    consumptionHashMap[i] = 0;
                }
                // console.log(consumptionHashMap);

                if(this.readings[site.name].length){

                    let previousTime = 0;
                    let previousConsumptionValue = this.readings[site.name][0][unit + 't'];
                    let previousDay = 1;

                    this.readings[site.name].forEach(reading => {
                        if (reading.time < previousTime){
                            console.log('ERROR: reading not properly sorted by time', reading.time, previousTime)
                        }
                        
                        if(previousConsumptionValue <= reading[unit + 't']){
                            consumptionHashMap[reading.calc_day_month] += reading[unit + 't'] - previousConsumptionValue
                        }
                        else{
                            console.log('ERROR: the previous consumption value  was higher than the current one', previousConsumptionValue, reading[unit + 't'])
                        }
                        // console.log('dia ' + reading.calc_day_month, consumptionHashMap[reading.calc_day_month], reading[unit + 't'], previousConsumptionValue, reading[unit + 't'] - previousConsumptionValue)
                        
                        // let auxDate = new Date(reading.time*1000);                                            
                        // this.series.push( [auxDate.toJSON(), Reading] );
                        previousTime = reading.time;
                        previousConsumptionValue = reading[unit + 't'];
                        previousDay = reading.calc_day_month;
                    })

                }

                console.log(consumptionHashMap);

                this.yMin = 0;
                Object.keys(consumptionHashMap).forEach( (key) => {
                    let consumption = consumptionHashMap[key] / 1000;
                    if (this.yMax < consumption) this.yMax = consumption;

                    let auxDate = new Date(this.year, this.monthMapper[this.month], key);
                    this.series.push([auxDate,consumption])
                });
                this.refreshEnergyConsumptionEchartSeries();
            },            


            setupConsumptionCostsSeries(unit){
                let site = this.getSiteById(this.selectedSiteId);
                if (site.tariff == null){return null;}
                
                switch (site.tariff.tariff_type) {
                    case 'simple':
                        this.setupSimpleConsumptionSeries(site, site.tariff)
                        break;
                    case 'bi-hourly':
                        this.setupBiHourlyConsumptionSeries(site, site.tariff)
                        break;
                    case 'tri-hourly':
                        this.setupTriHourlyConsumptionSeries(site, site.tariff)
                        break;
                }
            },

            setupSimpleConsumptionSeries(site, tariff){
                console.log('SimpleConsumptionSeries');
            },

            setupBiHourlyConsumptionSeries(site, tariff){
                console.log('setupBiHourlyConsumptionSeries');
            },

            setupTriHourlyConsumptionSeries(site, tariff){
                console.log('setupTriHourlyConsumptionSeries');
            },
            
            setupRegularSeries(unit){
                console.log('setupRegularSeries');
            },

            setupEchartGraph(){
                var dom = document.getElementById("echart-report");
                this.eChart = echarts.init(dom);
                console.log('creating echarts-report dom')
            },

            refreshEnergyConsumptionEchartSeries(){
                if(this.eChart != null){
                    console.log('**** drawing chart ****')
                    this.eChart.setOption(this.energyConsumptionOptionsBuilder(), true);
                }
            },

            energyConsumptionOptionsBuilder(){

                let maxArray = [];
                let minArray = [];
                
                maxArray.push(this.yMax);
                minArray.push(this.yMin);
            

                let max = maxArray.reduce(function(a, b) {
                    return Math.max(a, b);
                });
                let min = minArray.reduce(function(a, b) {
                    return Math.min(a, b);
                });


                let gap = Math.ceil((max - min) * .10);

                console.log(max, min, gap)

                return {
                    tooltip: {
                        trigger: 'axis',
                        position: function (pt) {
                            return [pt[0], '10%'];
                        },
                        formatter: function (params) {
                            var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                            let dateString = new Date(params[0].axisValue).toLocaleDateString("en-US")
                            let rez = '<span>' + dateString + '</span> <br />';
                            //console.log(params); //quite useful for debug
                            params.forEach(item => {
                                //console.log(item); //quite useful for debug
                                var xx = '<span>'   + colorSpan(item.color) + ' ' + item.seriesName + ': ' + item.data[1] + '</span> <br />'
                                rez += xx;
                            });

                            return rez;
                        }  
                    },
                    title: {
                        left: 'center',
                        text: this.units[this.selectedUnit].name + ' values',
                        textStyle:{
                            color: "#fff",
                            fontFamily: 'Source Sans Pro',
                        }
                    },
                    toolbox: {
                        feature: {
                            dataZoom: {
                                title: {
                                    zoom: 'zoom',
                                    back: 'back',
                                },
                                yAxisIndex: 'none',
                                iconStyle:{
                                    borderColor: "#839496",
                                }
                            },
                            restore: {
                                title: 'restore',
                                iconStyle:{
                                    borderColor: "#839496",
                                }                                
                            },
                            saveAsImage: {
                                title: 'download image',
                                iconStyle:{
                                    borderColor: "#839496",
                                }                                
                            }
                        }
                    },
                    calculable : true,
                    xAxis : {
                        type: 'time',
                        boundaryGap:false,
                        axisLabel: {
                            formatter: (value =>
                                moment(value).format('MMM Do')
                            ),
                            color: "#839496",
                            fontFamily: 'Source Sans Pro',
                            fontSize: 12,
                        },
                        axisLine:{
                            lineStyle:{
                                color: "#839496"
                            },
                        }, 
                        axisTick:{
                            lineStyle:{
                                color: "#839496"
                            },
                        },
                        splitLine:{
                            lineStyle:{
                                color: "#839496"
                            },                            
                        },
                    },
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, '100%'],
                        min: 0,
                        max: Math.round(max + gap),
                        axisLabel:{
                            color: "#839496",
                            fontFamily: 'Source Sans Pro',
                            fontSize: 14,
                        },
                        axisLine:{
                            lineStyle:{
                                color: "#839496"
                            },
                        }, 
                        axisTick:{
                            lineStyle:{
                                color: "#839496"
                            },
                        },
                        splitLine:{
                            lineStyle:{
                                color: "#839496"
                            },                            
                        },
                    },
                    dataZoom: [
                        {
                            type: 'inside',
                            start: 0,
                            end: 100

                        },
                        {
                            start: 0,
                            end: 100,
                            handleIcon: 'M10.7,11.9v-1.3H9.3v1.3c-4.9,0.3-8.8,4.4-8.8,9.4c0,5,3.9,9.1,8.8,9.4v1.3h1.3v-1.3c4.9-0.3,8.8-4.4,8.8-9.4C19.5,16.3,15.6,12.2,10.7,11.9z M13.3,24.4H6.7V23h6.6V24.4z M13.3,19.6H6.7v-1.4h6.6V19.6z',
                            handleSize: '50%',
                            handleStyle: {
                                color: '#fff',
                                shadowBlur: 3,
                                shadowColor: 'rgba(0, 0, 0, 0.6)',
                                shadowOffsetX: 2,
                                shadowOffsetY: 2
                            },
                            labelFormatter:function (value, valueStr) {
                                let today = new Date(value);
                                let timeStr = today.toLocaleTimeString("pt-PT")
                                let dateStr = today.toLocaleDateString("en-US", {day: '2-digit', month: '2-digit', year:"numeric"})
                                return timeStr + "\n" + dateStr;

                            },
                            textStyle:{
                                color: "#839496",
                                fontFamily: 'Source Sans Pro',
                            },
                        },
                    ],
                    series: this.energyConsumptionSeriesBuilder()
                }
            },

            energyConsumptionSeriesBuilder(){

                let series = [];
                
                series.push({
                    name: this.units[this.selectedUnit].name,
                    type:'bar',
                    //smooth:this.smooth,
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[0]
                    },
                    data: this.series,
                })
                
                return series;
            },
            clear(){
                console.log('clearing chart...')
                this.readings = [];
                this.series = [];
                // this.serie2= [];
                // this.serie3= [];
                this.yMax= Number.NEGATIVE_INFINITY;
                this.yMin= Number.POSITIVE_INFINITY;
                // this.yMax2= Number.NEGATIVE_INFINITY;
                // this.yMin2= Number.POSITIVE_INFINITY;
                // this.yMax3= Number.NEGATIVE_INFINITY;
                // this.yMin3= Number.POSITIVE_INFINITY;
                // this.serie1Checkbox= true;
                // this.serie2Checkbox= false;
                // this.serie3Checkbox= false;
                this.refreshEnergyConsumptionEchartSeries();
            },

        }
    }
</script>