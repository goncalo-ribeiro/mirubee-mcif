<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        multi factor authentication email setup
                    </div>

                    <div class="card-body" >

                        <p>you need to ensure you have access to the email account first, an email with a secret code was sent to your account in order to do so.</p>
                        <p>please enter the sent code in the field below:</p>

                        <div class="form-row">
                            <div class=" col-md-3">
                                <input type="text" class="form-control" id="email-activation-code" placeholder="email code" v-model="code">
                            </div>
                            <div class=" col-md-3">
                                <button type="button" id="send-email-code" class="btn btn-primary mx-3" style="width: 100%" v-on:click="sendCode">validate email ownership</button>
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
                code: ''
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
                    $('#send-email-code').prop('disabled', true);
                    axios.post(myUrl+"/api/mfa/setup/activateEmail", {code: this.code})
                    .then( response => {
                        console.log(response.data);
                        Vue.toasted.show('Email Activated!', { icon : 'check', type: 'success'});

                        this.$emit('user-updated', response.data.user);

                        this.$router.push({ name: 'mfaSetup', params: { user: response.data.user,}}).catch(err => 
                        {
                            console.log(err)
                        });
                        $('#send-email-code').prop('disabled', false);
                    })
                    .catch(error => {
                        console.log(error.response);
                    
                        let errors = error.response.data.errors;
    
                        for (let key in errors){
                            errors[key].forEach(err => 
                                Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                            );
                        }
                        $('#send-email-code').prop('disabled', false);
                    })
                }
                    
            },
            sendEmail(){
                axios.post(myUrl+"/api/mfa/setup/activationEmail")
                .then( response => {
                    console.log(response);
                    Vue.toasted.show('Activation Email sent', { icon : 'check', type: 'success'});
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