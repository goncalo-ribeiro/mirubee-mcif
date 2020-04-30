<template>
    <div>

    <!--
        <div id="echart" style="height:80vh; width:80vw" class="m-a"></div>
    -->
        <div class="mt-5 container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <span style="vertical-align: middle;">
                                {{siteName}}
                            </span>
                            <span style="float: right!important;">
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#update-site-modal">edit <i class="material-icons" style="padding-left:4px; vertical-align: middle;">edit</i> </button>
                                <button type="button" class="btn btn-danger btn-sm" v-on:click="deleteSite">remove <i class="material-icons" style="padding-left:4px; vertical-align: middle;">delete</i></button>
                            </span>
                        </div>

                        <div class="card-body" >
                            <div>
                                <div class="form-row">
                                    <div class="form-group col-md-4">
                                        <label for="device">device</label>
                                        <select class="form-control" v-model="selectedDevice">
                                            <option disabled value="0">choose one of the following devices</option>
                                            <option v-for="(device, index) in devices" :key="index" :value="device.id">{{device.name}}</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="datepicker">selected days</label>
                                        <date-picker :input-attr="{id: 'datepicker'}" input-class="form-control" v-model="datesPicked" range format="DD MMM YYYY" placeholder="pick your desired date" v-on:input="getReadings()"></date-picker>
                                    </div>
                                    <div class="form-group col-md-4">
                                        <label for="parameter">unit</label>
                                        <select class="form-control" v-on:input="selectedUnit = $event.target.value; setupSeriesandDrawGraph()">
                                            <option v-for="(unit, index) in units" :key="index" :value="index">{{unit.name}}</option>
                                        </select>
                                    </div>
                                </div>
                                <!--
                                <div v-show="readings.length == 0" class="row justify-content-center">
                                    <div class="col-md-8" style="text-align: center;vertical-align: middle;">
                                        <h4>
                                            {{errorMessages[errorMessageIndex]}}
                                        </h4>
                                        <h1>
                                            ¯\_(ツ)_/¯
                                        </h1>
                                    </div>
                                </div>
                                    -->
                                
                                <div id="echart" style="height:60vh; width: 100%; min-height: 400px;" class="m-a">
                                </div>
                                <div>
                                    <label class="ml-5" for="serie1-checkbox">
                                        <input class="form-check-input" type="checkbox" id="serie1-checkbox" v-model="serie1Checkbox" @change="refreshEchartSeries($event)"
                                        >
                                            <span :style="'display:inline-block;margin-left:3px;margin-right:3px;border-radius:10px;width:9px;height:9px;background-color:' + colors[0] "></span> phase 1
                                    </label>
                                    <label class="ml-5" for="serie2-checkbox">
                                        <input class="form-check-input" type="checkbox" id="serie2-checkbox" v-model="serie2Checkbox" @change="refreshEchartSeries($event)"
                                        >
                                            <span :style="'display:inline-block;margin-left:3px;margin-right:3px;border-radius:10px;width:9px;height:9px;background-color:' + colors[1] "></span> phase 2
                                    </label>
                                    <label class="ml-5" for="serie3-checkbox">
                                        <input class="form-check-input" type="checkbox" id="serie3-checkbox" v-model="serie3Checkbox" @change="refreshEchartSeries($event)"
                                        >
                                            <span :style="'display:inline-block;margin-left:3px;margin-right:3px;border-radius:10px;width:9px;height:9px;background-color:' + colors[2] "></span> phase 3
                                    </label>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="update-site-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateModalLabel">update {{tempName}}<i class="material-icons" style="padding-left:5px; vertical-align: middle;">edit</i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        
                        <label for="update-site-name">your site's name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="update-site-name" placeholder="site's name" v-model="tempName" v-on:keyup.enter="updateSite">
                        </div>

                        <label for="update-site-location">your site's location (optional)</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="update-site-location" placeholder="site's location" v-model="tempLocation" v-on:keyup.enter="updateSite">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="updateSite">update site</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--
    -->
</template>

<script>
var moment = require('moment');

