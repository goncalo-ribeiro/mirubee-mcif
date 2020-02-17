<template>

    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        alerts
                        <span style="float: right!important;">
                            <button type="button" class="btn btn-secondary btn-sm" data-toggle="modal" data-target="#create-alert-modal" @click="setMode('create')">
                                create alert <i class="material-icons" style="font-size:13px; vertical-align: middle;">add</i>
                            </button>
                        </span>
                    </div>

                    <div class="card-body" >
                        <div v-if="alerts.length">
                            <div v-for="alert in alerts" :key="alert.id" class="list-group">
                                
                                <a class="list-group-item clearfix">
                                    {{alert.name}}
                                    <span style="float: right!important;">
                                        <button data-toggle="modal" data-target="#create-alert-modal" type="button" class="btn btn-primary btn-sm" @click="setMode('edit', alert)">edit</button>
                                        <button data-toggle="modal" data-target="#remove-alert" type="button" class="btn btn-danger btn-sm" @click="setMode('remove', alert)">remove</button>
                                    </span>
                                </a>
                            </div>
                        </div>
                        <div v-else style="text-align: center;vertical-align: middle;"> 
                            <h4>
                                you have no created alerts
                            </h4>
                            <h1>
                                ¯\_(ツ)_/¯
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="remove-alert" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >remove alert</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to remove this alert?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" v-on:click="removeAlert()">Erase</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="create-alert-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="mode=='edit'" class="modal-title">edit alert<i class="material-icons" style="padding-left:5px; vertical-align: middle;">edit</i></h5>
                        <h5 v-else class="modal-title">create a new alert <i class="material-icons" style="padding-left:5px; vertical-align: middle;">add</i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        
                        <label for="alert-name">a short description of the alert</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="alert-name" placeholder="alert's name" v-model="AlertName">
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="alert-type">type of alert</label>
                                <select id="alert-type" class="form-control" v-model="AlertType">
                                    <option disabled value="0">choose the desired type</option>
                                    <option value="email">email</option>
                                    <option value="website">website</option>
                                    <option disabled value="sms">sms (not yet available)</option>
                                </select>   
                            </div>
                            <div class="form-group col-md-6">
                                <label for="alert-unit">unit monitored by the alert</label>
                                <select id="alert-unit" class="form-control" v-model="AlertUnit">
                                    <option disabled value="0">choose the desired unit</option>
                                    <option v-for="unit in units" :key="unit" v-bind:value="unit">
                                        {{unit}}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <label for="alert-condition">the needed condition to trigger the alert</label>
                        <div class="input-group mb-3">
                            <select id="alert-condition" class="form-control" v-model="AlertCondition">
                                <option disabled value="0">choose the desired condition</option>
                                <option value="bigger than">bigger than</option>
                                <option value="lesser than">lesser than</option>
                                <option value="equal">equal</option>
                                <option value="between">between</option>
                            </select>   
                        </div>
                        
                        <hr>

                        <span v-if="AlertCondition == 0">choose the type of condition above to the associated values</span>

                        <div v-else-if="AlertCondition != 'between'">
                            <label>{{AlertCondition}}</label>
                            <div class="input-group mb-3">
                                <input type="number" class="form-control" placeholder="value of the unit" v-model="AlertThreshold">
                            </div>
                        </div>
                        
                        <div v-else>
                            <label>{{AlertCondition}}</label>
                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <input type="number" class="form-control" placeholder="first value" v-model="AlertThreshold">
                                </div>
                                <div class="form-group col-md-6">
                                    <input type="number" class="form-control" placeholder="second value" v-model="AlertThreshold2">
                                </div>
                            </div>    
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="submitAlert">{{mode}} alert</button>
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
        activated(){
            console.log('alerts component activated.');
            this.getUserAlerts();
        },
        data: function(){
            return {
                alerts: [],

                AlertId: null,
                AlertName: null,
                AlertUnit: 0,
                AlertType: 0,
                AlertCondition: 0,
                AlertThreshold: null,
                AlertThreshold2: null,

                mode: 'create',

                units: ['voltage', 'current', 'apparent power', 'active power',  'frequency', 'power factor']
            }
        },
        methods:{
            setMode(mode, alert = null){
                this.mode = mode;
                if(alert != null){
                    this.AlertId = alert.id
                    this.AlertName = alert.name
                    this.AlertUnit = alert.unit
                    this.AlertType = alert.type
                    this.AlertCondition = alert.condition
                    this.AlertThreshold = alert.threshold
                    this.AlertThreshold2 = alert.threshold2
                }else{
                    this.AlertId = null
                    this.AlertName = null
                    this.AlertUnit = 0
                    this.AlertType = 0
                    this.AlertCondition = 0
                    this.AlertThreshold = null
                    this.AlertThreshold2 = null
                }
                console.log(this.AlertId);
            },
            getUserAlerts(){
                axios.get(myUrl+"/api/alerts")
                .then(response =>{
                    this.alerts = response.data.data;
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user alerts retrieval)", { icon : 'cancel', type: 'error'});
                })    
            },
            submitAlert(){
                console.log("create alert");
                if (this.AlertName === null || this.AlertUnit === 0 || this.AlertType === 0 || this.AlertCondition === 0 || this.AlertThreshold === null){
                    Vue.toasted.show("Please fill in the required fields", { icon : 'cancel', type: 'error'});
                }
                else{
                    if(this.mode == 'edit'){
                        this.editAlert();
                    }else{
                        this.createAlert();
                    }
                }
            },
            createAlert(){
                axios.post(myUrl+"/api/alerts", { name : this.AlertName, unit : this.AlertUnit, threshold: this.AlertThreshold, threshold2: this.AlertThreshold2 , type: this.AlertType, condition: this.AlertCondition})
                .then(response => {
                    console.log(response);
                    $('#create-alert-modal').modal('hide')
                    Vue.toasted.show('Alert created', { icon : 'check', type: 'success'});
                    this.alerts.push(response.data.alert)

                })
                .catch(error => {
                    console.log(error.response);

                    let errors = error.response.data.errors;

                    for (let key in errors){
                        errors[key].forEach(err => 
                            Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                        );
                    }

                })
            },
            editAlert(){
                axios.put(myUrl+"/api/alerts", {id: this.AlertId, name : this.AlertName, unit : this.AlertUnit, threshold: this.AlertThreshold, threshold2: this.AlertThreshold2 , type: this.AlertType, condition: this.AlertCondition})
                .then(response => {
                    console.log(response);
                    $('#create-alert-modal').modal('hide')
                    Vue.toasted.show('Alert updated', { icon : 'check', type: 'success'});

                    let tempArray = _.clone(this.alerts);
                    let index = this.alerts.findIndex( element => element.id === response.data.alert.id)

                    tempArray[index] = response.data.alert;

                    this.alerts = tempArray;
                })
                .catch(error => {
                    console.log(error.response);

                    let errors = error.response.data.errors;

                    for (let key in errors){
                        errors[key].forEach(err => 
                            Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                        );
                    }

                })                

            },
            removeAlert(){
                console.log('remove', this.AlertId);
                axios.delete(myUrl+"/api/alerts/" + this.AlertId)
                .then( response => {
                    console.log(response);
                    
                    $('#remove-alert').modal('hide')
                    Vue.toasted.show('Alert deleted', { icon : 'check', type: 'success'});
                    
                    let index = this.alerts.findIndex( element => element.id == response.data.id);
                    console.log(index);
                    this.alerts.splice(index, 1);
                })
                .catch(error => {
                    console.log(error.response);
                
                    let errors = error.response.data.errors;

                    for (let key in errors){
                        errors[key].forEach(err => 
                            Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                        );
                    }
                })

            }
        }
    }
</script>