<template>

    <div class="list-group">
        <a class="list-group-item clearfix" v-for="mes in meses">
            {{mes}}
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
            meses: []   
        },
        methods:{
 
        }
    }
</script>