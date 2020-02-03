<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        devices
                        <span style="float: right!important;">
                            <button type="button" v-on:click="getDevices" class="btn btn-secondary btn-sm">refresh devices<i class="material-icons" style="font-size:13px; vertical-align: middle;">refresh</i> </button>
                        </span>
                    </div>

                    <div class="card-body" >
                        <div v-if="devices.length == 0" style="text-align: center;vertical-align: middle;">
                            <h4>
                                you have no devices linked to your account
                            </h4>
                            <h1>
                                ¯\_(ツ)_/¯
                            </h1>
                        </div>
                        <div v-else>
                            <table class="table table-hover table-striped">
                                <thead>
                                    <tr class="table-dark">
                                        <th scope="col">mac address</th>
                                        <th scope="col">name</th>
                                        <th scope="col">type</th>
                                        <th scope="col">actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="device in devices" :key="device.id">
                                        <td>{{device.mac_address}}</td>
                                        <td>{{device.name}}</td>
                                        <td>{{device.type}}</td>
                                        <td>
                                            <button type="button" data-toggle="modal" data-target="#view-device-modal" v-on:click="setTempDevice(device)" class="btn btn-primary btn-sm">more details <i class="material-icons" style="padding-left:4px; vertical-align: middle; font-size:16px;">search</i></button>
                                            <button type="button" data-toggle="modal" data-target="#update-device-modal" v-on:click="setTempDevice(device)" class="btn btn-primary btn-sm">edit <i class="material-icons" style="padding-left:4px; vertical-align: middle; font-size:16px;">edit</i></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="update-device-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="tempDevice.name == null" class="modal-title" >update device<i class="material-icons" style="padding-left:5px; vertical-align: middle;">edit</i></h5>
                        <h5 v-else class="modal-title" >update {{tempDevice.name}}<i class="material-icons" style="padding-left:5px; vertical-align: middle;">edit</i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        
                        <label for="update-device-name">your device's name (optional)</label>
                        <div class="input-group mb-3">
                            <input type="text" class="form-control" id="update-device-name" placeholder="device's name" v-model="tempDevice.name" v-on:keyup.enter="updateDevice">
                        </div>

                        <label for="update-device-location">your device's site</label>
                        <div class="input-group mb-3">
                            <select class="form-control" id="update-device-site" v-model="tempDevice.site.id">
                                <option disabled value="0">device's site</option>
                                <option value="-1">no site</option>
                                <option v-for="(site, index) in sites" :key="index" :value="site.id">{{site.name}}</option>
                            </select>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">cancel</button>
                        <button type="button" class="btn btn-primary" v-on:click="updateDevice">update site</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="view-device-modal" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog  modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 v-if="selectedProduct == null" class="modal-title" >view device's details<i class="material-icons" style="padding-left:5px; vertical-align: middle;">search</i></h5>
                        <h5 v-else class="modal-title" >{{selectedProduct.type}}<i class="material-icons" style="padding-left:5px; vertical-align: middle;">search</i></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">

                        <div v-if="selectedProduct != null">
                            <div class="form-row">
                                <div class="form-group col-md-9">
                                    <h5 class="mb-3">{{selectedProduct.desc}}</h5>
                                    <div class="form-row mt-2">
                                        <div class="form-group col-md-6">
                                            <p class="mb-0">device's name:</p>
                                            <h5>{{tempDevice.name}}</h5>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <p class="mb-0">device's mac address:</p>
                                            <h5>{{tempDevice.mac_address}}</h5>
                                        </div>
                                    </div>
                                    <div class="form-row mt-2">
                                        <div class="form-group col-md-6">
                                            <p class="mb-0">device's software:</p>
                                            <h5>{{tempDevice.soft}}</h5>
                                        </div>
                                        <div class="form-group col-md-6">
                                            <p class="mb-0">device's site:</p>
                                            <h5>{{tempDevice.site.name}}</h5>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md-3">
                                    <img style="border-radius: 50%; width: 100%" :src="selectedProduct.image" alt="product-image">
                                </div>
                            </div>
                            

                        </div>
<!--
                        <img style="border-radius: 50%;" :src="selectedProduct.image" alt="product-image">
-->
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">close</button>
                    </div>
                </div>
            </div>
        </div>        
<!--
    -->
    </div>
</template>

<script>
export default {
        name: "devices",
        mounted() {
            console.log('Component mounted.')
            console.log(this.devices.length)
            this.getDevices();
            this.getProducts();

            $('#update-site-modal').on('shown.bs.modal', () => {
                $('#update-device-name').focus();
            }); 
        },
        data: function(){
            return {
                devices: [],
                tempDevice: {name: "", site: {id: 0}},

                products: [],
            }
        },
        props: {
            sites: {
                type: Array,
            },
        },
        computed:{
            selectedProduct: function(){
                if(this.tempDevice.site.id == 0){
                    return null;
                }else{
                    let index = this.products.findIndex( product => product.id === this.tempDevice.product_id)
                    return this.products[index];
                }
            },
        },
        methods:{
            getDevices(){
                axios.get(myUrl+"/api/devices")
                .then(response =>{
                    this.devices = response.data.data;
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's device retrieval)", { icon : 'cancel', type: 'error'});
                })    
            },
            getProducts(){
                axios.get(myUrl+"/api/products")
                .then(response =>{
                    this.products = response.data.data;
                })
                .catch(error =>{
                    console.log(error);
                    Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's device retrieval)", { icon : 'cancel', type: 'error'});
                })
            },
            updateDevice(){
                axios.put(myUrl+"/api/devices/"+this.tempDevice.id, { name: this.tempDevice.name, id: this.tempDevice.id, site: this.tempDevice.site})
                .then( response => {
                    $('#update-device-modal').modal('hide')
                    Vue.toasted.show('Device update', { icon : 'check', type: 'success'});

                    let index = this.devices.findIndex( element => element.id === response.data.device.id)

                    this.devices[index] = response.data.device;

                    let temp = this.devices;
                    this.devices = [];
                    this.devices = temp;
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
            setTempDevice(device){
                this.tempDevice.id =  device.id;
                this.tempDevice.name =  device.name;
                this.tempDevice.site.id = device.site.id;
                this.tempDevice.site.name = device.site.name;
                this.tempDevice.product_id = device.product_id;
                this.tempDevice.mac_address = device.mac_address;
                this.tempDevice.soft = device.soft;
            }
        }
    }
</script>