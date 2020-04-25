<template>

    <div class="list-group">
        <a class="list-group-item clearfix" v-for="month in months" :key="month">
            {{month}}
            <!--
            
            -->
            <span style="float: right!important;">
                <router-link style="padding:0" class="nav-link" :to="{ name: 'monthlyReport', params: { year: year, month: month}}">
                    
                    <button type="button" class="btn btn-primary btn-sm">
                        check report 
                        <i class="material-icons" style="padding-left:4px; vertical-align: middle; font-size:16px;">search</i>
                    </button>

                </router-link>
            </span>
        </a>
    </div>
</template>

<script>
export default {
        name: "lista-relatorios",
        mounted() {
            console.log('Component mounted.')
            
        },
        data: function(){
            return {
                
            }
        },
        props:{
            months: Array,
            year: Number,
        },
        methods:{
 
        }
    }
</script>