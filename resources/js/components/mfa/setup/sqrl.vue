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
                            <p>associate your SQRL identity with you user account</p>
                            <p>click on the image or scan the QR code below:</p>
                            
                            <div  v-if="nonce" class="form-row">
                                <div class=" col-md-3">
                                </div>
                                <div class="col-md-6">
                                    <!--
                                    <a  id="sqrl" :href="nonce.url_login_sqrl" v-on:click="sqrlLinkClick();" tabindex="-1" >
                                        -->
                                    <a  id="sqrl" v-on:click="sqrlLinkClick();" tabindex="-1" style="cursor: pointer" >
                                        <img style="margin-left: auto; margin-right: auto; width: 200px; height:200px; float: left;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/SQRL_icon_vector_outline.svg/1200px-SQRL_icon_vector_outline.svg.png" class="card-img sqrl-logo" border="0" alt="Click to authenticate SQRL identity">
                                        <img style="margin-left: auto; margin-right: auto; width: 200px; height:200px; float: right;" :src="'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' + nonce.url_login_sqrl" alt="Sqrl Qr Code">
                                    </a>
                                </div>
                                <div class="col-md-3">
                                </div>
                            </div>
                            <!--
                            <div class="m-2">
                                <iframe frameborder="0" height=500 width=820 name="sqrlFrame"></iframe>
                            </div>
                            -->
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
            this.pollForNextPage();
        },
        beforeDestroy(){
            this.window.close();
        },
        data: function(){
            return {
                nonce: null,
                window: null,
                refreshTimer: 3000, // time between each sqrl status check 
                timeout: null,
            }
        },
        props: {
            user: Object,
        },
        watch:{
            $route (to, from){
                if(this.window){
                    this.window.close();
                }
                if(this.timeout){
                    clearTimeout(this.timeout);
                }
            }
        },
        computed:{
            url: function (){
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

            sqrlLinkClick(e) {
                console.log('sqrlLinkClick')
                //this.window = window.open(myUrl+'/sqrl/login?nut='+this.nonce.nonce, 'sqrlFrame', 'location=yes, height=500, width=820, ,left=30,top=30, toolbar=yes, scrollbars=yes, status=yes, menubar=yes');
                this.window = window.open(myUrl+'/sqrl/login?nut='+this.nonce.nonce, '_blank', 'location=yes, height=500, width=820, ,left=30,top=30, toolbar=yes, scrollbars=yes, status=yes, menubar=yes');
            },

            pollForNextPage() {
                console.log('pollForNextPage');
                if (document.hidden || this.nonce == null) {					// before probing for any page change, we check to 
                    this.timeout = setTimeout(this.pollForNextPage, this.refreshTimer);	// see whether the page is visible. If the user is 
                    return;								// not viewing the page, check again in 5 seconds.
                }

                axios.get(this.url, {headers: {'Access-Control-Allow-Origin': '*'}})
                .then( response => {
                    console.log(response);
                    if(response.data.isReady == true) {
                        console.log('response ready', response.data.nextPage);
                        if(this.window){
                            this.window.close();
                        }
                        Vue.toasted.show('Sqrl identity verified...', { icon : 'hourglass_empty', type: 'success'});

                        axios.post(myUrl+"/api/mfa/sqrl", {nut: this.nonce.nonce})
                        .then( response => {
                            console.log(response.data);
                            Vue.toasted.show('Sqrl authentication activated!', { icon : 'check', type: 'success'});

                            this.$emit('user-updated', response.data.user);

                            this.$router.push({ name: 'mfaSetup', params: { user: response.data.user,}}).catch(err => 
                            {
                                console.log(err)
                            });
                        })
                        .catch(error => {
                            console.log(error.response);
                        /*
                            let errors = error.response.data.errors;
        
                            for (let key in errors){
                                errors[key].forEach(err => 
                                    Vue.toasted.show(err, { icon : 'cancel', type: 'error'})
                                );
                            }*/
                        });

                        //document.location.href = response.data.nextPage;
                    } else {
                        console.log('response not ready');
                        
                        if(response.data.msg === "Time out, reload nonce!" || response.data.msg === "IP Doesnt Match!" || response.data.msg === "SQRL is disable for this user!") {
                            this.window.close();
                    
                            Vue.toasted.show('Nut expired! Please reload this component and try again, if you want to Authenticate by SQRL', { icon : 'cancel', type: 'error'})

                        } else {
                            this.timeout = setTimeout(this.pollForNextPage, this.refreshTimer); // next check in this.refreshTimer milliseconds 
                        }
                    }
                })
                .catch(error => {
                    this.timeout = setTimeout(this.pollForNextPage, this.refreshTimer); // next check in this.refreshTimer milliseconds 
                })
            },
        }
    }
</script>