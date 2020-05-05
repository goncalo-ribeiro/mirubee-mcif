<template>
    <div class="container" id="mfa-form">
            <div class="row my-5">
                <div class="col-md-8 m-auto animatebottom">
                    <div class="jumbotron">
                        <h1 class="display-4">MFA - Email</h1>
                        <p class="lead">an email was sent to your address with a code to authenticate yourself</p>
                        <hr class="my-4">

                        <p>please enter the code in the field below:</p>

                        <div class="form-row">
                            <div class=" col-md-3">
                            </div>
                            <div class=" col-md-6">
                                <input type="text" class="form-control" id="email-activation-code" placeholder="email authentication code" v-model="code">
                                <span>
                                    <input class="form-check-input"  v-model="remember" type="checkbox">
                                    remember this device
                                </span>
                                
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
                                <button type="button" id="send-email-code-auth" class="btn btn-primary" style="width: 100%" v-on:click="sendCode">authenticate</button>
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
        name: "mfa-email-setup",
        mounted() {
            console.log('mfa email setup component mounted.');
        },
        activated(){
            console.log('mfa email setup component activated.');
            this.sendEmail();
        },
        data: function(){
            return {
                code: '',
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
                    $('#send-email-code-auth').prop('disabled', true);
                    axios.post(myUrl+"/api/mfa/auth/email", {code: this.code, remember: this.remember})
                    .then( response => {
                        console.log(response.data);
                        Vue.toasted.show('successfully authenticated', { icon : 'check', type: 'success'});

                        // this.$emit('user-updated', response.data.user);

                        this.$router.push({ name: 'siteRetriever'}).catch(err => 
                        {
                            console.log(err)
                        });
                        $('#send-email-code-auth').prop('disabled', false);
                    })
                    .catch(error => {
                        console.log(error.response);
                    
                        let errors = error.response.data.errors;
    
                        for (let key in errors){
                            errors[key].forEach(err => 
                                Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                            );
                        }
                        $('#send-email-code-auth').prop('disabled', false);
                    })
                }
                    
            },
            sendEmail(){
                axios.post(myUrl+"/api/mfa/auth/email/code")
                .then( response => {
                    console.log(response);
                    Vue.toasted.show('Authentication Email sent', { icon : 'check', type: 'success'});
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