<template>

<!--
-->
    <div id="chart" style="height:80vh; width:80vw" class="m-a"></div>
<!--
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Dashboard</div>

                    <div class="card-body" >
                        Dashboard body
                        <div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    -->
</template>

<script>
    export default {
        name: "dashboard",
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
                
                this.setupEchartGraph();
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

            setupEchartGraph(){
                var dom = document.getElementById("chart");
                var myChart = echarts.init(dom);

                let gap = (this.yMax - this.yMin) * .10;
                console.log(gap)

                var option = {
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
                    xAxis: {
                        type: 'category',
                        boundaryGap: false,
                        data: this.xAxisData,
                    },
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
                            smooth:true,
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
                            data: this.yAxisData,
                        }
                    ]
                }

                myChart.setOption(option, true);
            }
        }
    }
</script>
