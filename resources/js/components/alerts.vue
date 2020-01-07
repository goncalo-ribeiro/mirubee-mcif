<template>

    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        alerts
                        <span style="float: right!important;">
                            <button type="button" class="btn btn-secondary btn-sm">create alert <i class="material-icons" style="font-size:13px; vertical-align: middle;">add_alert</i> </button>
                        </span>
                    </div>

                    <div class="card-body" >
                        <div class="list-group">
                            <a class="list-group-item clearfix">
                                voltage higher than 260 Volts
                                <span style="float: right!important;">
                                    <button type="button" class="btn btn-primary btn-sm">view</button>
                                    <button type="button" class="btn btn-primary btn-sm">edit</button>
                                    <button type="button" class="btn btn-danger btn-sm">remove</button>
                                </span>
                            </a>
                            <a class="list-group-item clearfix" onclick="alert('Action2 -> Details');">
                                consumption higher than 350 kwh
                                <span style="float: right!important;">
                                    <button type="button" class="btn btn-primary btn-sm">view</button>
                                    <button type="button" class="btn btn-primary btn-sm">edit</button>
                                    <button type="button" class="btn btn-danger btn-sm">remove</button>
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