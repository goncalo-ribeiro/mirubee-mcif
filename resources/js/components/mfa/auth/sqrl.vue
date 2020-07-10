<template>

<div class="container" id="mfa-form">
            <div class="row my-5">
                <div class="col-md-8 m-auto animatebottom">
                    <div class="jumbotron">
                        <h1 class="display-4">MFA - SQRL</h1>
                        <p class="lead">use your sqrl identity to authenticate yourself</p>
                        <hr class="my-4">

                        <p>please scan the qr code below to authenticate yourself:</p>

                            
                        <div  v-if="nonce" class="form-row">
                            <div class=" col-md-1">
                            </div>
                            <div class="col-md-10">
                                <!--
                                <a  id="sqrl" :href="nonce.url_login_sqrl" v-on:click="sqrlLinkClick();" tabindex="-1" >
                                    -->
                                <a  id="sqrl" v-on:click="sqrlLinkClick();" tabindex="-1" style="cursor: pointer" >
                                    <img style="margin-left: auto; margin-right: auto; width: 200px; height:200px; float: left;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/SQRL_icon_vector_outline.svg/1200px-SQRL_icon_vector_outline.svg.png" class="card-img sqrl-logo" border="0" alt="Click to authenticate SQRL identity">
                                    <img style="margin-left: auto; margin-right: auto; width: 200px; height:200px; float: right;" :src="'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' + nonce.url_login_sqrl" alt=" loading Sqrl Qr Code, please wait">
                                </a>
                            </div>
                            <div class=" col-md-1">
                            </div>
                        </div>
                        <div class="form-row mt-2 ml-2">
                            <input id="remember" class="form-check-input ml-0"  v-model="remember" type="checkbox">
                            <label style="margin-left: 1.25rem" for="remember">remember this device</label> 
                        </div>
                        <div style="height:5vh; width: 100%">
                        </div>

                        

                        <div class="form-row mt-3">
                            <div class=" col-md-4">
                                <button type="button" class="btn btn-danger" style="width: 100%" v-on:click="goBack">back</button>
                            </div>
                            <div class=" col-md-8">                                
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
        name: "mfa-sqrl-auth",
        mounted() {
            console.log('mfa sqrl auth component mounted.');
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
                remember: false,
                refreshTimer: 3000, //check sqrl status every 3 seconds
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
            goBack(){
                this.$router.push({ name: 'mfaAuthentication',  params: { user: this.user,}})
            },

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
                newWindow.focus();
            },

            pollForNextPage() {
                console.log('pollForNextPage', this.refreshTimer );
                if (document.hidden || this.nonce == null) {					// before probing for any page change, we check to 
                    this.timeout = setTimeout(this.pollForNextPage, this.refreshTimer);	// see whether the page is visible. If the user is 
                    return;								// not viewing the page, check again in 5 seconds.
                }

                axios.get(this.url, {headers: {'Access-Control-Allow-Origin': '*'}})
                .then( response => {
                    console.log(response);
                    if(response.data.isReady == true) {
                        console.log('response ready', response.data.nextPage);
                        this.window.close();
                        Vue.toasted.show('Sqrl identity verified...', { icon : 'hourglass_empty', type: 'success'});

                        axios.post(myUrl+"/api/mfa/sqrl", {nut: this.nonce.nonce})
                        .then( response => {
                            console.log(response.data);
                            Vue.toasted.show('successfully authenticated', { icon : 'check', type: 'success'});

                            this.$router.push({ name: 'siteRetriever'}).catch(err => 
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