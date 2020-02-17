<template>
    <div>
        <div class="mt-5 container">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">tariffs</div>

                        <div class="card-body" >

                            <div class="list-group">
                                <a v-for="site in sites" :key="site.id" class="list-group-item clearfix">
                                    {{site.name}}
                                    <span style="float: right!important;">
                                        <div v-if="site.tariff != null">
                                            <button data-toggle="modal" data-target="#update-tariff" type="button" class="btn btn-primary btn-sm"
                                            v-on:click="setupTempTariff(site)">
                                                edit
                                            </button>
                                            <button data-toggle="modal" data-target="#remove-tariff" type="button" class="btn btn-danger btn-sm"
                                            v-on:click="selectedSite = site">
                                                remove
                                            </button>
                                        </div>
                                        <div v-else>
                                            <button data-toggle="modal" data-target="#add-tariff" type="button" class="btn btn-success btn-sm"
                                            v-on:click="selectedSite = site">
                                                set up tariff
                                            </button>
                                        </div>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="remove-tariff" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >remove {{selectedSite.name}}'s' tariff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to erase this tariff?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-danger" v-on:click="removeTariff">Erase</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="modal fade" id="update-tariff" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >update {{selectedSite.name}}'s' tariff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="potencia">contracted power</label>
                                <input autofocus type="text" class="form-control" id="potencia-update" placeholder="kVa" @input="updateExistingTariff('contracted_power', $event)">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">daily power price</label>
                                <input type="text" class="form-control" id="preço-update" placeholder="€" @input="updateExistingTariff('daily_power_price', $event)">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="imposto">tax</label>
                                <input type="number" class="form-control" id="imposto-update" placeholder="%" @input="updateExistingTariff('tax', $event)">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">type of tariff</label>
                                <select class="form-control" v-model="tariff_type_select" @input="updateExistingTariff('tariff_type', $event)">
                                    <option disabled value="0">choose one of the following options</option>
                                    <option value="1">simple</option>
                                    <option value="2">bi-hourly</option>
                                    <option value="3">tri-hourly</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <span v-show="tariff_type_select == 0">choose the type of tariff above to set the consumption price for each time of the day</span>
                        <div class="form-row">
                            <div v-show="tariff_type_select == 1" class="form-group col-md-6">
                                <label for="preco-simples">consumption price</label>
                                <input autofocus type="number" class="form-control" id="preco-simples-update" placeholder="€/kWh" @input="updateExistingTariff('price_simple', $event)">
                            </div>
                            <div v-show="tariff_type_select > 1" class="form-group col-md-6">
                                <label for="vazio">consumption price during off-peak hours</label>
                                <input type="number" class="form-control" id="vazio-update" placeholder="€/kWh" @input="updateExistingTariff('price_off_peak_hours', $event)">
                            </div>
                            <div v-show="tariff_type_select == 2" class="form-group col-md-6">
                                <label for="fora-vazio">consumption price outside off-peak hours</label>
                                <input type="number" class="form-control" id="fora-vazio-update" placeholder="€/kWh" @input="updateExistingTariff('price_outside_off_peak_hours', $event)">
                            </div>
                            <div v-show="tariff_type_select == 3" class="form-group col-md-6">
                                <label for="ponta">consumption price during peak hours</label>
                                <input type="number" class="form-control" id="ponta-update" placeholder="€/kWh" @input="updateExistingTariff('price_peak_hours', $event)">
                            </div>
                        </div>   
                        <div class="form-row">                     
                            <div v-show="tariff_type_select == 3" class="form-group col-md-6">
                                <label for="cheias">consumption price during full time</label>
                                <input type="number" class="form-control" id="cheias-update" placeholder="€/kWh" @input="updateExistingTariff('price_full_time_hours', $event)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="updateTariff">update Tariff</button>
                    </div>
                </div>
            </div>
        </div>         

        <div class="modal fade" id="add-tariff" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" >set up {{selectedSite.name}}'s' tariff</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="potencia">contracted power</label>
                                <input autofocus type="text" class="form-control" id="potencia" placeholder="kVa" @input="updateNewTariff('contracted_power', $event)">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">daily power price</label>
                                <input type="text" class="form-control" id="preço" placeholder="€" @input="updateNewTariff('daily_power_price', $event)">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="imposto">tax</label>
                                <input type="number" class="form-control" id="imposto" placeholder="%" @input="updateNewTariff('tax', $event)">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">type of tariff</label>
                                <select class="form-control" v-model="tariff_type_select" @input="updateNewTariff('tariff_type', $event)">
                                    <option disabled value="0">choose one of the following options</option>
                                    <option value="1">simple</option>
                                    <option value="2">bi-hourly</option>
                                    <option value="3">tri-hourly</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <span v-if="tariff_type_select == 0">choose the type of tariff above to set the consumption price for each time of the day</span>
                        <div class="form-row">
                            <div v-if="tariff_type_select == 1" class="form-group col-md-6">
                                <label for="preco-simples">consumption price</label>
                                <input autofocus type="number" class="form-control" id="preco-simples" placeholder="€/kWh" @input="updateNewTariff('price_simple', $event)">
                            </div>
                            <div v-if="tariff_type_select > 1" class="form-group col-md-6">
                                <label for="vazio">consumption price during off-peak hours</label>
                                <input type="number" class="form-control" id="vazio" placeholder="€/kWh" @input="updateNewTariff('price_off_peak_hours', $event)">
                            </div>
                            <div v-if="tariff_type_select == 2" class="form-group col-md-6">
                                <label for="fora-vazio">consumption price outside off-peak hours</label>
                                <input type="number" class="form-control" id="fora-vazio" placeholder="€/kWh" @input="updateNewTariff('price_outside_off_peak_hours', $event)">
                            </div>
                            <div v-if="tariff_type_select == 3" class="form-group col-md-6">
                                <label for="ponta">consumption price during peak hours</label>
                                <input type="number" class="form-control" id="ponta" placeholder="€/kWh" @input="updateNewTariff('price_peak_hours', $event)">
                            </div>
                        </div>   
                        <div class="form-row">                     
                            <div v-if="tariff_type_select == 3" class="form-group col-md-6">
                                <label for="cheias">consumption price during full time</label>
                                <input type="number" class="form-control" id="cheias" placeholder="€/kWh" @input="updateNewTariff('price_full_time_hours', $event)">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="addTariff">add Tariff</button>
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
        name: "tarifario",
        mounted() {
            console.log('Component mounted.')
            $('#add-tariff').on('hidden.bs.modal', this.clearNewTariff)
            //$('#update-tariff').on('shown.bs.modal', this.setupTempTariff)
            $('#update-tariff').on('hidden.bs.modal', this.clearTempTariff)
        },
        data: function(){
            return {
                tariff_type_select: 0,
                selectedSite:{name: null},
                newTariff: {
                    contracted_power: null,
                    daily_power_price: null,
                    tax: null,
                    tariff_type: null,
                    price_simple: null,
                    price_off_peak_hours: null,
                    price_outside_off_peak_hours: null,
                    price_peak_hours: null,
                    price_full_time_hours: null,
                },
                tempTariff:{
                    contracted_power: null,
                    daily_power_price: null,
                    tax: null,
                    tariff_type: null,
                    price_simple: null,
                    price_off_peak_hours: null,
                    price_outside_off_peak_hours: null,
                    price_peak_hours: null,
                    price_full_time_hours: null,
                }
            }
        },
        props: {
            sites: {
                type: Array,
            },
        },
        methods:{
            addTariff: function(){

                if(this.newTariff.contracted_power == null || this.newTariff.daily_power_price == null || this.newTariff.tax == null || this.newTariff.tariff_type == null){
                    Vue.toasted.show("Please fill in the fields below", { icon : 'cancel', type: 'error'});                    
                    return null;
                }

                let auxTariff = {}

                for (let key in this.newTariff) {
                    if(this.newTariff[key] != null){
                        auxTariff[key] = this.newTariff[key];
                    }
                }

                axios.post(myUrl+"/api/sites/" + this.selectedSite.id + "/tariffs", auxTariff)
                .then( response => {
                    console.log(response);
                    
                    $('#add-tariff').modal('hide')
                    Vue.toasted.show('Tariff set up', { icon : 'check', type: 'success'});
                    this.$emit('tariff-updated', this.selectedSite.id, response.data.tariff);
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
                console.log('addTariff')
            },

            updateTariff: function(){
                if(this.tempTariff.contracted_power == null || this.tempTariff.daily_power_price == null || this.tempTariff.tax == null || this.tempTariff.tariff_type == null){
                    Vue.toasted.show("Please fill in the fields below", { icon : 'cancel', type: 'error'});                    
                    return null;
                }

                let auxTariff = {}

                for (let key in this.tempTariff) {
                    if(this.tempTariff[key] != null){
                        auxTariff[key] = this.tempTariff[key];
                    }
                }

                axios.put(myUrl+"/api/sites/" + this.selectedSite.id + "/tariffs", auxTariff)
                .then( response => {
                    console.log(response);
                    
                    $('#update-tariff').modal('hide')
                    Vue.toasted.show('Tariff updated', { icon : 'check', type: 'success'});
                    this.$emit('tariff-updated', this.selectedSite.id, response.data.tariff);
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
                console.log('updateTariff')

            },

            updateNewTariff: function(prop, event){
                let input = event.target.value;

                if(prop == 'tariff_type'){
                    if(input == 1){
                        Vue.set(this.newTariff, 'tariff_type', 'simple');
                    }
                    if(input == 2){
                        Vue.set(this.newTariff, 'tariff_type', 'bi-hourly');
                    }
                    if(input == 3){
                        Vue.set(this.newTariff, 'tariff_type', 'tri-hourly');
                    }
                    this.newTariff.price_simple= null;
                    $('#preco-simples').val("");
                    this.newTariff.price_off_peak_hours= null;
                    $('#vazio').val("");
                    this.newTariff.price_outside_off_peak_hours= null;
                    $('#fora-vazio').val("");
                    this.newTariff.price_peak_hours= null;
                    $('#ponta').val("");
                    this.newTariff.price_full_time_hours= null;
                    $('#cheias').val("");
                }
                else{
                    Vue.set(this.newTariff, prop, event.target.value);
                }
            },

            updateExistingTariff: function(prop, event){
                let input = event.target.value;

                if(prop == 'tariff_type'){
                    if(input == 1){
                        Vue.set(this.tempTariff, 'tariff_type', 'simple');
                    }
                    if(input == 2){
                        Vue.set(this.tempTariff, 'tariff_type', 'bi-hourly');
                    }
                    if(input == 3){
                        Vue.set(this.tempTariff, 'tariff_type', 'tri-hourly');
                    }
                    this.tempTariff.price_simple= null;
                    $('#preco-simples-update').val("");
                    this.tempTariff.price_off_peak_hours= null;
                    $('#vazio-update').val("");
                    this.tempTariff.price_outside_off_peak_hours= null;
                    $('#fora-vazio-update').val("");
                    this.tempTariff.price_peak_hours= null;
                    $('#ponta-update').val("");
                    this.tempTariff.price_full_time_hours= null;
                    $('#cheias-update').val("");
                }
                else{
                    Vue.set(this.tempTariff, prop, event.target.value);
                }
            },

            clearNewTariff(){
                console.log('clearNewTariff')
                this.newTariff = {contracted_power: null,
                    daily_power_price: null,
                    tax: null,
                    tariff_type: null,
                    price_simple: null,
                    price_off_peak_hours: null,
                    price_outside_off_peak_hours: null,
                    price_peak_hours: null,
                    price_full_time_hours: null,
                }
                $('#preco-simples').val("");
                $('#vazio').val("");
                $('#fora-vazio').val("");
                $('#ponta').val("");
                $('#cheias').val("");

                this.tariff_type_select = 0;
                this.selectedSite = {name: null};

            },

            setupTempTariff: function(site){
                console.log('setupTempTariff')
                this.selectedSite = site;
                for (let key in site.tariff) {
                    if(site.tariff[key] != null){
                        this.tempTariff[key] = site.tariff[key];
                    }
                }
                switch(this.tempTariff.tariff_type) {
                    case 'simple':
                        this.tariff_type_select = 1;
                        break;
                    case 'bi-hourly':
                        this.tariff_type_select = 2;
                        break;
                    case 'tri-hourly':
                        this.tariff_type_select = 3;
                        break;                        
                    default:
                        this.tariff_type_select = 0;
                } 
                $('#potencia-update').val(this.tempTariff.contracted_power)
                $('#preço-update').val(this.tempTariff.daily_power_price)
                $('#imposto-update').val(this.tempTariff.tax)

                $('#preco-simples-update').val(this.tempTariff.price_simple);
                $('#vazio-update').val(this.tempTariff.price_off_peak_hours);
                $('#fora-vazio-update').val(this.tempTariff.price_outside_off_peak_hours);
                $('#ponta-update').val(this.tempTariff.price_peak_hours);
                $('#cheias-update').val(this.tempTariff.price_full_time_hours);
                
            },

            clearTempTariff(){
                console.log('clearTempTariff')
                this.tempTariff = {contracted_power: null,
                    daily_power_price: null,
                    tax: null,
                    tariff_type: null,
                    price_simple: null,
                    price_off_peak_hours: null,
                    price_outside_off_peak_hours: null,
                    price_peak_hours: null,
                    price_full_time_hours: null,
                }
                $('#preco-simples-update').val("");
                $('#vazio-update').val("");
                $('#fora-vazio-update').val("");
                $('#ponta-update').val("");
                $('#cheias-update').val("");

                this.tariff_type_select = 0;
                this.selectedSite = {name: null};
            },

            removeTariff(){
                axios.delete(myUrl+"/api/sites/" + this.selectedSite.id + "/tariffs")
                .then( response => {
                    console.log(response);
                    
                    $('#remove-tariff').modal('hide')
                    Vue.toasted.show('Tariff deleted', { icon : 'check', type: 'success'});
                    this.$emit('tariff-deleted', this.selectedSite.id, response.data.tariff);
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