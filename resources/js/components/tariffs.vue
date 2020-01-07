<template>

    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">tariffs</div>

                    <div class="card-body" >
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="potencia">contracted power</label>
                                <input autofocus type="text" class="form-control" id="potencia" placeholder="kVa">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">daily power price</label>
                                <input type="text" class="form-control" id="preço" placeholder="€">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="imposto">tax</label>
                                <input type="number" class="form-control" id="imposto" placeholder="%">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">type of tariff</label>
                                <select class="form-control" v-model="tarifario">
                                    <option disabled value="0">choose one of the following options</option>
                                    <option value="1">simple</option>
                                    <option value="2">bi-hourly</option>
                                    <option value="3">tri-hourly</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <span v-if="tarifario == 0">choose the type of tariff above to set the consumption price for each time of the day</span>
                        <div class="form-row">
                            <div v-if="tarifario == 1" class="form-group col-md-6">
                                <label for="preco-simples">consumption price</label>
                                <input autofocus type="number" class="form-control" id="preco-simples" placeholder="€/kWh">
                            </div>
                            <div v-if="tarifario > 1" class="form-group col-md-6">
                                <label for="vazio">consumption price during off-peak hours</label>
                                <input type="number" class="form-control" id="vazio" placeholder="€/kWh">
                            </div>
                            <div v-if="tarifario == 2" class="form-group col-md-6">
                                <label for="fora-vazio">consumption price outside off-peak hours</label>
                                <input type="number" class="form-control" id="fora-vazio" placeholder="€/kWh">
                            </div>
                            <div v-if="tarifario == 3" class="form-group col-md-6">
                                <label for="ponta">consumption price during peak hours</label>
                                <input type="number" class="form-control" id="ponta" placeholder="€/kWh">
                            </div>
                        </div>   
                        <div class="form-row">                     
                            <div v-if="tarifario == 3" class="form-group col-md-6">
                                <label for="cheias">consumption price during full time</label>
                                <input type="number" class="form-control" id="cheias" placeholder="€/kWh">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--
    -->
</template>

<script>
export default {
        name: "tarifario",
        mounted() {
            console.log('Component mounted.')
            
        },
        data: function(){
            return {
                tarifario: 0,
            }
        },
        methods:{
 
        }
    }
</script>