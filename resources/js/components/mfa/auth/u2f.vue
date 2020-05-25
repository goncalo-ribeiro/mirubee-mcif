<template>
    <div class="container" id="mfa-form">
            <div class="row my-5">
                <div class="col-md-8 m-auto animatebottom">
                    <div class="jumbotron">
                        <h1 class="display-4">MFA - U2F</h1>
                        <p class="lead">use your u2f key to authenticate yourself</p>
                        <hr class="my-4">

                        <p>please connect your u2f key to your device and press the button bellow to authenticate yourself</p>

                        <div class="form-row">
                            <div class=" col-md-3">
                            </div>
                            <div class=" col-md-6">
                                
                                    <button type="button" class="btn btn-primary" style="width: 100%" v-on:click="checkregistration">authenticate</button>  
                                    <br>
                                    <input id="remember" class="form-check-input ml-0"  v-model="remember" type="checkbox">
                                    <label style="margin-left: 1.25rem" for="remember">remember this device</label>                     
                            </div>
                            <div class=" col-md-3">
                            </div>
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
export default {
        name: "mfa-u2f-setup",
        mounted() {
            console.log('mfa u2f setup component mounted.');
        },
        activated(){
            console.log('mfa u2f setup component activated.');
        },
        data: function(){
            return {
                remember: false,
            }
        },
        props: {
            user: Object,
        },
        computed:{
        },
        methods:{
            goBack(){
                this.$router.push({ name: 'mfaAuthentication',  params: { user: this.user,}})
            },

            checkregistration() {
                if (!window.fetch || !navigator.credentials || !navigator.credentials.create) {
                    window.alert('Browser not supported.');
                    return;
                }
                // get default args
                axios.get(myUrl+"/api/u2f/getArgs").then( response => { 
                    //console.log(response);
                    return response.data;
                    // convert base64 to arraybuffer
                }).then( json => {
                    // error handling
                    if (json.success === false) {
                        throw new Error(json.msg);
                    }
                    // replace binary base64 data with ArrayBuffer. a other way to do this
                    // is the reviver function of JSON.parse()
                    this.recursiveBase64StrToArrayBuffer(json);
                    return json;
                    // create credentials
                }).then( getCredentialArgs => {
                    return navigator.credentials.get(getCredentialArgs);
                    // convert to base64
                }).then( cred => {
                    return {
                        id: cred.rawId ? this.arrayBufferToBase64(cred.rawId) : null,
                        clientDataJSON: cred.response.clientDataJSON  ? this.arrayBufferToBase64(cred.response.clientDataJSON) : null,
                        authenticatorData: cred.response.authenticatorData ? this.arrayBufferToBase64(cred.response.authenticatorData) : null,
                        signature : cred.response.signature ? this.arrayBufferToBase64(cred.response.signature) : null
                    };
                    // transfer to server
                }).then(JSON.stringify).then( AuthenticatorAttestationResponse => {
                    let auxObj = JSON.parse(AuthenticatorAttestationResponse);
                    auxObj['remember'] = this.remember;
                    AuthenticatorAttestationResponse = JSON.stringify(auxObj);
                    return window.fetch("api/u2f/processGet", {
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "Authorization" : axios.defaults.headers.common['Authorization']
                            //"X-CSRF-Token": $('input[name="_token"]').val()
                        },
                        method: "post",
                        credentials: "same-origin",
                        body: AuthenticatorAttestationResponse
                    });
                    // convert to json
                }).then( response => {
                    return response.json();
                }).then( json => {
                    if (json.success) {
                        window.alert(json.msg || 'login success');

                        this.$router.push({ name: 'siteRetriever'}).catch(err => 
                        {
                            console.log(err)
                        });

                    } else {
                        throw new Error(json.msg);
                    }
                }).catch(function(err) {
                    console.log(err)
                    window.alert(err.message || 'unknown error occured');
                });
            },

            recursiveBase64StrToArrayBuffer(obj) {
                let prefix = '?BINARY?B?';
                let suffix = '?=';
                if (typeof obj === 'object') {
                    for (let key in obj) {
                        if (typeof obj[key] === 'string') {
                            let str = obj[key];
                            if (str.substring(0, prefix.length) === prefix && str.substring(str.length - suffix.length) === suffix) {
                                str = str.substring(prefix.length, str.length - suffix.length);
                                let binary_string = window.atob(str);
                                let len = binary_string.length;
                                let bytes = new Uint8Array(len);
                                for (var i = 0; i < len; i++)        {
                                    bytes[i] = binary_string.charCodeAt(i);
                                }
                                obj[key] = bytes.buffer;
                            }
                        } else {
                            this.recursiveBase64StrToArrayBuffer(obj[key]);
                        }
                    }
                }
            },
            
            /**
             * Convert a ArrayBuffer to Base64
             * @param {ArrayBuffer} buffer
             * @returns {String}
             */
            arrayBufferToBase64(buffer) {
                var binary = '';
                var bytes = new Uint8Array(buffer);
                var len = bytes.byteLength;
                for (var i = 0; i < len; i++) {
                    binary += String.fromCharCode( bytes[ i ] );
                }
                return window.btoa(binary);
            },
        }
    }
</script>