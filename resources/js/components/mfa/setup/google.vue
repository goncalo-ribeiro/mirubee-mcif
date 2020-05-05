<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        multi factor authentication google setup
                    </div>

                    <div class="card-body" >
                        <div v-if="!qrCode">
                            <p>a qr code is being generated, please wait until the process is done.</p>
                        </div>

                        <div v-else>
                            <p>set up your google authenticator app by scanning the qr code below:</p>
                            <div>
                                <img style=" display: block; margin-left: auto; margin-right: auto;" :src="qrCode">
                            </div>
                            <p>alternatively, you can use this code: {{ secret }}</p>
                            
                            <p>please enter the code in the field below:</p>

                            <div class="form-row">
                                <div class=" col-md-3">
                                </div>
                                <div class=" col-md-6">
                                    <input type="text" class="form-control" id="google-activation-code" placeholder="google code" v-model="code">
                                </div>
                                <div class=" col-md-3">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-row mt-3">
                            <div class=" col-md-4">
                                <!--
                                <button type="button" class="btn btn-danger" style="width: 100%" v-on:click="goBack">back</button>
                                -->
                            </div>
                            <div class=" col-md-4">                                
                            </div>
                            <div class=" col-md-4">
                                <button :disabled="!qrCode || !code" type="button" id="send-google-code" class="btn btn-primary" style="width: 100%" v-on:click="sendCode">activate google authenticator</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>  
    </div>
</template>

<script>
export default {
        name: "mfa-google-setup",
        mounted() {
            console.log('mfa google setup component mounted.');
        },
        activated(){
            console.log('mfa google setup component activated.');
            this.getQRCode();
        },
        data: function(){
            return {
                code: null,
                qrCode: null,
                secret: null,
            }
        },
        props: {
            user: Object,
        },
        computed:{
        },
        methods:{
            sendCode(){
                console.log('sendCode', this.code);
                if(this.code != ''){
                    $('#send-google-code').prop('disabled', true);
                    axios.post(myUrl+"/api/mfa/setup/google", {code: this.code, secret: this.secret})
                    .then( response => {
                        console.log(response.data);
                        Vue.toasted.show('Google authenticator activated!', { icon : 'check', type: 'success'});
                        this.$emit('user-updated', response.data.user);

                        this.$router.push({ name: 'mfaSetup', params: { user: response.data.user,}}).catch(err => 
                        {
                            console.log(err)
                        });
                        $('#send-google-code').prop('disabled', false);
                    })
                    .catch(error => {
                        console.log(error.response);
                    
                        let errors = error.response.data.errors;
    
                        for (let key in errors){
                            errors[key].forEach(err => 
                                Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                            );
                        }
                        $('#send-google-code').prop('disabled', false);
                    })
                }
                    
            },
            getQRCode(){
                axios.post(myUrl+"/api/mfa/setup/activateGoogle")
                .then( response => {
                    console.log(response.data);
                    this.qrCode = response.data.qrCode;
                    this.secret = response.data.secret;
                    Vue.toasted.show('A QR code has been generated', { icon : 'check', type: 'success'});
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