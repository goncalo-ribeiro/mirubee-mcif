<template>

<!--
    <div id="echart" style="height:80vh; width:80vw" class="m-a"></div>
-->
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">{{siteName}}</div>

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
<!--
    -->
</template>

<script>
var moment = require('moment');

import Auth from './auth/auth.js';

export default {
        name: "dashboard",
        activated() {
            console.log(this.$route.params)
            this.name = this.$route.params.siteName;
            this.id = this.$route.params.siteId;
        },
        mounted() {
            console.log('Component mounted.')
    
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
        computed: {
            siteName: function () {
                return this.$route.params.siteName;
            },
            siteId: function () {
                return this.$route.params.siteId;
            },
        },
        watch: { 
            '$route.params.siteId': {
                handler: function(search) {
                    console.log(search)
                },
                deep: true,
                immediate: true
            },
            siteName: function (){
                console.log('siteName changed')
            }
        },
        methods:{
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