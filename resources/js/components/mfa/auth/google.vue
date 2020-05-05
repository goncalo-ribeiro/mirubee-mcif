<template>
    <div class="container" id="mfa-form">
            <div class="row my-5">
                <div class="col-md-8 m-auto animatebottom">
                    <div class="jumbotron">
                        <h1 class="display-4">MFA - Google</h1>
                        <p class="lead">use your google authenticator app to authenticate yourself</p>
                        <hr class="my-4">

                        <p>please enter the generated one time password in the field below:</p>

                        <div class="form-row">
                            <div class=" col-md-3">
                            </div>
                            <div class=" col-md-6">
                                <input type="text" class="form-control" id="google-activation-code" placeholder="google authenticator code" v-model="code">
                                <input id="remember" class="form-check-input ml-0"  v-model="remember" type="checkbox">
                                <label style="margin-left: 1.25rem" for="remember">remember this device</label>
                                                               
                            </div>
                            <div class=" col-md-3">
                            </div>
                        </div>
                        
                        <div style="height:5vh; width: 100%">
                        </div>

                        <div class="form-row mt-3">
                            <div class=" col-md-4">
                                <button type="button" class="btn btn-danger" style="width: 100%" v-on:click="goBack">back</button>
                            </div>
                            <div class=" col-md-4">                                
                            </div>
                            <div class=" col-md-4">
                                <button :disabled="!code" type="button" id="send-email-code-auth" class="btn btn-primary" style="width: 100%" v-on:click="sendCode">authenticate</button>
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
        },
        data: function(){
            return {
                code: null,
                remember: false,
            }
        },
        props: {
            user: Object,
        },
        computed:{
        },
        methods:{
            goBack(){
                this.$router.push({ name: 'mfaAuthentication',  params: { user: this.user,}})
            },
            sendCode(){
                console.log('sendCode', this.code);
                if(this.code != ''){
                    $('#send-google-code-auth').prop('disabled', true);
                    axios.post(myUrl+"/api/mfa/auth/google", {code: this.code, remember: this.remember})
                    .then( response => {
                        console.log(response.data);
                        Vue.toasted.show('successfully authenticated', { icon : 'check', type: 'success'});

                        // this.$emit('user-updated', response.data.user);

                        this.$router.push({ name: 'siteRetriever'}).catch(err => 
                        {
                            console.log(err)
                        });
                        $('#send-google-code-auth').prop('disabled', false);
                    })
                    .catch(error => {
                        console.log(error.response);
                    
                        let errors = error.response.data.errors;
    
                        for (let key in errors){
                            errors[key].forEach(err => 
                                Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                            );
                        }
                        $('#send-google-code-auth').prop('disabled', false);
                    })
                }
                    
            },
        }
    }
</script>