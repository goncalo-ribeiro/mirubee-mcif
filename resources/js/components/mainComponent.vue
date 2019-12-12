<template>
    <div>
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
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
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'dashboard' }" v-on:click="activeTab = 'dashboard'">
                            <router-link class="nav-link" to="/dashboard/">dashboard</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'tarifario' }" v-on:click="activeTab = 'tarifario'">
                            <router-link class="nav-link" to="/tarifario">tarifário</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'alertas' }" v-on:click="activeTab = 'alertas'">
                            <router-link class="nav-link" to="/alertas">alertas</router-link>
                        </li>
                        <li class="nav-item" v-bind:class="{ active: activeTab == 'relatorios' }" v-on:click="activeTab = 'relatorios'">
                            <router-link class="nav-link" to="/relatorios">relatorios</router-link>
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
                activeTab: 'dashboard',
            }
        },
    }
</script>
