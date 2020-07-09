<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        multi factor authentication SQRL setup
                    </div>

                    <div class="card-body" >
                        <div>
                            <p>associate your SQRL identity with you user account bellow</p>
                            
                            <div  v-if="nonce" class="form-row">
                                <div class=" col-md-3">
                                </div>
                                <div class=" col-md-6">
                                    <a class="mx-auto" id="sqrl" :href="nonce.url_login_sqrl" tabindex="-1">
                                    <!--
                                    <a class="mx-auto" id="sqrl" :href="nonce.url_login_sqrl" v-on:click="" tabindex="-1">
                                        -->
                                        <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/SQRL_icon_vector_outline.svg/1200px-SQRL_icon_vector_outline.svg.png" style="margin-left:30px;margin-top:30px" class="card-img sqrl-logo" border="0" alt=" SQRL Code - Click to authenticate your SQRL identity ">
                                        new
                                        <!--
                                        <p style="margin-left:30px;margin-top:20px"> {!! QrCode::size(100)->generate($url_login_sqrl); !!} </p>
                                        -->
                                    </a>
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>  
    </div>
</template>

<script>
//var gifProbe = new Image();
export default {
        name: "mfa-sqrl-setup",
        mounted() {
            console.log('mfa sqrl setup component mounted.');
        },
        activated(){
            this.getNonce();
        },
        data: function(){
            return {
                nonce: null,

            }
        },
        props: {
            user: Object,
        },
        computed:{
            checkStateUrl: function (){
                return this.nonce ? this.nonce.check_state_on : null;
            }
        },
        methods:{
            getNonce(){
                axios.get(myUrl+"/api/mfa/setup/sqrlNonce")
                .then( response => {
                    console.log(response.data);
                    this.nonce = response.data.nonce;
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

            
        }
    }
</script>