import Auth from './auth/auth.js';
import DatePicker from 'vue2-datepicker';
//import 'vue2-datepicker/index.css';

export default {
        name: "site",
        mounted() {
            console.log('Component mounted.')

            $('#update-site-modal').on('show.bs.modal', () => {
                console.log('show')
                this.tempName = this.siteName;
                this.tempLocation = this.siteLocation;
            });
            $('#update-site-modal').on('shown.bs.modal', () => {
                $('#update-site-name').focus();
            });
            this.setupEchartGraph();
            $(window).on('resize', () => {
                if(this.eChart != null && this.eChart != undefined){
                    this.eChart.resize();
                }
            });
        },
        activated(){
            console.log('Component activated.');
            console.log(' * * * * * setup activated * * * * * *');
            this.setup();
            this.eChart.resize();
        },
        data: function(){
            return {
                valueTime: null,
                
                errorMessages: ['you have no devices linked to this site', 'no readings were found on the selected days'],
                errorMessageIndex: 0,

                tempName: undefined,
                tempLocation: undefined,

                smooth: false,
                timestampsRepetidos: true,

                eChart: undefined,

                devices: [],
                selectedDevice: 0,

                //deprecated
                /*units: ['voltage', 'current', 'apparent power', 'active power', 'inductive reactive power', 'capacitive reactive power', 'frequency',
                'power factor', 'Harmonics A', 'Harmonics V', 'CO2', 'Active Energy', 'Reactive Energy'],*/
                //deprecated
                unitsDBName: ['v','i','p','a','r','q','f','e','o','ps'],

                units: [
                {name:'voltage', nameDB:'v'},
                {name:'current', nameDB:'i'},
                {name:'apparent power', nameDB:'p'},
                {name:'active power', nameDB:'a'},
                {name:'inductive reactive power', nameDB:'v'},      //?
                {name:'capacitive reactive power', nameDB:'v'},     //?
                {name:'frequency', nameDB:'q'},
                {name:'power factor', nameDB:'f'},
                {name:'Harmonics A', nameDB:'v'},                   //?
                {name:'Harmonics V', nameDB:'v'},                   //?
                {name:'CO2', nameDB:'v'},                           //?
                {name:'Active Energy', nameDB:'v'},                 //?
                {name:'Reactive Energy', nameDB:'v'}],              //?

                selectedUnit: 0,

                readings: [],
                serie1: [],
                serie2: [],
                serie3: [],

                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,

                yMax2: Number.NEGATIVE_INFINITY,
                yMin2: Number.POSITIVE_INFINITY,

                yMax3: Number.NEGATIVE_INFINITY,
                yMin3: Number.POSITIVE_INFINITY,

                serie1Checkbox: true,
                serie2Checkbox: false,
                serie3Checkbox: false,

                colors:['rgb(255, 70, 131)', 'rgb(70, 255, 131)', 'rgb(70, 131, 255)'],
                
                datesPicked: undefined,
            }
        },
        props: {
            siteName: {
                type: String,
            },
            siteId: {
                type: Number,
            },
            siteLocation: {
                type: String,
            },
        },
        watch: { 
            siteId: function(newVal, oldVal) { // watch it
                console.log(' * * * * * setup watcher * * * * * *');
                this.setup();
            },
            selectedDevice: function(newVal, oldVal) { // watch it
                //this.getReadings();
            },
        },
        methods:{
            deleteSite(){
                axios.delete(myUrl+ "/api/sites", {data: {id : this.siteId}})
                .then(response => {
                    console.log(response);
                    Vue.toasted.show('Site deleted', { icon : 'delete', type: 'success'});
                    this.$emit('site-deleted', response.data.site.id)
                })
                .catch(error => {
                    console.log(error);
                    let errors = error.response.data.errors;

                    for (let key in errors){
                        errors[key].forEach(err => 
                            Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                        );
                    }
                });
            },
            updateSite(){
                if( this.tempName == "" ){
                    Vue.toasted.show("The site's name can't be empty", { icon : 'cancel', type: 'error'});
                    return;
                }
                axios.put(myUrl+"/api/sites", { name : this.tempName, location : this.tempLocation, id : this.siteId})
                .then( response => {
                    console.log(response);
                    $('#update-site-modal').modal('hide')
                    Vue.toasted.show('Site update', { icon : 'check', type: 'success'});
                    
                    this.$emit('site-updated', response.data.site)
                })
                .catch(error => {
                    console.log(error.response);
                    let errors = error.response.data.errors;

                    for (let key in errors){
                        errors[key].forEach(err => 
                            Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                        );
                    }

                })
            },
            setup(){
                console.log(' * * * * * setup * * * * * *');
                if(this.eChart){
                    this.clear();
                }
                this.getSiteDevices();
                this.setDayOnPicker();
            },
            getSiteDevices(){
                axios.get(myUrl+"/api/sites/" + this.siteId + "/devices")
                .then(response =>{
                    this.devices = response.data.devices;
                    console.log('this.devices.length', this.devices.length);
                    if (this.devices.length != 0){
                        console.log('this.devices.length != 0')
                        this.selectedDevice = this.devices[0].id;
                    }
                    else{
                        console.log('this.devices.length == 0')
                        this.selectedDevice = 0;
                    }
                    console.log('selectedDevice', this.selectedDevice)
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's device retrieval)", { icon : 'cancel', type: 'error'});
                })      
            },

            setDayOnPicker(){
                this.datesPicked = [new Date(), new Date()];
            },

            getReadings(){
                console.log('getReadings')
                console.log('selectedDevice', this.selectedDevice)

                if(this.selectedDevice == 0){
                    console.log('this.selectedDevice == 0')
                    this.errorMessageIndex = 0;
                    return;
                }

                let start = Date.UTC(this.datesPicked[0].getFullYear(), this.datesPicked[0].getMonth(), this.datesPicked[0].getDate(),0,0,0) / 1000;
                let end = Date.UTC(this.datesPicked[1].getFullYear(), this.datesPicked[1].getMonth(), this.datesPicked[1].getDate(),23,59,59) / 1000;

                console.log(start, end)

                axios.get(myUrl + '/api/sites/' + this.siteId + '/readings/' + start + '/' + end )
                .then( success => {
                    this.randomizeReadingsOfOtherPhases(success.data.readings);
                    this.readings = success.data.readings;

                    if(this.readings.length == 0){
                        this.errorMessageIndex = 1;
                        return;
                    }
                    
                    this.setupSeriesandDrawGraph();
                    
                });
            },

            randomizeReadingsOfOtherPhases(readings){
                for (let i = 0; i < readings.length; i++) {
                    readings[i].v3 = readings[i].v3 + Math.floor(Math.random() * 3) - 1 // entre -1 e 1
                }
            },

            setupSeriesandDrawGraph(){
                                
                this.serie1 = [];
                this.serie2 = [];
                this.serie3 = [];

                this.yMax = Number.NEGATIVE_INFINITY;
                this.yMax2 = Number.NEGATIVE_INFINITY;
                this.yMax3 = Number.NEGATIVE_INFINITY;

                this.yMin = Number.POSITIVE_INFINITY;
                this.yMin2 = Number.POSITIVE_INFINITY;
                this.yMin3 = Number.POSITIVE_INFINITY;


                console.log('selectedUnit', this.selectedUnit);
                let unit = this.units[this.selectedUnit].nameDB;
                let lastTime = 0;
                
                console.log('unit', unit)

                this.readings.forEach(reading => {
                    if(lastTime + 30 < reading.time){
                        
                        let auxDate = new Date(reading.time*1000);
                        let Reading1 = reading[unit + '1'];
                        let Reading2 = reading[unit + '2'];
                        let Reading3 = reading[unit + '3'];


                        if (this.yMax < Reading1) this.yMax = Reading1;
                        if (this.yMax2 < Reading2) this.yMax2 = Reading2;
                        if (this.yMax3 < Reading3) this.yMax3 = Reading3;

                        if (this.yMin > Reading1) this.yMin = Reading1;
                        if (this.yMin2 > Reading2) this.yMin2 = Reading2;
                        if (this.yMin3 > Reading3) this.yMin3 = Reading3;
                                                
                        this.serie1.push( [auxDate.toJSON(), Reading1] );
                        this.serie2.push( [auxDate.toJSON(), Reading2] );
                        this.serie3.push( [auxDate.toJSON(), Reading3] );

                        lastTime = reading.time;
                    }
                })

                this.refreshEchartSeries();
            },

            setupEchartGraph(){
                var dom = document.getElementById("echart");
                this.eChart = echarts.init(dom);
                console.log('creating echarts dom')
            },

            refreshEchartSeries(event = null){
                
                if(this.serie1Checkbox || this.serie2Checkbox || this.serie3Checkbox){
                    if(this.eChart != null){
                        console.log('**** drawing chart ****')
                        this.eChart.setOption(this.optionsBuilder(), true);
                    }
                }else{
                    if(event != null){
                        switch(event.target.id) {
                            case 'serie1-checkbox':
                                $('#' + event.target.id).prop('checked', true);
                                this.serie1Checkbox = true;
                                break;
                            case 'serie2-checkbox':
                                $('#' + event.target.id).prop('checked', true);
                                this.serie2Checkbox = true;
                                break;
                            case 'serie3-checkbox':
                                $('#' + event.target.id).prop('checked', true);
                                this.serie3Checkbox = true;
                                break;
                        }                          
                    }
                }
            },

            optionsBuilder(){

                let maxArray = [];
                let minArray = [];
                if (this.serie1Checkbox){
                    maxArray.push(this.yMax);
                    minArray.push(this.yMin);
                }
                if (this.serie2Checkbox){
                    maxArray.push(this.yMax2);
                    minArray.push(this.yMin2);
                }
                if (this.serie3Checkbox){
                    maxArray.push(this.yMax3);
                    minArray.push(this.yMin3);
                }

                console.log(maxArray, minArray)

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
                            let dateString = new Date(params[0].axisValue).toLocaleDateString("en-US", {hour12: false, day: '2-digit', month: '2-digit', year:"numeric", hour: '2-digit', minute: '2-digit', second: '2-digit'})
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
                                moment(value).format('HH:mm:ss')
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
                    series: this.seriesBuilder()
                }
            },

            seriesBuilder(){

                let series = [];
                if(this.serie1Checkbox){
                    series.push({
                        name: this.units[this.selectedUnit].name + ' 1',
                        type:'line',
                        smooth:this.smooth,
                        symbol: 'none',
                        sampling: 'average',
                        itemStyle: {
                            color: this.colors[0]
                        },
                        data: this.serie1,
                    })
                }
                if(this.serie2Checkbox){
                    series.push({
                        name: this.units[this.selectedUnit].name + ' 2',
                        type:'line',
                        smooth:this.smooth,
                        symbol: 'none',
                        sampling: 'average',
                        itemStyle: {
                            color: this.colors[1]
                        },
                        data: this.serie2,
                    })
                }
                if(this.serie3Checkbox){
                    series.push({
                        name: this.units[this.selectedUnit].name + ' 3',
                        type:'line',
                        smooth:this.smooth,
                        symbol: 'none',
                        sampling: 'average',
                        itemStyle: {
                            color: this.colors[2]
                        },
                        data: this.serie3,
                    })
                }
                return series;
            },
            clear(){
                console.log('clearing chart...')
                this.readings = [];
                this.serie1= [];
                this.serie2= [];
                this.serie3= [];
                this.yMax= Number.NEGATIVE_INFINITY;
                this.yMin= Number.POSITIVE_INFINITY;
                this.yMax2= Number.NEGATIVE_INFINITY;
                this.yMin2= Number.POSITIVE_INFINITY;
                this.yMax3= Number.NEGATIVE_INFINITY;
                this.yMin3= Number.POSITIVE_INFINITY;
                this.serie1Checkbox= true;
                this.serie2Checkbox= false;
                this.serie3Checkbox= false;
                this.refreshEchartSeries();
            }
        }
    }
</script>