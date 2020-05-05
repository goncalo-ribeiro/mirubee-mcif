<template>
    <div class="container" id="mfa-form">
            <div class="row my-5">
                <div class="col-md-8 m-auto animatebottom">
                    <div class="jumbotron">
                        <h1 class="display-4">Multi Factor Authentication</h1>
                        <p class="lead">please choose your prefered method of multi factor authentication below</p>
                        <hr class="my-4">
                        
                        <div class="list-group">
                                
                            <a v-for="method in mfaMethods" :key="method.formName" style="cursor:pointer; border-color: #839496" class="list-group-item clearfix" @click="click(method.resourceName)">
                                {{method.formName}}
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
        name: "mfa-auth",
        mounted() {
            console.log('mfa auth component mounted.');

            Object.keys(this.user.mfaMethods).forEach( (key) => {
                switch (key) {
                    case 'email':
                        if(this.user.mfaMethods[key] === true) this.mfaMethods.push({formName: 'email code', resourceName: 'email', idName: 'email-setup-auth'})
                        break;
                    case 'google':
                        if(this.user.mfaMethods[key] === true) this.mfaMethods.push({formName: 'google authenticator', resourceName: 'google', idName: 'google-setup-auth'},)
                        break;
                    case 'u2f':
                        if(this.user.mfaMethods[key] === true) this.mfaMethods.push({formName: 'u2f authenticator', resourceName: 'u2f', idName: 'u2f-setup-auth'},)
                        break;
                    case 'sqrl':
                        if(this.user.mfaMethods[key] === true) this.mfaMethods.push({formName: 'sqrl authenticator', resourceName: 'sqrl', idName: 'sqrl-setup-auth'})
                        break;
                }
            });
        },
        activated(){
            console.log('mfa auth component activated.');
        },
        data: function(){
            return {
                mfaMethods: [],
            }
        },
        props: {
            user: Object,
        },
        computed:{
        },
        methods:{
            click(methodName){
                switch (methodName) {
                    case 'email':
                        this.$router.push({ name: 'mfaAuthenticationEmail', params: { user: this.user,}}).catch(err => 
                        {
                            console.log(err)
                        });
                        break;
                    case 'google':
                        this.$router.push({ name: 'mfaAuthenticationGoogle', params: { user: this.user,}}).catch(err => 
                        {
                            console.log(err)
                        });                        
                        break;
                    case 'u2f':
                        
                        break;
                    case 'sqrl':
                        
                        break;
                }
            }
        }
    }
</script>