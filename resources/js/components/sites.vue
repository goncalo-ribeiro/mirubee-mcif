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
                                <div id="echart" style="height:70vh" class="m-a">
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
            /*
            axios.get(myUrl + '/api/readings', ).
            then( success => {
                //console.log(this)
                this.data = success.data;
                //console.log(this.data);

                this.yAxisData = [];
                this.xAxisData = [];
                this.newData = [];
                this.newerData = [];
                this.test = [
                    ["2018-08-15T10:04:01.339Z",1],[ "2018-08-15T10:14:13.914Z",2],[ "2018-08-15T10:40:03.147Z",3],[ "2018-08-15T11:50:14.335Z",4]
                ];

                success.data.forEach(element => {
                    this.yAxisData.push(element.v1)

                    if (this.yMax < element.v1) this.yMax = element.v1;

                    if (this.yMin > element.v1) this.yMin = element.v1;
                    
                    let auxDate = new Date(element.time*1000);
                    let auxVolt = element.v1;
                    
                    this.xAxisData.push([auxDate.getHours(), auxDate.getMinutes(), auxDate.getSeconds()].join(':'))

                    let newElement = {
                        name: auxDate.toString(),
                        value: [
                            [auxDate.getFullYear(), auxDate.getMonth() + 1, auxDate.getDate()].join('/'),
                            auxVolt
                        ]
                    }
                    this.newData.push(newElement);
                    
                    this.newerData.push( [auxDate.toJSON(), auxVolt] );
                });
                
                this.setupEchartGraph();
            });
            */
        },
        data: function(){
            return {
                tempName: undefined,
                tempLocation: undefined,

                smooth: false,
                timestampsRepetidos: true,

                data: undefined,
                newerData: undefined,
                yAxisData: undefined,
                xAxisData: undefined,
                yMax: Number.NEGATIVE_INFINITY,
                yMin: Number.POSITIVE_INFINITY,
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
            refresh(){
                this.yAxisData = [];
                this.xAxisData = [];
                this.newData = [];
                this.newerData = [];
                this.test = [
                    ["2018-08-15T10:04:01.339Z",1],[ "2018-08-15T10:14:13.914Z",2],[ "2018-08-15T10:40:03.147Z",3],[ "2018-08-15T11:50:14.335Z",4]
                ];
                let timestamps = [];

                this.data.forEach(element => {
                    if(!timestamps.includes(element.time)){
                        this.yAxisData.push(element.v1)

                        if (this.yMax < element.v1) this.yMax = element.v1;

                        if (this.yMin > element.v1) this.yMin = element.v1;
                        
                        let auxDate = new Date(element.time*1000);
                        let auxVolt = element.v1;
                        
                        this.xAxisData.push([auxDate.getHours(), auxDate.getMinutes(), auxDate.getSeconds()].join(':'))

                        let newElement = {
                            name: auxDate.toString(),
                            value: [
                                [auxDate.getFullYear(), auxDate.getMonth() + 1, auxDate.getDate()].join('/'),
                                auxVolt
                            ]
                        }
                        this.newData.push(newElement);
                        
                        this.newerData.push( [auxDate.toJSON(), auxVolt] );
                        if(!this.timestampsRepetidos){
                            timestamps.push(element.time);
                        } 
                    }
                });
                
                this.setupEchartGraph();
            },

            setupEchartGraph(){
                var dom = document.getElementById("echart");
                var myChart = echarts.init(dom);

                let gap = (this.yMax - this.yMin) * .10;
                console.log(gap)

                var option1 = {
                    tooltip: {
                        trigger: 'axis',
                        position: function (pt) {
                            return [pt[0], '10%'];
                        }
                    },
                    title: {
                        left: 'center',
                        text: 'valores voltagem',
                    },
                    toolbox: {
                        feature: {
                            dataZoom: {
                                title: 'zoom',
                                yAxisIndex: 'none'
                            },
                            restore: {
                                title: 'restaurar',
                            },
                            saveAsImage: {
                                title: 'download imagem',
                            }
                        }
                    },
                    calculable : true,
                    xAxis : [
                        {
                            type: 'time',
                            boundaryGap:false,
                            axisLabel: {
                                formatter: (value =>
                                    moment(value).format('HH:mm:ss')
                                    //this.timestampFormatter(value)
                                )
                            }
                        }
                    ],
                    yAxis: {
                        type: 'value',
                        boundaryGap: [0, '100%'],
                        min: Math.round(this.yMin - gap),
                        max: Math.round(this.yMax + gap),
                        //min: 210,
                        //max: 260
                        //scale: true,

                    },
                    dataZoom: [{
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
                        }
                    }
                    ],
                    series: [
                        {
                            name:'voltagem',
                            type:'line',
                            smooth:this.smooth,
                            symbol: 'none',
                            sampling: 'average',
                            itemStyle: {
                                color: 'rgb(255, 70, 131)'
                            },
                            areaStyle: {
                                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [{
                                    offset: 0,
                                    color: 'rgb(255, 158, 68)'
                                }, {
                                    offset: 1,
                                    color: 'rgb(255, 70, 131)'
                                }])
                            },
                            data: this.newerData,
                        }
                    ]
                }
                myChart.setOption(option1, true);
            }
        }
    }
</script>