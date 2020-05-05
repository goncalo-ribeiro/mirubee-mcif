<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand">
                    Mirubee-mcif
                </a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="label">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto" >

                        <li class="nav-item dropdown" >
                            
                            <a id="sitesDropdown" v-bind:class="{ active: activeTab == 'sites' }" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                                sites
                            </a>

                            <div class="dropdown-menu" aria-labelledby="sitesDropdown">
                                
                                <a class="dropdown-item" data-toggle="modal" style="cursor: pointer;" data-target="#create-site-modal">
                                    create a new site <i class="material-icons" style="padding-left:5px; vertical-align: middle;">add</i>
                                </a>

                                <div v-if="userSites.length">
                                    <div class="dropdown-divider"></div>

                                    <router-link  v-for="site in userSites" :key="site.id" class="dropdown-item" 
                                    :to="{ name: 'sites', params: { siteId: site.id, siteName: site.name, siteLocation: site.location}}"
                                    v-bind:class="{ active: activeSiteId === site.id }">
                                         <span v-on:click="clickSite(site)">{{site.name}}</span>
                                    </router-link>

                                </div>
                            </div>
                            
                            
<!-- 
:to="{ name: 'sites', params: { siteId: site.id }}">
:to="'/sites/' + site.id"
-->

                            

                        </li>

                        <li class="nav-item" v-bind:class="{ active: activeTab == 'devices' }" v-on:click="activeTab = 'devices';activeSiteId = -1">
                            <router-link class="nav-link" :to="{ name: 'devices', params: { sites: userSites}}">devices</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'tariffs' }" v-on:click="activeTab = 'tariffs';activeSiteId = -1">
                            <router-link class="nav-link"
                                :to="{ name: 'tariffs', params: { sites: userSites}}">tariffs</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'alerts' }" v-on:click="activeTab = 'alerts';activeSiteId = -1">

                            <router-link class="nav-link"
                                :to="{ name: 'alerts', params: { notifications: user.notifications, unreadNotifications: unreadNotifications}}">
                                alerts <span v-if="notifications > 0" class="badge badge-info">{{sumObjectProperties(unreadNotifications)}}</span>
                            </router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'reports' }" v-on:click="activeTab = 'reports';activeSiteId = -1">
                            <router-link class="nav-link" :to="{ name: 'reports', params: {fromMain: true}}">reports</router-link>
                        </li>
                    </ul>
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                                {{user.name}}
                            </a>

                            <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="" onclick="">
                                    logout
                                </a>
                                <router-link class="dropdown-item" :to="{ name: 'mfaSetup', params: { user: user} }">
                                    mfa setup
                                </router-link>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
        <div>
            <transition appear name="slide-fade" mode="out-in">
                <keep-alive>
                    <router-view 
                        @site-deleted="siteDeleted"
                        @sites-retrieved="sitesRetrieved"
                        @site-updated="siteUpdated"
                        @tariff-updated="tariffUpdated"
                        @tariff-deleted="tariffDeleted"
                        @notifications-read="readNotifications"
                        @delete-notification="deleteNotification"
                        @delete-alert-notifications="deleteAlertNotifications"
                        @user-updated="userUpdated">
                    </router-view>
                </keep-alive>
            </transition>
        </div>

        <div class="modal fade" id="create-site-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">create a new site <i class="material-icons" style="padding-left:5px; vertical-align: middle;">add</i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        
                        <label for="site-name">your site's name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="site-name" placeholder="site's name" v-model="siteName" v-on:keyup.enter="createSite">
                        </div>

                        <label for="site-location">your site's location (optional)</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="site-location" placeholder="site's location" v-model="siteLocation" v-on:keyup.enter="createSite">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="createSite">create site</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Auth from './auth/auth.js';
    import { get } from 'http';

    export default {
        mounted() {
            console.log('mainComponent mounted.')

            $('#create-site-modal').on('hidden.bs.modal', function () {
                console.log('hidden')
                $('#site-name').val("");
                $('#site-location').val("");
            })

            $('#create-site-modal').on('shown.bs.modal', function () {
                console.log('shown')
                $('#site-name').focus();
            })
            //this.getReports();
        },
        props: {
            sites: {
                type: Array,
            },
        },
        computed:{
            selectedProduct: function(){
                if(this.tempDevice.site.id == 0){
                    return null;
                }else{
                    let index = this.products.findIndex( product => product.id === this.tempDevice.product_id)
                    return this.products[index];
                }
            },
            notifications: function () {
                let notificationCounter = 0;
                this.user.notifications.forEach(notification =>{
                        if (notification.read_at === null){
                            notificationCounter++;
                        }
                    }
                );
                return notificationCounter;
            },
            unreadNotifications: function(){
                let unreadNotifications = {};
                this.user.notifications.forEach(notification =>{
                        if (notification.read_at === null){
                            if(unreadNotifications[notification.data.alert_id] === undefined){
                                unreadNotifications[notification.data.alert_id] = 1
                            }else{
                                unreadNotifications[notification.data.alert_id]++
                            }
                        }
                    }
                );
                return unreadNotifications;
                  
            }
        },
        data() {
            return {
                user: Auth.user,     
                activeTab: 'sites',
                activeSiteId: -1,
                
                siteName: undefined,
                siteLocation: undefined,

                userSites: [],
                userReports: {},
                reportsYears: [],
            }
        },
        methods:{
            clickSite(site){
                this.activeTab = 'sites';
                this.activeSiteId = site.id
                //console.log(site)
            },
            createSite(){
                if( this.siteName == "" ){
                    Vue.toasted.show("The site's name can't be empty", { icon : 'cancel', type: 'error'});
                    return;
                }
                axios.post(myUrl+"/api/sites", { name : this.siteName, location : this.siteLocation})
                .then( response => {
                    console.log(response);
                    $('#create-site-modal').modal('hide')
                    Vue.toasted.show('Site created', { icon : 'check', type: 'success'});
                    this.userSites.push(response.data.site)
                    this.$router.push({ name: 'sites', params: { siteId: response.data.site.id, siteName: response.data.site.name, siteLocation: response.data.site.location}})
                    this.activeSiteId = response.data.site.id;
                    this.activeTab = 'sites'
                })
                .catch(error => {
                    console.log(error.response);
                    
                    //error.response.errors.forEach(element => console.log(element));

                    let errors = error.response.data.errors;

                    for (let key in errors){
                        errors[key].forEach(err => 
                            Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                        );
                    }

                })

                console.log("continue")
            },
            siteDeleted(siteId){
                console.log("site deleted", siteId)
                let index = this.userSites.findIndex( element => element.id === siteId)
                this.userSites.splice(index, 1)
                if (this.userSites.length){
                    this.$router.push({ name: 'sites', params: { siteId: this.userSites[0].id, siteName: this.userSites[0].name, siteLocation: this.userSites[0].location}})
                    this.activeSiteId = this.userSites[0].id;
                }else{
                    //this.$router.push({ path: '/alerts'})
                    this.$router.push({ name: 'sitePicker'}).catch(err => 
                    {
                        console.log(err)
                    })
                    this.activeSiteId = -1;
                }
            },
            sitesRetrieved(response){
                this.userSites = response.data
                if (this.userSites.length){
                    this.$router.push({ name: 'sites', params: { siteId: this.userSites[0].id, siteName: this.userSites[0].name, siteLocation: this.userSites[0].location}})
                    this.activeSiteId = this.userSites[0].id;
                }else{
                    //this.$router.push({ path: '/alerts'})
                    this.$router.push({ name: 'sitePicker'}).catch(err => 
                    {
                        console.log(err)
                    })
                    this.activeSiteId = -1;
                }
            },
            siteUpdated(site){
                console.log("site updated", site)
                let index = this.userSites.findIndex( element => element.id === site.id)

                this.userSites[index] = site;
                this.$router.push({ name: 'sites', params: { siteId: site.id, siteName: site.name, siteLocation: site.location}})
                this.activeSiteId = site.id
            },
            tariffUpdated(siteID, tariff){
                console.log(siteID, tariff)
                let index = this.userSites.findIndex( element => element.id === siteID)

                this.userSites[index].tariff = tariff;
            },
            tariffDeleted(siteID){
                let index = this.userSites.findIndex( element => element.id === siteID)

                this.userSites[index].tariff = null;
            },
            userUpdated(user){
                this.user = user;
            },

            readNotifications(alertId){
                this.user.notifications.forEach(notification => {
                    if (notification.data.alert_id == alertId){
                        notification.read_at = 1;
                    }
                });
            
                axios.put(myUrl+"/api/alerts/" + alertId + "/notifications")
                .then( response => {
                    console.log(response);
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

            deleteNotification(notification){
                this.user.notifications.forEach( (item, index) => {
                    if (item.id == notification.id){

                        axios.delete(myUrl+ "/api/alerts/notifications/" + notification.id)
                        .then(response => {
                            console.log(response);
                            Vue.toasted.show(response.data.message, { icon : 'delete', type: 'success'});
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

                        this.user.notifications.splice(index, 1);
                    }
                });
            },

            deleteAlertNotifications(alertId){
                this.user.notifications.forEach( (item, index) => {
                    if (item.data.alert_id == alertId){
                        this.user.notifications.splice(index, 1);
                    }
                });

                axios.delete(myUrl+ "/api/alerts/"+ alertId +"/notifications/")
                .then(response => {
                    console.log(response);
                    Vue.toasted.show(response.data.message, { icon : 'delete', type: 'success'});
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

            sumObjectProperties(obj){
                var sum = 0;
                for( var el in obj ) {
                    if( obj.hasOwnProperty( el ) ) {
                        sum += parseFloat( obj[el] );
                        }
                    }
                return sum;
            },
/*
            getReports(){
                axios.get(myUrl+"/api/reports")
                .then(response =>{
                    console.log(response.data);

                    response.data.forEach(report => {
                        if (this.userReports[report.year]){
                            this.userReports[report.year].push(report.month)
                        }else{
                            this.userReports[report.year] = [];
                            this.userReports[report.year].push(report.month);
                        }
                        if (!this.reportsYears.includes(report.year)){
                            this.reportsYears.push(report.year);
                        }
                                            
                    })
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's reports retrieval)", { icon : 'cancel', type: 'error'});
                })    
            },*/
            
        }
    }
</script>
