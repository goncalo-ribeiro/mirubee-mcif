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
                                <div class="col-md-6">
                                    <a  id="sqrl" :href="nonce.url_login_sqrl" v-on:click="sqrlLinkClick();" tabindex="-1" >
                                        <img style="margin-left: auto; margin-right: auto; width: 150px; height:150px; float: left;" src="https://upload.wikimedia.org/wikipedia/commons/thumb/1/17/SQRL_icon_vector_outline.svg/1200px-SQRL_icon_vector_outline.svg.png" class="card-img sqrl-logo" border="0" alt=" SQRL Code - Click to authenticate your SQRL identity ">
                                        <img style="margin-left: auto; margin-right: auto; width: 150px; height:150px; float: right;" :src="'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' + nonce.url_login_sqrl">
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
            /*
            this.gifProbe.onload = function() {          // define our load-success function
                this.sqrlScheme = false;			// prevent retriggering of the SQRL QR code.
                console.log('this.gifProbe.onload', this.localhostRoot + this.encodedSqrlUrl)
                //document.location.href = this.localhostRoot + this.encodedSqrlUrl;
            };
            this.gifProbe.onerror = function() { // define our load-failure function
                setTimeout( function(){ this.gifProbe.src = this.localhostRoot + Date.now() + '.gif';	}, 250 );
            }*/
            this.pollForNextPage();
        },
        data: function(){
            return {
                nonce: null,
                /*encodedSqrlUrl: false, sqrlScheme: true,*/
                /*gifProbe: new Image(),			// create an instance of a memory-based probe image
                localhostRoot: 'http://localhost:25519/',	// the SQRL client listener*/
            }
        },
        props: {
            user: Object,
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
                this.encodedSqrlUrl = this.nonce.encoded_url_login_sqrl
                // if we have an encoded URL to jump to, initiate our GIF probing before jumping
                if ( this.encodedSqrlUrl ) { this.gifProbe.onerror(); };	// trigger the initial image probe query
            },

            pollForNextPage() {
                console.log('pollForNextPage');
                if (document.hidden || this.nonce == null) {					// before probing for any page change, we check to 
                    setTimeout(this.pollForNextPage, 5000);	// see whether the page is visible. If the user is 
                    return;								// not viewing the page, check again in 5 seconds.
                }

                axios.get(this.url)
                .then( response => {
                    console.log(response);
                    if(response.data.isReady == true) {
                        console.log('response ready', response.data.nextPage);
                        Vue.toasted.show('Associating your SQRL identity to your user account...', { icon : 'check', type: 'success'});
                        //document.location.href = response.data.nextPage;
                    } else {
                        console.log('response not ready');
                        if(response.data.msg === "Time out, reload nonce!" || response.data.msg === "IP Doesnt Match!" || response.data.msg === "SQRL is disable for this user!") {
                    
                            Vue.toasted.show('Nut expired! Please reload this component and try again, if you want to Authenticate by SQRL', { icon : 'cancel', type: 'error'})

                        } else {
                            setTimeout(this.pollForNextPage, 5000); // next check in 5000 milliseconds 
                        }
                    }
                })
                .catch(error => {
                    setTimeout(this.pollForNextPage, 5000); // next check in 5000 milliseconds 
                })
            },
        }
    }
</script>