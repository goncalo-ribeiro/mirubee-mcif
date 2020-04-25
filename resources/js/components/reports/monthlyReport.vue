<template>
    <div>
        <h1>
            Report of {{month}} {{year}}
        </h1>
        <!--
        <router-link class="nav-link" to="/reports/">back</router-link>
        <router-link class="nav-link" :to="{ name: 'reportsList', params: { year: year, meses: monthsByYear[year]}}">back</router-link>
        -->
    </div>

</template>

<script>
export default {
        name: "relatorio-mensal",
        mounted() {
            console.log('Component mounted.')
            
        },
        data: function(){
            return {
                
            }
        },
        props:{
            month: String,
            year: Number, 
        },
        methods:{
 
        }
    }
</script>