<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <!--<div v-if="Object.entries(reports).length">-->
                <div v-if="true">
                    <div class="card">
                        <div class="card-header">
                            reports
                            <ul class="nav nav-tabs card-header-tabs mt-2">
                                <li class="nav-item" style="cursor: pointer;" v-for="year in years" :key=year v-on:click=" activeYear = year">
                                    <router-link style="outline: 0 none !important; border: 0;" class="nav-link" v-bind:class="{ active: activeYear == year }"
                                    :to="{ name: 'reportsList', params: { year: year, months: reports[year]}}">
                                        {{year}}
                                    </router-link>
                                    <!--
                                    <a class="nav-link" v-bind:class="{ active: activeYear == year }">{{year}}</a>
                                    -->
                                </li>
                            </ul>

                        </div>

                        <div class="card-body">
                            <transition appear name="slide-fade" mode="out-in">
                                <keep-alive>
                                    <router-view >
                                    </router-view>
                                </keep-alive>
                            </transition>
                        </div>
                    </div>
                </div>
                
                <div v-else>
                    <div class="card">
                        <div class="card-header">
                            reports
                        </div>

                        <div class="card-body" style="text-align: center;vertical-align: middle;">
                            <h4>
                                you have no devices linked to your account
                            </h4>
                            <h1>
                                ¯\_(ツ)_/¯
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
        name: "relatorios",
        mounted() {
            console.log('Component mounted.')
            this.getReports();
        },
        props:{
            fromMain: Boolean,
        //     reports: Object,
        //     years: Array,
        },
        watch:{
            fromMain: function (val) {
                console.log('watcher (fromMain)', val)
                if (val == true){
                    if(this.years.length){
                        this.activeYear = this.years[0];
                        
                        this.$router.push({ name: 'reportsList', params: { year: this.years[0], months: this.reports[this.years[0]]}}).catch(err => 
                        {
                            console.log(err)
                        });
                    }  
                    //this.fromMain = false;
                }
            },
        },
        
        activated(){
            console.log('Component activated')
            // if(this.years.length){
            //     this.activeYear = this.years[0];
            //     this.$router.push({ name: 'reportsList', params: { year: this.years[0], months: this.monthsByYear[this.years[0]]}}).catch(err => 
            //     {
            //         console.log(err)
            //     });
            // }              
        },
        // updated(){
        //     console.log('Component updated')
        // },
        data: function(){
            return {
                activeYear: null,
                // reports: [],
                years: [],
                reports: {},
            }
        },
        computed:{
            /*
            monthsMap: function(){

            },
*/
        },
        methods:{
            getReports(){
                axios.get(myUrl+"/api/reports")
                .then(response =>{
                    console.log(response.data);
                    this.populateData(response.data);                         
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's reports retrieval)", { icon : 'cancel', type: 'error'});
                })    
            },
            populateData(reports){
                reports.forEach(report => {
                    if (this.reports[report.year]){
                        this.reports[report.year].push(report.month)
                    }else{
                        this.reports[report.year] = [];
                        this.reports[report.year].push(report.month);
                    }
                });
                reports.forEach(report => {
                    if (!this.years.includes(report.year)){
                        this.years.push(report.year);
                    }
                });
                if(this.years.length){
                    this.activeYear = this.years[0];
                    this.$router.push({ name: 'reportsList', params: { year: this.years[0], months: this.reports[this.years[0]]}}).catch(err => 
                    {
                        console.log(err)
                    });
                }    

            },
        },
        
    }
</script>