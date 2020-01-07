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
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'sites' }" v-on:click="activeTab = 'sites'">
                            <router-link class="nav-link" to="/sites/">sites</router-link>
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

                            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
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
    </div>
</template>

<script>
    import Auth from './auth/auth.js';

    export default {
        mounted() {
            console.log(this.user)
            console.log('mainComponent mounted.')
        },
        data() {
            return {
                user: Auth.user,     
                activeTab: 'sites',
            }
        },
    }
</script>
