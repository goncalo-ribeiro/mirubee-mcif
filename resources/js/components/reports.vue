<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        reports
                        <ul class="nav nav-tabs card-header-tabs mt-2">
                            <li class="nav-item" style="cursor: pointer;" v-on:click="activeYear = '2019', meses=['outubro', 'novembro', 'dezembro']">
                                <a class="nav-link" v-bind:class="{ active: activeYear == '2019' }">2019</a>
                            </li>
                            <li class="nav-item" style="cursor: pointer;" v-on:click="activeYear = '2020', meses=['janeiro', 'fevereiro', 'março', 'abril', 'maio']">
                                <a class="nav-link" v-bind:class="{ active: activeYear == '2020' }">2020</a>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body">
                        <reportsList v-bind:meses="meses"></reportsList>
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
            
        },
        data: function(){
            return {
                activeYear: '2019',
                meses: ['outubro', 'novembro', 'dezembro']
            }
        },
        methods:{
 
        }
    }
</script>

<!--
v-bind:meses="['outubro', 'novembro', 'dezembro']"
v-bind:meses="['janeiro', 'fevereiro', 'março', 'abril', 'maio']"
    -->