<template>

    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        alertas
                        <span style="float: right!important;">
                            <button type="button" class="btn btn-secondary btn-sm">adicionar alerta <i class="material-icons" style="font-size:13px; vertical-align: middle;">add_alert</i> </button>
                        </span>
                    </div>

                    <div class="card-body" >
                        <div class="list-group">
                            <a class="list-group-item clearfix">
                                voltagem superior a 260 Volts
                                <span style="float: right!important;">
                                    <button type="button" class="btn btn-primary btn-sm">consultar</button>
                                    <button type="button" class="btn btn-primary btn-sm">editar</button>
                                    <button type="button" class="btn btn-danger btn-sm">remover</button>
                                </span>
                            </a>
                            <a class="list-group-item clearfix" onclick="alert('Action2 -> Details');">
                                consumo superior a 350 kwh
                                <span style="float: right!important;">
                                    <button type="button" class="btn btn-primary btn-sm">consultar</button>
                                    <button type="button" class="btn btn-primary btn-sm">editar</button>
                                    <button type="button" class="btn btn-danger btn-sm">remover</button>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--
    -->
</template>

<script>
export default {
        name: "alertas",
        mounted() {
            console.log('Component mounted.')
            
        },
        data: function(){
            return {
            }
        },
        methods:{
 
        }
    }
</script>