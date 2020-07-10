<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        multi factor authentication setup
                    </div>

                    <div class="card-body" >
                        <div class="list-group">
                            <a v-for="method in activationMethods" :key="method.formName" class="list-group-item clearfix">
                                {{method.formName}}
                                <span style="float: right!important;">
                                    <div class="form-check">
                                        <input :id="method.idName" class="form-check-input"  @click.prevent="clickCheckbox($event, method)" type="checkbox">
                                    </div>
                                </span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</template>

<script>
export default {
        name: "mfa-setup",
        mounted() {
            console.log('mfa setup component mounted.');
            
        },
        activated(){
            console.log('mfa setup component activated.');
            this.activationMethods.forEach(method => {
                $('#' + method.idName).prop('checked', this.myUser.mfaMethods[method.resourceName]);
            });
        },
        data: function(){
            return {

                activationMethods: [
                    {formName: 'email code', resourceName: 'email', idName: 'email-setup-check'},
                    {formName: 'google authenticator', resourceName: 'google', idName: 'google-setup-check'},
                    {formName: 'u2f authenticator', resourceName: 'u2f', idName: 'u2f-setup-check'},
                    {formName: 'sqrl authenticator', resourceName: 'sqrl', idName: 'sqrl-setup-check'}
                ],

                myUser: this.user,
            }
        },
        props: {
            user: Object,
        },
        computed:{
        },
        watch: {
            user: function (val) {
                console.log('watch user', val)
                this.myUser = val
            }
        },
        methods:{
            clickCheckbox(evt, method){
                switch (method.resourceName) {
                    case 'email':
                        this.clickCheckboxEmail(evt, method);
                        break;
                    case 'google':
                        this.clickCheckboxGoogle(evt, method);
                        break;
                    case 'u2f':
                        this.clickCheckboxU2f(evt, method);
                        break;
                    case 'sqrl':
                        this.clickCheckboxSqrl(evt, method);
                        break;
                }
            },
            
            clickCheckboxEmail(evt, method) {
                console.log('clickCheckboxEmail(evt, method)');
                if(this.myUser.mfaMethods.email){
                    this.disableEmailMFA();
                }else{
                    if(this.myUser.mfaMethods.email_activated){
                        this.enableEmailMFA();
                    }
                    else{
                        this.$router.push({ name: 'mfaSetupEmail', params: { user: this.myUser,}}).catch(err => 
                        {
                            console.log(err)
                        });
                    }
                }
            },
            
            
            clickCheckboxGoogle(evt, method) {
                console.log('clickCheckboxGoogle(evt, method)');
                if(this.myUser.mfaMethods.google){
                    this.disableGoogleMFA();
                }else{
                    this.$router.push({ name: 'mfaSetupGoogle', params: { user: this.myUser,}}).catch(err => 
                    {
                        console.log(err)
                    });
                }
            },

            clickCheckboxU2f(evt, method) {
                console.log('clickCheckboxU2f(evt, method)');
                if(this.myUser.mfaMethods.u2f){
                    this.disableU2FMFA();
                }else{
                    this.$router.push({ name: 'mfaSetupU2F', params: { user: this.myUser,}}).catch(err => 
                    {
                        console.log(err)
                    });
                }

            },
            clickCheckboxSqrl(evt, method) {
                console.log('clickCheckboxSQRL(evt, method)');
                if(this.myUser.mfaMethods.sqrl){
                    this.disableSQRLMFA();
                }else{
                    this.$router.push({ name: 'mfaSetupSQRL', params: { user: this.myUser,}}).catch(err => 
                    {
                        console.log(err)
                    });
                }

            },

            disableSQRLMFA(){
                console.log('disable SQRL')
                axios.delete(myUrl+"/api/mfa/setup/sqrl")
                .then( response => {
                    console.log(response.data);
                    Vue.toasted.show(response.data.message, { icon : 'check', type: 'success'});

                    this.$emit('user-updated', response.data.user);
                    this.myUser = response.data.user;
                    $('#google-setup-check').prop('checked', false);
                     
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

            enableEmailMFA(){
                axios.post(myUrl+"/api/mfa/setup/email")
                .then( response => {
                    console.log(response.data);
                    Vue.toasted.show(response.data.message, { icon : 'check', type: 'success'});

                    this.$emit('user-updated', response.data.user);
                    this.myUser = response.data.user;
                    $('#email-setup-check').prop('checked', true);     
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

            disableEmailMFA(){
                axios.delete(myUrl+"/api/mfa/setup/email")
                .then( response => {
                    console.log(response.data);
                    Vue.toasted.show(response.data.message, { icon : 'check', type: 'success'});

                    this.$emit('user-updated', response.data.user);
                    this.myUser = response.data.user;
                    $('#email-setup-check').prop('checked', false);
                     
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

            disableGoogleMFA(){
                console.log('disable Google')
                axios.delete(myUrl+"/api/mfa/setup/google")
                .then( response => {
                    console.log(response.data);
                    Vue.toasted.show(response.data.message, { icon : 'check', type: 'success'});

                    this.$emit('user-updated', response.data.user);
                    this.myUser = response.data.user;
                    $('#google-setup-check').prop('checked', false);
                     
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

            disableU2FMFA(){
                console.log('disable U2F')
                axios.delete(myUrl+"/api/mfa/setup/u2f")
                .then( response => {
                    console.log(response.data);
                    Vue.toasted.show(response.data.message, { icon : 'check', type: 'success'});

                    this.$emit('user-updated', response.data.user);
                    this.myUser = response.data.user;
                    $('#u2f-setup-check').prop('checked', false);
                     
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

        },
    }
</script>