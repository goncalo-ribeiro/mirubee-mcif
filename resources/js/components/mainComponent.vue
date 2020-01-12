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

                        <li class="nav-item" v-bind:class="{ active: activeTab == 'tariffs' }" v-on:click="activeTab = 'tariffs';activeSiteId = -1">
                            <router-link class="nav-link" to="/tariffs">tariffs</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'alerts' }" v-on:click="activeTab = 'alerts';activeSiteId = -1">
                            <router-link class="nav-link" to="/alerts">alerts</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'reports' }" v-on:click="activeTab = 'reports';activeSiteId = -1">
                            <router-link class="nav-link" to="/reports">reports</router-link>
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
                                <a class="dropdown-item" href="" onclick="">
                                    editar conta
                                </a>
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
                        >
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
        },
        data() {
            return {
                user: Auth.user,     
                activeTab: 'sites',
                activeSiteId: -1,
                
                siteName: undefined,
                siteLocation: undefined,

                userSites: [],
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
            }
        }
    }
</script>
