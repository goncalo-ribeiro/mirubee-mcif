<template>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-8" style="text-align: center;vertical-align: middle;">
                
                <h4>
                    Retrieving your sites...
                </h4>
                <h1>
                    ⌛
                </h1>
                <div class="progress">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
        name: "siteRetriever",
        mounted() {
            console.log('retriever Component mounted.')

            let timer = true;

            axios.get(myUrl+"/api/sites")//, { headers: { Accept: 'application/json'}})
            .then(response =>{
                console.log(response);

                if (timer){
                    let _this = this
                    setTimeout( function() {
                        //console.log('done');
                        _this.$emit('sites-retrieved', response.data);
                    }, 500);
                }else{
                    _this.$emit('sites-retrieved', response.data);
                }
                

                $(".progress-bar").animate({
                    width: "80%"
                }, 300);
            })
            .catch(error =>{
                console.log(error);
                Vue.toasted.show('error ' + error.response.status + ": " + error.response.data.message + " (user's site retrieval)", { icon : 'cancel', type: 'error'});
            })        
        }
    }
</script>
