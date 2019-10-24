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
                            <input required autofocus type="email" v-model="email" class="form-control" id="emailInput" placeholder="email">
                        </div>
                        <div class="input-group mb-3">
                            <input required type="password" v-model="password" class="form-control" id="passwordInput" placeholder="password">
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
                    Vue.toasted.show('Successfully logged in', { icon : 'check'});
                })
                .catch( error => {
                    console.log("Auth login catch: ", error);
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
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>