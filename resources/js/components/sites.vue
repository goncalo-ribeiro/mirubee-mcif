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
                                        <select class="form-control" v-model="selectedUnit" v-on:input="getReadings()">
                                            <option v-for="(unit, index) in units" :key="index" :value="index">{{unit}}</option>
                                        </select>
                                    </div>
                                </div>
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
                                <div v-show="readings.length != 0">
                                <div id="echart" style="height:60vh" class="m-a">
                                </div>
                                    <div>
                                        <input type="checkbox" id="smooth-checkbox" v-model="smooth" @change="setupEchartGraph">
                                        <label for="smooth-checkbox">smooth = {{smooth}}</label>

                                        <input class="ml-5" type="checkbox" id="timestampsRepetidos-checkbox" v-model="timestampsRepetidos" @change="refresh">
                                        <label for="timestampsRepetidos-checkbox">timestampsRepetidos = {{timestampsRepetidos}}</label>
                                    </div>
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
            this.setup()
        },
        activated(){
            console.log('Component activated.');
            this.setup(); 
        },
        data: function(){
            return {
                errorMessages: ['you have no devices linked to this site', 'no readings were found on the selected days'],
                errorMessageIndex: 0,

                tempName: undefined,
                tempLocation: undefined,

                smooth: false,
                timestampsRepetidos: true,

                devices: [],
                selectedDevice: 0,

                units: ['voltage', 'intensity', 'apparent power', 'active power', 'inductive reactive power', 'capacitive reactive power', 'frequency',
                'power factor', 'Harmonics A', 'Harmonics V', 'CO2', 'Active Energy', 'Reactive Energy'],
                selectedUnit: 0,

                readings: [],
                serie1: undefined,
                serie2: undefined,
                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,

                yMax2: Number.NEGATIVE_INFINITY,
                yMin2: Number.POSITIVE_INFINITY,
                
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
                this.setup();
            },
            selectedDevice: function(newVal, oldVal) { // watch it
                this.getReadings();
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
                let start = this.datesPicked[0]
                start.setHours(0,0,0,0);

                let end = this.datesPicked[1]
                end.setHours(23,59,59,999);

                console.log(start, end)
                console.log(start.getTime(), end.getTime());

                axios.get(myUrl + '/api/sites/' + this.siteId + '/readings/' + start.getTime()/1000 + '/' + end.getTime()/1000 )
                .then( success => {
                    this.readings = success.data.readings;

                    if(this.readings.length == 0){
                        this.errorMessageIndex = 1;
                        return;
                    }
                
                    this.serie1 = [];
                    this.serie2 = [];

                    let lastTime = 0;

                    this.readings.forEach(reading => {
                        if(lastTime + 30 < reading.time){

                            if (this.yMax < reading.v1) this.yMax = reading.v1;

                            if (this.yMin > reading.v1) this.yMin = reading.v1;
                            
                            let auxDate = new Date(reading.time*1000);
                            let Reading = reading.v1;
                            
                            let random;
                            if (this.yMin == Number.POSITIVE_INFINITY){
                                random = reading.v1
                            }
                            else{
                                random = Math.floor(Math.random() * (this.yMax - this.yMin)) + this.yMin;
                            }
                            let Reading2 = reading.v1 + 2 ;

                            //let Reading2;
                                                    
                            this.serie1.push( [auxDate.toJSON(), Reading] );
                            this.serie2.push( [auxDate.toJSON(), Reading2] );
                            lastTime = reading.time;
                        }
                    });
                    
                   this.setupEchartGraph();
                });
            },

            refresh(){
                this.yAxisData = [];
                this.xAxisData = [];
                this.newData = [];
                this.serie1 = [];
                this.test = [
                    ["2018-08-15T10:04:01.339Z",1],[ "2018-08-15T10:14:13.914Z",2],[ "2018-08-15T10:40:03.147Z",3],[ "2018-08-15T11:50:14.335Z",4]
                ];
                let timestamps = [];

                this.readings.forEach(reading => {
                    if(!timestamps.includes(reading.time)){
                        this.yAxisData.push(reading.v1)

                        if (this.yMax < reading.v1) this.yMax = reading.v1;

                        if (this.yMin > reading.v1) this.yMin = reading.v1;
                        
                        let auxDate = new Date(reading.time*1000);
                        let auxVolt = reading.v1;
                        
                        this.xAxisData.push([auxDate.getHours(), auxDate.getMinutes(), auxDate.getSeconds()].join(':'))

                        let newElement = {
                            name: auxDate.toString(),
                            value: [
                                [auxDate.getFullYear(), auxDate.getMonth() + 1, auxDate.getDate()].join('/'),
                                auxVolt
                            ]
                        }
                        this.newData.push(newElement);
                        
                        this.serie1.push( [auxDate.toJSON(), auxVolt] );
                        if(!this.timestampsRepetidos){
                            timestamps.push(reading.time);
                        } 
                    }
                });
                
                this.setupEchartGraph();
            },

            setupEchartGraph(){
                var dom = document.getElementById("echart");
                var myChart = echarts.init(dom);

                let gap = Math.ceil((this.yMax - this.yMin) * .10);
                console.log(gap)

                var options = {
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
                        text: 'voltage values',
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
                        min: Math.round(this.yMin - gap),
                        max: Math.round(this.yMax + gap),
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
                    series: [
                        {
                            name:'voltagem 1',
                            type:'line',
                            smooth:this.smooth,
                            symbol: 'none',
                            sampling: 'average',
                            itemStyle: {
                                color: 'rgb(255, 70, 131)'
                            },/*
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgb(255, 158, 68)'
                                }, {
                                    offset: 1,
                                    color: 'rgb(255, 70, 131)'
                                }])
                            },*/
                            data: this.serie1,
                        },
                        {
                            name:'voltagem 2',
                            type:'line',
                            smooth:this.smooth,
                            symbol: 'none',
                            sampling: 'average',
                            itemStyle: {
                                color: 'rgb(70, 255, 131)'
                            },/*
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgb(70, 255, 68)'
                                }, {
                                    offset: 1,
                                    color: 'rgb(158, 255, 131)'
                                }])
                            },*/
                            data: this.serie2,
                        }
                    ]
                }
                myChart.setOption(options, true);
            }
        }
    }
</script>