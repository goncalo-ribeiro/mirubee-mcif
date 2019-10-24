<template>
    <div class="container" id="login-form">
        <div class="row my-5">
            <div class="col-md-8 m-auto animatebottom">
                <div class="jumbotron">
                    <h1 class="display-4">Register</h1>
                    <p class="lead">Please enter your data on these fields to complete the registration process</p>
                    <hr class="my-4">
                    <div class="form-group">
                        <div class="input-group mb-3">
                            <input required autofocus type="email" v-model="email" class="form-control" id="emailInput" placeholder="email">
                        </div>
                        <div class="input-group mb-3">
                            <input required type="text" v-model="name" class="form-control" id="nameInput" placeholder="name">
                        </div>
                        <div class="input-group mb-3">
                            <input required type="password" v-model="password" class="form-control" id="passwordInput" placeholder="password">
                        </div>
                        <div class="input-group mb-3">
                            <input required type="password" v-model="passwordConfirmation" class="form-control" id="passwordConfirmationInput" placeholder="password confirmation">
                        </div>
                        <div class="input-group mb-3">
                            <button type="button" class="btn btn-primary" @click.prevent="registerClick">register</button>
                            <router-link class="my-auto ml-auto" to="/login">already registered? click here</router-link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "register",
        data: function(){
            return {
                email: undefined,
                name: undefined,
                password: undefined,
                passwordConfirmation: undefined,
                registerFailedMsg: undefined,
            }
        },
        methods:{
            registerClick() {
                this.registerFailedMsg = this.validateFields();

                if(this.registerFailedMsg){
                    console.log(this.registerFailedMsg);
                    Vue.toasted.show(this.registerFailedMsg, { icon : 'clear', type: 'error'});
                    return;
                }
                
                Event.$emit('email-changed', this.email);
                axios.post('api/register',
                    {
                        name : this.name,
                        email : this.email,
                        password : this.password,
                    })
                    .then(response => {
                        console.log("Server Accepted new User: ", response.data);
                        Event.$emit('register-success', this.email);
                        this.clearInputFields();
                        //this.$emit('register-success');
                        //this.$events.fire('toast', 'Your account was succesfully created, check your email to activate it', 5, 'success');
                    })
                    .catch(error => {
                        console.log("Server Denied new User: ");
                        console.log(error.response.data);
                        this.registerFailedMsg = undefined;
                        this.$emit('register-fail');
                        //this.$events.fire('toast', message, 5, 'danger');
                    });
            },
            validateFields(){
                if(this.email === undefined || this.email.trim() === ""){
                    return "the email field can't be left empty";
                }
                if(this.name === undefined || this.name.trim() === ""){
                    return "the name field can't be left empty";
                }
                if(this.password === undefined || this.password.trim() === ""){
                    return "the password field can't be left empty";
                }
                if(this.password != this.passwordConfirmation){
                    return "the passwords entered are not equal";
                }
                return undefined;
            },

            clearInputFields(){
                this.email = undefined;
                this.name = undefined;
                this.password = undefined;
                this.passwordConfirmation = undefined;
                this.registerFailedMsg = undefined;
            },
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>