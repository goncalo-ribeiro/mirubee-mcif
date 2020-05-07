<template>
    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        multi factor authentication u2f setup
                    </div>

                    <div class="card-body" >
                        <div>
                            <p>start the process of registering your u2f key by pressing the button bellow </p>
                            
                            <div class="form-row">
                                <div class=" col-md-3">
                                </div>
                                <div class=" col-md-6">
                                    <button type="button" id="register-u2f" class="btn btn-primary" style="width: 100%" v-on:click="newregistration">register u2f key</button>
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
            }
        },
        props: {
            user: Object,
        },
        computed:{
        },
        methods:{
            newregistration() {
                if (!window.fetch || !navigator.credentials || !navigator.credentials.create) {
                    window.alert('Browser not supported.');
                    return;
                }

                axios.get(myUrl+"/api/u2f/createArgs").then( response => {
                    console.log(response)
                    return response.data;
                    // convert base64 to arraybuffer
                }).then( (json) => {
                    // error handling
                    if (json.success === false) {
                        throw new Error(json.msg);
                    }
                    // replace binary base64 data with ArrayBuffer. a other way to do this
                    // is the reviver function of JSON.parse()
                    this.recursiveBase64StrToArrayBuffer(json);
                    //console.log(json);
                    return json;
                    // create credentials
                }).then((createCredentialArgs) => {
                    console.log(createCredentialArgs);
                    return navigator.credentials.create(createCredentialArgs);
                    // convert to base64
                })
                .then( (cred) => {
                    return {
                        clientDataJSON: cred.response.clientDataJSON  ? this.arrayBufferToBase64(cred.response.clientDataJSON) : null,
                        attestationObject: cred.response.attestationObject ? this.arrayBufferToBase64(cred.response.attestationObject) : null
                    };
                    // transfer to server
                }).then(JSON.stringify).then( (AuthenticatorAttestationResponse) => {
                    //console.log(AuthenticatorAttestationResponse)

                    //return window.fetch('processCreate', {method:'POST', body: AuthenticatorAttestationResponse, cache:'no-cache'});
                                        
                    return window.fetch("api/u2f/processCreate", {
                        headers: {
                            "Content-Type": "application/json",
                            "Accept": "application/json",
                            "X-Requested-With": "XMLHttpRequest",
                            "Authorization" : axios.defaults.headers.common['Authorization']
                            // "X-CSRF-Token": $('input[name="_token"]').val()
                        },
                        method: "post",
                        credentials: "same-origin",
                        body: AuthenticatorAttestationResponse
                    });

                    // convert to JSON
                }).then( (response) => {
                    console.log(response)
                    return response.json();
                    // analyze response
                }).then( (json) => {
                    if (json.success) {
                        window.alert(json.msg || 'registration success');
                        
                        this.$emit('user-updated', json.user);
                        this.$router.push({ name: 'mfaSetup', params: { user: json.user,}}).catch(err => 
                        {
                            console.log(err)
                        });
                    } else {
                        throw new Error(json.msg);
                    }
                    // catch errors
                }).catch(function(err) {
                    console.log(err);
                    window.alert(err.message || 'unknown error occured');
                });
            },
            
            /**
             * convert RFC 1342-like base64 strings to array buffer
             * @param {mixed} obj
             * @returns {undefined}
             */
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

    window.onload = function() {
        if (location.protocol !== 'https:' && location.host !== 'localhost') {
            location.href = location.href.replace('http', 'https');
        }
    }
</script>