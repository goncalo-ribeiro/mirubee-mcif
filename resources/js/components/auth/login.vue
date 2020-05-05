<template>
    <div class="container" id="login-form">
        <div class="row my-5">
            <div class="col-md-8 m-auto animatebottom">
                <div class="jumbotron">
                    <h1 class="display-4">Mirubee MCIF</h1>
                    <p class="lead">A secure Energy Consumption Monitoring Platform</p>
                    <hr class="my-4">
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input autofocus type="email" v-model="email" class="form-control" id="emailInput" placeholder="email" v-on:keyup.enter="loginClick">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" v-model="password" class="form-control" id="passwordInput" placeholder="password" v-on:keyup.enter="loginClick">
                        </div>
                        <div class="input-group mb-3">
                            <button type="button" class="btn btn-primary" @click.prevent="loginClick">login</button>
                            <router-link class="my-auto ml-auto" to="/register">haven't signed up yet? click here</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "login",
        data: function(){
            return {
                email: undefined,
                password: undefined,
            }
        },
        activated(){
            this.clearInputFields();
        },
        methods:{
            loginClick() {
                if(this.validateFields() === false){
                    alert("Email and password cannot be empty");
                    return;
                }
                let credentials = {
                    email: this.email,
                    password: this.password
                };
                this.$auth.login(credentials)
                .then(success => {
                    console.log("Success Login");
                    Vue.toasted.show('Successfully logged in', { icon : 'check', type: 'success'});
                    
                    // mfa
                    let mfa = this.$auth.user.mfaMethods;
                    console.log(mfa.google, mfa.u2f, mfa.email, mfa.sqrl, mfa.google || mfa.u2f || mfa.email || mfa.sqrl)
                    if(mfa.google || mfa.u2f || mfa.email || mfa.sqrl){
                        console.log('mfa activated')
                        if(mfa.authenticated){
                            console.log('mfa authenticated')
                            this.$router.push({ name: 'siteRetriever'})
                        }else{
                            console.log('mfa unauthenticated')
                            this.$router.push({ name: 'mfaAuthentication',  params: { user: this.$auth.user,}})
                        }
                    }else{
                        console.log('mfa not activated')
                        this.$router.push({ name: 'siteRetriever'})
                    }

                })
                .catch( error => {
                    console.log("Auth login catch: ", error);
                    Vue.toasted.show(error, { icon : 'cancel', type: 'error'});
                    /*
                    if(error.response.status === 403){
                        this.resendEmailConfirmationButton = true;
                    }
                    this.loginFailedMsg = error.response.data.msg;
                    this.$emit('login-fail');*/
                });
            },
            validateFields(){
                let isValid = true;
                if(this.email === undefined || this.email.trim() === ""){
                    isValid = false;
                }
                if(this.password === undefined || this.password.trim() === ""){
                    isValid = false;
                }
                return isValid;
            },
            clearInputFields(){
                this.email = '';
                this.password = '';
            },
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>