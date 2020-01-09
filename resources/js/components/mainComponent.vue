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

                        <li class="nav-item dropdown" v-bind:class="{ active: activeTab == 'sites' }">
                            
                            <a id="sitesDropdown" class="nav-link dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="cursor: pointer;">
                                sites
                            </a>

                            <div class="dropdown-menu" aria-labelledby="sitesDropdown">
                                
                                <a class="dropdown-item" data-toggle="modal" style="cursor: pointer;" data-target="#create-site-modal">
                                    create a new site <i class="material-icons" style="padding-left:5px; vertical-align: middle;">create</i>
                                </a>

                                <div v-if="userSites.length">
                                    <div class="dropdown-divider"></div>

                                    <router-link  v-for="site in userSites" :key="site.id" class="dropdown-item" :to="{ name: 'sites', params: { siteId: site.id, siteName: site.name}}">
                                        {{site.name}}
                                    </router-link>

                                </div>
                            </div>
                            
                            
<!-- 
:to="{ name: 'sites', params: { siteId: site.id }}">
:to="'/sites/' + site.id"
-->

                            

                        </li>

                        <li class="nav-item" v-bind:class="{ active: activeTab == 'tariffs' }" v-on:click="activeTab = 'tariffs'">
                            <router-link class="nav-link" to="/tariffs">tariffs</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'alerts' }" v-on:click="activeTab = 'alerts'">
                            <router-link class="nav-link" to="/alerts">alerts</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'reports' }" v-on:click="activeTab = 'reports'">
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
                    <router-view></router-view>
                </keep-alive>
            </transition>
        </div>

        <div class="modal fade" id="create-site-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">create a new site <i class="material-icons" style="padding-left:5px; vertical-align: middle;">create</i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        
                        <label for="site-name">your site's name</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="site-name" placeholder="site's name" v-model="siteName">
                        </div>

                        <label for="site-location">your site's location (optional)</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="site-location" placeholder="site's location" v-model="siteLocation">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="createSite">Create site</button>
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

            axios.get(myUrl+"/api/sites")//, { headers: { Accept: 'application/json'}})
            .then(response =>{
                console.log(response);
                this.userSites = response.data.data
            })
            .catch(error =>{
                console.log(error.response);
                Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's site retrieval)", { icon : 'cancel', type: 'error'});
            })
        },
        data() {
            return {
                user: Auth.user,     
                activeTab: 'alerts',
                
                siteName: undefined,
                siteLocation: undefined,

                userSites: [],
            }
        },
        methods:{
            createSite(){
                if( this.siteName == "" ){
                    Vue.toasted.show("the site's name can't be empty", { icon : 'cancel', type: 'error'});
                    return;
                }
                axios.post(myUrl+"/api/sites", { siteName : this.siteName, siteLocation : this.siteLocation})
                .then( success => {
                    console.log("response")
                });

                console.log("continue")
            }
        }
    }
</script>
