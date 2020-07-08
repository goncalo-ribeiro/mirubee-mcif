<template>
    <div>
        <h3 class="mb-2">
            {{month}} {{year}} report
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
        <div id="echart-report" style="height:60vh; width: 100%; min-height: 400px;" class="m-a">
        </div>
        <div id="report-table-div" v-if="this.units[this.selectedUnit].nameDB == '€' && (this.series).length && getSiteById(selectedSiteId) != null">
            <h3 class="mt-3">
                cost simulation tables
            </h3>

            <h4 class="mt-2<">
                monthly consumption simulation table
            </h4>
            <!-- Simple table -->
            <table class="table" v-if="getSiteById(selectedSiteId).tariff.tariff_type == 'simple'">
                <thead>
                    <tr class="table-dark">
                        <th scope="col">tariff time zone</th>
                        <th scope="col">consumption (kWh)</th>
                        <th scope="col">cost (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>whole day</td>
                        <td>{{Math.round(consumptionSeriesSum * 1000) /1000}}</td>
                        <td>{{Math.round(simpleConsumptionCost * 1000) /1000}}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Bi Hour table -->
            <table class="table" v-if="getSiteById(selectedSiteId).tariff.tariff_type == 'bi-hourly'">
                <thead>
                    <tr class="table-dark">
                        <th scope="col">tariff time zone</th>
                        <th scope="col">consumption (kWh)</th>
                        <th scope="col">cost (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>off-peak hours</td>
                        <td>{{Math.round(consumptionSeriesSum * 1000) /1000}}</td>
                        <td>{{Math.round(offPeakConsumptionCost * 1000) /1000}}</td>
                    </tr>
                    <tr>
                        <td>outside off-peak hours</td>
                        <td>{{Math.round(consumptionSeries2Sum * 1000) /1000}}</td>
                        <td>{{Math.round(outsideOffPeakConsumptionCost * 1000) /1000}}</td>
                    </tr>
                    <tr style="background-color: #073642">
                        <td colspan="2">total cost</td>
                        <td>{{Math.round(totalConsumptionCost * 1000) /1000}}</td>
                    </tr>
                </tbody>
            </table>

            <!-- Tri Hour table -->
            <table class="table" v-if="getSiteById(selectedSiteId).tariff.tariff_type == 'tri-hourly'">
                <thead>
                    <tr class="table-dark">
                        <th scope="col">tariff time zone</th>
                        <th scope="col">consumption (kWh)</th>
                        <th scope="col">cost (€)</th>
                    </tr>
                </thead>
                <tbody>
                
                    <tr>
                        <td>off-peak hours</td>
                        <td>{{Math.round(consumptionSeriesSum * 1000) /1000}}</td>
                        <td>{{Math.round(offPeakConsumptionCost * 1000) /1000}}</td>
                    </tr>
                    <tr>
                        <td>peak hours</td>
                        <td>{{Math.round(consumptionSeries2Sum * 1000) /1000}}</td>
                        <td>{{Math.round(peakConsumptionCost * 1000) /1000}}</td>
                    </tr>
                    <tr>
                        <td>full time hours</td>
                        <td>{{Math.round(consumptionSeries3Sum * 1000) /1000}}</td>
                        <td>{{Math.round(fullTimeConsumptionCost * 1000) /1000}}</td>
                    </tr>
                    <tr style="background-color: #073642">
                        <td colspan="2">total cost</td>
                        <td>{{Math.round(totalConsumptionCost * 1000) /1000}}</td>
                    </tr>
                </tbody>
            </table>

            <h4 class="mt-2<">
                monthly power price simulation table
            </h4>
            <table class="table">
                <thead>
                    <tr class="table-dark">
                        <th scope="col">daily power price (€)</th>
                        <th scope="col">number of days in {{month}}</th>
                        <th scope="col">monthly power price (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{getSiteById(selectedSiteId).tariff.daily_power_price}}</td>
                        <td>{{series.length}}</td>
                        <td>{{Math.round(getSiteById(selectedSiteId).tariff.daily_power_price * series.length * 1000) /1000}}</td>
                    </tr>
                </tbody>
            </table>
            <h4 class="mt-2<">
                total monthly cost simulation table
            </h4>
            <table class="table">
                <thead>
                    <tr class="table-dark">
                        <th scope="col">monthly consumption cost (€)</th>
                        <th scope="col">monthly power price (€)</th>
                        <th scope="col">tax percentage (%)</th>
                        <th scope="col">total monthly cost (€)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{Math.round(totalConsumptionCost * 1000) /1000}}</td>
                        <td>{{Math.round(getSiteById(selectedSiteId).tariff.daily_power_price * series.length * 1000) /1000}}</td>
                        <td>{{getSiteById(selectedSiteId).tariff.tax}}</td>
                        <td>{{ Math.round(((totalConsumptionCost + (getSiteById(selectedSiteId).tariff.daily_power_price * series.length)) *
                            (1 + (getSiteById(selectedSiteId).tariff.tax) * .01)) * 1000) / 1000}}</td>
                    </tr>
                </tbody>
            </table>
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
            $(window).on('resize', () => {
                console.log(this.units[this.selectedUnit].nameDB, (this.series).length)
                if(this.eChart != null && this.eChart != undefined){
                    this.eChart.resize();
                }
            });    
        },
        activated(){
            console.log('monthly report activated!');
            this.getSites();
            this.reset();
            this.eChart.resize();
        },
        data: function(){
            return {
                readings: {},
                eChart: undefined,

                sites: [],
                selectedSiteId: 0,

                selectedUnit: 0,
                units: [
                    {name:'energy consumption (kWh)', nameDB:'e'},
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
                series2: [],
                series3: [],

                consumptionSeries: {},
                consumptionSeries2: {},
                consumptionSeries3: {},

                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,

                colors:['rgb(255, 70, 131)', 'rgb(70, 255, 131)', 'rgb(70, 131, 255)'],
                dateArray: [],
            }
        },
        props:{
            month: String,
            year: Number, 
        },
        computed:{
            consumptionSeriesSum: function(){
                let counter = 0;
                Object.entries(this.consumptionSeries).forEach(([key, value]) => {
                    counter += value;     
                });
                return counter / 1000;
            },
            consumptionSeries2Sum: function(){
                let counter = 0;
                Object.entries(this.consumptionSeries2).forEach(([key, value]) => {
                    counter += value;     
                });
                return counter / 1000;
            },
            consumptionSeries3Sum: function(){
                let counter = 0;
                Object.entries(this.consumptionSeries3).forEach(([key, value]) => {
                    counter += value;     
                });
                return counter / 1000;
            },

            simpleConsumptionCost: function(){
                let site = this.getSiteById(this.selectedSiteId);
                if (site != null){
                    if (site.tariff.price_simple != null){
                        return this.consumptionSeriesSum * site.tariff.price_simple;
                    }
                }
                return 0;
            },

            offPeakConsumptionCost: function() {
                let site = this.getSiteById(this.selectedSiteId);
                if (site != null){
                    if (site.tariff.price_off_peak_hours != null){
                        return this.consumptionSeriesSum * site.tariff.price_off_peak_hours;
                    }
                }
                return 0;
            },
            
            outsideOffPeakConsumptionCost: function() {
                let site = this.getSiteById(this.selectedSiteId);
                if (site != null){
                    if (site.tariff.price_outside_off_peak_hours != null){
                        return this.consumptionSeries2Sum * site.tariff.price_outside_off_peak_hours;
                    }
                }
                return 0;
            },

            peakConsumptionCost: function() {
                let site = this.getSiteById(this.selectedSiteId);
                if (site != null){
                    if (site.tariff.price_peak_hours != null){
                        return this.consumptionSeries2Sum * site.tariff.price_peak_hours;
                    }
                }
                return 0;
            },

            fullTimeConsumptionCost: function() {
                let site = this.getSiteById(this.selectedSiteId);
                if (site != null){
                    if (site.tariff.price_full_time_hours != null){
                        return this.consumptionSeries3Sum * site.tariff.price_full_time_hours;
                    }
                }
                return 0;
            },

            totalConsumptionCost: function(){
                let site = this.getSiteById(this.selectedSiteId);
                if (site != null){
                    return (site.tariff.tariff_type == 'simple') ? this.simpleConsumptionCost :
                    (site.tariff.tariff_type == 'bi-hourly') ? this.offPeakConsumptionCost + this.outsideOffPeakConsumptionCost :
                    (site.tariff.tariff_type == 'tri-hourly') ? this.offPeakConsumptionCost + this.peakConsumptionCost + this.fullTimeConsumptionCost : 0
                }
                return 0;
            }
        },
        methods:{

            //#region initialization
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
                console.log('getSites')
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

            setupEchartGraph(){
                var dom = document.getElementById("echart-report");
                this.eChart = echarts.init(dom);
            },

            getMonthlyReadings(){
                let site = this.getSiteById(this.selectedSiteId);
                if(this.readings[site.name],length){
                    console.log('readings already retrieved for this site')
                    return;
                }
                
                console.log('getMonthlyReadings')
                let start = Date.UTC(this.year, this.monthMapper[this.month], 1);
                let end = Date.UTC(this.year, (this.monthMapper[this.month])+1, 0,23,59,59);

                console.log(start, end)

                axios.get(myUrl + '/api/sites/' + this.selectedSiteId + '/readings/' + start/1000 + '/' + end/1000 )
                .then( success => {
                    this.readings[site.name] = success.data.readings;
                    
                    this.setupSeriesandDrawGraph();
                });
            },

            //#endregion initialization

            setupSeriesandDrawGraph(){
                console.log('setupSeriesandDrawGraph');
                if(this.selectedSiteId == 0){
                    return;
                }
                
                this.series = [];
                this.series2 = [];
                this.series3 = [];
                
                this.yMax = Number.NEGATIVE_INFINITY;
                this.yMin = Number.POSITIVE_INFINITY;

                let unit = this.units[this.selectedUnit].nameDB;
                this.refreshEnergyConsumptionEchartSeries();

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
            
            //#region Regular Units 
      
            setupRegularSeries(unit){
                let site = this.getSiteById(this.selectedSiteId);
                let endDate = new Date(this.year, (this.monthMapper[this.month])+1, 0);
                let lastDayOfMonth = endDate.getDate();

                let monthlyHashMap = {};
                for (let i = 1; i <= lastDayOfMonth; i++) {
                    monthlyHashMap[i] = [];
                }

                if(this.readings[site.name].length){

                    let previousTime = 0;

                    this.readings[site.name].forEach(reading => {
                        if (reading.time < previousTime){
                            console.log('ERROR: reading not properly sorted by time', reading.time, previousTime)
                        }
                        
                        for (let i = 1; i < 4; i++){
                            monthlyHashMap[reading.calc_day_month].push(reading[unit + i])
                            if (this.yMax < reading[unit + i]) this.yMax = reading[unit + i];
                            if (this.yMin > reading[unit + i]) this.yMin = reading[unit + i];
                            
                        }
                        
                        previousTime = reading.time;
                    })

                }

                console.log(monthlyHashMap);

                let auxArray = [];

                Object.keys(monthlyHashMap).forEach( (key) => {
                    let auxDate = new Date(this.year, this.monthMapper[this.month], key);
                    this.dateArray.push(auxDate);
                    auxArray.push(monthlyHashMap[key]);
                });

                this.series = echarts.dataTool.prepareBoxplotData(auxArray);
                this.refreshRegularEchartSeries();
            },

            refreshRegularEchartSeries(){
                if(this.eChart != null){
                    console.log('**** drawing chart ****')
                    this.eChart.setOption(this.boxPlotOptionsBuilder(), true);
                }
            },

            boxPlotOptionsBuilder(){
                let max = this.yMax;
                let min = this.yMin;

                let gap = Math.ceil((max - min) * .10);

                console.log(max, min, gap)

                return {
                    tooltip: {
                        trigger: 'axis',
                        position: function (pt) {
                            return [pt[0], '10%'];
                        },
                        formatter: (param) => {
                            var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                            let dateString = this.dateArray[param[0].axisValue].toLocaleDateString();
                            let rez = '<span>' + dateString + '</span> <br />';
                            if(!isNaN(param[0].data[1])){
                                rez += colorSpan('rgb(255, 70, 131)') + ' ' + param[0].seriesName + '<br/> ';
                                rez += [
                                    'upper: ' + param[0].data[5],
                                    'Q3: ' + param[0].data[4],
                                    'median: ' + param[0].data[3],
                                    'Q1: ' + param[0].data[2],
                                    'lower: ' + param[0].data[1]
                                ].join('<br/>');
                            }else{
                                rez += 'no values registered'
                            }

                            return rez;
                            // return [
                            //     'Experiment ' + param.name + ': ',
                            //     'upper: ' + param.data[5],
                            //     'Q3: ' + param.data[4],
                            //     'median: ' + param.data[3],
                            //     'Q1: ' + param.data[2],
                            //     'lower: ' + param.data[1]
                            // ].join('<br/>');
                        },
                        // formatter: function (params) {
                        //     var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                        //     let dateString = new Date(params[0].axisValue).toLocaleDateString("en-US", {hour12: false, day: '2-digit', month: '2-digit', year:"numeric", hour: '2-digit', minute: '2-digit', second: '2-digit'})
                        //     let rez = '<span>' + dateString + '</span> <br />';
                        //     //console.log(params); //quite useful for debug
                        //     params.forEach(item => {
                        //         //console.log(item); //quite useful for debug
                        //         var xx = '<span>'   + colorSpan(item.color) + ' ' + item.seriesName + ': ' + item.data[1] + '</span> <br />'
                        //         rez += xx;
                        //     });

                        //     return rez;
                        // }  
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
                        type: 'category',
                        data: this.series.axisData,
                        boundaryGap:true,
                        axisLabel: {
                            formatter: (value =>
                                moment(this.dateArray[value]).format('MMM Do')
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
                        min: Math.round(min - gap),
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
                            labelFormatter: (value, valueStr) => {
                                // let today = new Date(value);
                                // let timeStr = today.toLocaleTimeString("pt-PT")
                                // let dateStr = today.toLocaleDateString("en-US", {day: '2-digit', month: '2-digit', year:"numeric"})
                                return moment(this.dateArray[value]).format('MMM Do');

                            },
                            textStyle:{
                                color: "#839496",
                                fontFamily: 'Source Sans Pro',
                            },
                        },
                    ],
                    series: this.boxPlotSeriesBuilder()
                }
            },

            boxPlotSeriesBuilder(){
                let series = [];
                
                series.push({
                    name: this.units[this.selectedUnit].name,
                    type:'boxplot',
                    itemStyle: {
                        color: this.colors[0],
                        borderColor: "#839496",
                    },
                    data: this.series.boxData,
                    // tooltip: {
                    //     formatter: function (param) {
                    //         console.log(param)
                    //         return [
                    //             'Experiment ' + param.name + ': ',
                    //             'upper: ' + param.data[5],
                    //             'Q3: ' + param.data[4],
                    //             'median: ' + param.data[3],
                    //             'Q1: ' + param.data[2],
                    //             'lower: ' + param.data[1]
                    //         ].join('<br/>');
                    //     }
                    // }
                })

                console.log(series)
                
                return series;
            },

            //#endregion Regular Units

            //#region Energy Consumption

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

                console.log(series)
                
                return series;
            },

            //#endregion Energy Consumption


            setupConsumptionCostsSeries(unit){
                let site = this.getSiteById(this.selectedSiteId);
                if (site.tariff == null){console.log('null tariff'); return null;}
                
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

            //#region simple

            setupSimpleConsumptionSeries(site, tariff){
                console.log('SimpleConsumptionSeries');

                let endDate = new Date(this.year, (this.monthMapper[this.month])+1, 0);
                let lastDayOfMonth = endDate.getDate();

                let consumptionHashMap = {};
                for (let i = 1; i <= lastDayOfMonth; i++) {
                    consumptionHashMap[i] = 0;
                }

                if(this.readings[site.name].length){

                    let previousTime = 0;
                    let previousConsumptionValue = this.readings[site.name][0]['et'];
                    let previousDay = 1;

                    this.readings[site.name].forEach(reading => {
                        if (reading.time < previousTime){
                            console.log('ERROR: reading not properly sorted by time', reading.time, previousTime)
                        }
                        
                        if(previousConsumptionValue <= reading['et']){
                            consumptionHashMap[reading.calc_day_month] += reading['et'] - previousConsumptionValue                            
                        }
                        else{
                            console.log('ERROR: the previous consumption value  was higher than the current one', previousConsumptionValue, reading['et'])
                        }
                        previousTime = reading.time;
                        previousConsumptionValue = reading['et'];
                        previousDay = reading.calc_day_month;
                    })
                }

                console.log(consumptionHashMap);

                this.consumptionSeries = consumptionHashMap;
                this.consumptionSeries2 = {};
                this.consumptionSeries3 = {};

                this.yMin = 0;
                Object.keys(consumptionHashMap).forEach( (key) => {
                    let consumption = consumptionHashMap[key] / 1000;
                    if (this.yMax < Math.round((consumption * tariff.price_simple) * 1000) / 1000) this.yMax = Math.round((consumption * tariff.price_simple) * 1000) / 1000;

                    let auxDate = new Date(this.year, this.monthMapper[this.month], key);
                    this.series.push([auxDate,Math.round((consumption * tariff.price_simple) * 1000) / 1000])
                });
                this.refreshSimpleEnergyConsumptionEchartSeries();
            },

            refreshSimpleEnergyConsumptionEchartSeries(){
                if(this.eChart != null){
                    console.log('**** drawing chart ****')
                    this.eChart.setOption(this.SimpleEnergyConsumptionCostOptionsBuilder(), true);
                }
            },

            SimpleEnergyConsumptionCostOptionsBuilder(){
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
                        //formatter: '{a0} : {b0} : {c0}<br />{a1} : {b1} : {c1}' 
                        formatter: function (params) {
                            var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                            let dateString = new Date(params[0].axisValue).toLocaleDateString()
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
                    series: this.SimpleConsumptionCostSeriesBuilder()
                }
            },

            SimpleConsumptionCostSeriesBuilder(){
                let series = [];
                series.push({
                    name: this.units[this.selectedUnit].name,
                    type:'bar',
                    //smooth:this.smooth,
                    stack: '1',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[0]
                    },
                    data: this.series,
                })
            
                return series;
            },

            //#endregion simple

            //#region bi

            setupBiHourlyConsumptionSeries(site, tariff){
                console.log('setupBiHourlyConsumptionSeries');
                let endDate = new Date(this.year, (this.monthMapper[this.month])+1, 0);
                let lastDayOfMonth = endDate.getDate();

                let consumptionHashMapOffPeak = {};
                let consumptionHashMapOutsideOffPeak = {};
                for (let i = 1; i <= lastDayOfMonth; i++) {
                    consumptionHashMapOffPeak[i] = 0;
                    consumptionHashMapOutsideOffPeak[i] = 0;
                }

                let biHourlyTimezones = this.buildBiHourlyTimezones(tariff);

                if(this.readings[site.name].length){

                    let previousTime = 0;
                    let previousConsumptionValue = this.readings[site.name][0]['et'];
                    let previousDay = 1;

                    this.readings[site.name].forEach(reading => {
                        if (reading.time < previousTime){
                            console.log('ERROR: reading not properly sorted by time', reading.time, previousTime)
                        }
                        
                        if(previousConsumptionValue <= reading['et']){
                            if (biHourlyTimezones[0].includes(reading.calc_hour)){
                                consumptionHashMapOffPeak[reading.calc_day_month] += reading['et'] - previousConsumptionValue
                            }
                            if (biHourlyTimezones[1].includes(reading.calc_hour)){
                                consumptionHashMapOutsideOffPeak[reading.calc_day_month] += reading['et'] - previousConsumptionValue
                            }                            
                        }
                        else{
                            console.log('ERROR: the previous consumption value  was higher than the current one', previousConsumptionValue, reading['et'])
                        }
                        // console.log('dia ' + reading.calc_day_month, consumptionHashMap[reading.calc_day_month], reading[unit + 't'], previousConsumptionValue, reading[unit + 't'] - previousConsumptionValue)
                        
                        // let auxDate = new Date(reading.time*1000);                                            
                        // this.series.push( [auxDate.toJSON(), Reading] );
                        previousTime = reading.time;
                        previousConsumptionValue = reading['et'];
                        previousDay = reading.calc_day_month;
                    })

                }

                console.log(consumptionHashMapOffPeak, consumptionHashMapOutsideOffPeak)

                this.consumptionSeries = consumptionHashMapOffPeak;
                this.consumptionSeries2 = consumptionHashMapOutsideOffPeak;
                this.consumptionSeries3 = {};

                this.yMin = 0;
                Object.keys(consumptionHashMapOffPeak).forEach( (key) => {
                    let consumptionOffPeak = consumptionHashMapOffPeak[key] / 1000;
                    let consumptionOutsideOffPeak = consumptionHashMapOutsideOffPeak[key] / 1000;
                    if (this.yMax < Math.round((consumptionOffPeak * tariff.price_off_peak_hours) * 1000) / 1000 + Math.round((consumptionOutsideOffPeak * tariff.price_outside_off_peak_hours) * 1000) / 1000){

                        this.yMax = Math.round((consumptionOffPeak * tariff.price_off_peak_hours) * 1000) / 1000 + Math.round((consumptionOutsideOffPeak * tariff.price_outside_off_peak_hours) * 1000) / 1000;
                    } 

                    let auxDate = new Date(this.year, this.monthMapper[this.month], key);
                    this.series.push([auxDate,Math.round((consumptionOffPeak * tariff.price_off_peak_hours) * 1000) / 1000])
                    this.series2.push([auxDate,Math.round((consumptionOutsideOffPeak * tariff.price_outside_off_peak_hours) * 1000) / 1000])
                });
                this.refreshBiHourlyEnergyConsumptionCostEchartSeries();
            },

            refreshBiHourlyEnergyConsumptionCostEchartSeries(){
                if(this.eChart != null){
                    console.log('**** drawing chart ****')
                    this.eChart.setOption(this.BiHourlyEnergyConsumptionCostOptionsBuilder(), true);
                }
            },

            BiHourlyEnergyConsumptionCostOptionsBuilder(){
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
                        //formatter: '{a0} : {b0} : {c0}<br />{a1} : {b1} : {c1}' 
                        formatter: function (params) {
                            var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                            let dateString = new Date(params[0].axisValue).toLocaleDateString()
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
                    series: this.BiHourlyConsumptionCostSeriesBuilder()
                }
            },

            BiHourlyConsumptionCostSeriesBuilder(){
                let series = [];
                series.push({
                    name: this.units[this.selectedUnit].name + ' (Off Peak Hours)',
                    type:'bar',
                    //smooth:this.smooth,
                    stack: '1',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[0]
                    },
                    data: this.series,
                })
            
                series.push({
                    name: this.units[this.selectedUnit].name + ' (Outside Off Peak Hours)',
                    type:'bar',
                    //smooth:this.smooth,
                    stack: '1',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[1]
                    },
                    data: this.series2,
                })
            
                return series;
            },

            buildBiHourlyTimezones(tariff){
                let timezoneOffPeak = []
                let timezoneOutsideOffPeak = []

                let startOffPeak = parseInt(tariff.starting_time_off_peak_hours.substring(0,2))
                let startOutsideOffPeak = parseInt(tariff.starting_time_outside_off_peak_hours.substring(0,2))

                timezoneOffPeak.push(startOffPeak);
                timezoneOutsideOffPeak.push(startOutsideOffPeak);

                //offpeak
                for (let i = 1; i < 24; i++) {
                    let hourAux = (startOffPeak + i) % 24;
                    if (timezoneOutsideOffPeak.includes(hourAux)){
                        break;
                    }
                    timezoneOffPeak.push(hourAux);
                }

                //OutsideOffPeak
                for (let i = 1; i < 24; i++) {
                    let hourAux = (startOutsideOffPeak + i) % 24;
                    if (timezoneOffPeak.includes(hourAux)){
                        break;
                    }
                    timezoneOutsideOffPeak.push(hourAux);
                }

                console.log(timezoneOffPeak ,timezoneOutsideOffPeak);
                return([timezoneOffPeak ,timezoneOutsideOffPeak])
            },

            //#endregion bi

            //#region tri

            setupTriHourlyConsumptionSeries(site, tariff){
                console.log('setupTriHourlyConsumptionSeries');
                let endDate = new Date(this.year, (this.monthMapper[this.month])+1, 0);
                let lastDayOfMonth = endDate.getDate();

                let consumptionHashMapOffPeak = {};
                let consumptionHashMapPeak = {};
                let consumptionHashMapFullTime = {};
                for (let i = 1; i <= lastDayOfMonth; i++) {
                    consumptionHashMapOffPeak[i] = 0;
                    consumptionHashMapPeak[i] = 0;
                    consumptionHashMapFullTime[i] = 0;
                }

                let triHourlyTimezones = this.buildTriHourlyTimezones(tariff);

                if(this.readings[site.name].length){

                    let previousTime = 0;
                    let previousConsumptionValue = this.readings[site.name][0]['et'];
                    let previousDay = 1;

                    this.readings[site.name].forEach(reading => {
                        if (reading.time < previousTime){
                            console.log('ERROR: reading not properly sorted by time', reading.time, previousTime)
                        }
                        
                        if(previousConsumptionValue <= reading['et']){
                            if (triHourlyTimezones[0].includes(reading.calc_hour)){
                                consumptionHashMapOffPeak[reading.calc_day_month] += reading['et'] - previousConsumptionValue
                            }
                            if (triHourlyTimezones[1].includes(reading.calc_hour)){
                                consumptionHashMapPeak[reading.calc_day_month] += reading['et'] - previousConsumptionValue
                            }
                            if (triHourlyTimezones[2].includes(reading.calc_hour)){
                                consumptionHashMapFullTime[reading.calc_day_month] += reading['et'] - previousConsumptionValue
                            }                            
                        }
                        else{
                            console.log('ERROR: the previous consumption value  was higher than the current one', previousConsumptionValue, reading['et'])
                        }
                        // console.log('dia ' + reading.calc_day_month, consumptionHashMap[reading.calc_day_month], reading[unit + 't'], previousConsumptionValue, reading[unit + 't'] - previousConsumptionValue)
                        
                        // let auxDate = new Date(reading.time*1000);                                            
                        // this.series.push( [auxDate.toJSON(), Reading] );
                        previousTime = reading.time;
                        previousConsumptionValue = reading['et'];
                        previousDay = reading.calc_day_month;
                    })

                }

                console.log(consumptionHashMapOffPeak, consumptionHashMapPeak, consumptionHashMapFullTime)

                this.consumptionSeries = consumptionHashMapOffPeak;
                this.consumptionSeries2 = consumptionHashMapPeak;
                this.consumptionSeries3 = consumptionHashMapFullTime;

                this.yMin = 0;
                Object.keys(consumptionHashMapOffPeak).forEach( (key) => {
                    let consumptionOffPeak = consumptionHashMapOffPeak[key] / 1000;
                    let consumptionPeak = consumptionHashMapPeak[key] / 1000;
                    let consumptionFullTime = consumptionHashMapFullTime[key] / 1000;
                    if (this.yMax < Math.round((consumptionOffPeak * tariff.price_off_peak_hours) * 1000) / 1000 +
                        Math.round((consumptionPeak * tariff.price_peak_hours) * 1000) / 1000 +
                        Math.round((consumptionFullTime * tariff.price_full_time_hours) * 1000) / 1000){

                        this.yMax = Math.round((consumptionOffPeak * tariff.price_off_peak_hours) * 1000) / 1000 + Math.round((consumptionPeak * tariff.price_peak_hours) * 1000) / 1000 + Math.round((consumptionFullTime * tariff.price_full_time_hours) * 1000) / 1000;
                    } 

                    let auxDate = new Date(this.year, this.monthMapper[this.month], key);
                    this.series.push([auxDate,Math.round((consumptionOffPeak * tariff.price_off_peak_hours) * 1000) / 1000])
                    this.series2.push([auxDate,Math.round((consumptionPeak * tariff.price_peak_hours) * 1000) / 1000])
                    this.series3.push([auxDate,Math.round((consumptionFullTime * tariff.price_full_time_hours) * 1000) / 1000])
                });
                this.refreshTriHourlyEnergyConsumptionCostEchartSeries();
            },

            refreshTriHourlyEnergyConsumptionCostEchartSeries(){
                if(this.eChart != null){
                    console.log('**** drawing chart ****')
                    this.eChart.setOption(this.TriHourlyEnergyConsumptionCostOptionsBuilder(), true);
                }
            },

            TriHourlyEnergyConsumptionCostOptionsBuilder(){
                
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
                        //formatter: '{a0} : {b0} : {c0}<br />{a1} : {b1} : {c1}' 
                        formatter: function (params) {
                            var colorSpan = color => '<span style="display:inline-block;margin-right:5px;border-radius:10px;width:9px;height:9px;background-color:' + color + '"></span>';
                            let dateString = new Date(params[0].axisValue).toLocaleDateString()
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
                    series: this.TriHourlyConsumptionCostSeriesBuilder()
                }
            },

            TriHourlyConsumptionCostSeriesBuilder(){
                let series = [];
                series.push({
                    name: this.units[this.selectedUnit].name + ' (Off Peak Hours)',
                    type:'bar',
                    //smooth:this.smooth,
                    stack: '1',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[0]
                    },
                    data: this.series,
                })
            
                series.push({
                    name: this.units[this.selectedUnit].name + ' (Peak Hours)',
                    type:'bar',
                    //smooth:this.smooth,
                    stack: '1',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[1]
                    },
                    data: this.series2,
                })
            
            
                series.push({
                    name: this.units[this.selectedUnit].name + ' (Full Time Hours)',
                    type:'bar',
                    //smooth:this.smooth,
                    stack: '1',
                    symbol: 'none',
                    sampling: 'average',
                    itemStyle: {
                        color: this.colors[2]
                    },
                    data: this.series3,
                })
            
                return series;
            },

            buildTriHourlyTimezones(tariff){
                let timezoneOffPeak = []
                let timezonePeak = []
                let timezoneFullTime = []

                let startOffPeak = parseInt(tariff.starting_time_off_peak_hours.substring(0,2))
                let startPeak = parseInt(tariff.starting_time_peak_hours.substring(0,2))
                let startFullTime = parseInt(tariff.starting_time_full_time_hours.substring(0,2))

                timezoneOffPeak.push(startOffPeak);
                timezonePeak.push(startPeak);
                timezoneFullTime.push(startFullTime);

                //offpeak
                for (let i = 1; i < 24; i++) {
                    let hourAux = (startOffPeak + i) % 24;
                    if (timezonePeak.includes(hourAux) || timezoneFullTime.includes(hourAux)){
                        break;
                    }
                    timezoneOffPeak.push(hourAux);
                }

                //fulltime
                for (let i = 1; i < 24; i++) {
                    let hourAux = (startFullTime + i) % 24;
                    if (timezonePeak.includes(hourAux) || timezoneOffPeak.includes(hourAux)){
                        break;
                    }
                    timezoneFullTime.push(hourAux);
                }

                //peak
                for (let i = 1; i < 24; i++) {
                    let hourAux = (startPeak + i) % 24;
                    if (timezoneOffPeak.includes(hourAux) || timezoneFullTime.includes(hourAux)){
                        break;
                    }
                    timezonePeak.push(hourAux);
                }

                console.log(timezoneOffPeak ,timezonePeak ,timezoneFullTime);
                return([timezoneOffPeak ,timezonePeak ,timezoneFullTime])
            },
            
            //#endregion tri

            clear(){
                console.log('clearing chart...')
                this.readings = [];
                this.series = [];
                this.serie2= [];
                this.serie3= [];
                this.yMax= Number.NEGATIVE_INFINITY;
                this.yMin= Number.POSITIVE_INFINITY;
                // this.yMax2= Number.NEGATIVE_INFINITY;
                // this.yMin2= Number.POSITIVE_INFINITY;
                // this.yMax3= Number.NEGATIVE_INFINITY;
                // this.yMin3= Number.POSITIVE_INFINITY;
                // this.serie1Checkbox= true;
                // this.serie2Checkbox= false;
                // this.serie3Checkbox= false;
                this.dateArray = [];
                this.refreshEnergyConsumptionEchartSeries();
            },

        }
    }
</script>