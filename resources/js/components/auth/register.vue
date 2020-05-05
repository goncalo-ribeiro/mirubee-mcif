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
                            <input autofocus v-model="email" class="form-control" id="emailInput" placeholder="email" v-on:keyup.enter="registerClick">
                        </div>
                        <div class="input-group mb-3">
                            <input type="text" v-model="name" class="form-control" id="nameInput" placeholder="name" v-on:keyup.enter="registerClick">
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" v-model="password" class="form-control" id="passwordInput" placeholder="password" v-on:keyup.enter="registerClick"> 
                        </div>
                        <div class="input-group mb-3">
                            <input type="password" v-model="passwordConfirmation" class="form-control" id="passwordConfirmationInput" placeholder="password confirmation" v-on:keyup.enter="registerClick">
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
        activated(){
            this.clearInputFields();
        },
        methods:{
            registerClick() {
                console.log('register click')
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
                        //Event.$emit('register-success', this.email);
                        this.clearInputFields();
                        //this.$emit('register-success');
                        Vue.toasted.show('Successfully Registered', { icon : 'check', type: 'success'});
                        this.$router.push('/login')
                    })
                    .catch(error => {
                        console.log("Server Denied new User: ");
                        console.log(error)
                        console.log(error.response.data);
                        this.$emit('register-fail');
                        Vue.toasted.show(error.response.data, { icon : 'cancel', type: 'error'});
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
                this.email = '';
                this.name = '';
                this.password = '';
                this.passwordConfirmation = '';
                this.registerFailedMsg = '';
            },
        },
        mounted() {
            console.log('Component mounted.')
        }
    }
</script>