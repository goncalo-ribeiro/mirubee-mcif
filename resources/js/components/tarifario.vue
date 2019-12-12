<template>

    <div class="mt-5 container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">tarifário</div>

                    <div class="card-body" >
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="potencia">potência contratada</label>
                                <input autofocus type="text" class="form-control" id="potencia" placeholder="kVa">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">preço da potência por dia</label>
                                <input type="text" class="form-control" id="preço" placeholder="€">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="imposto">imposto</label>
                                <input type="number" class="form-control" id="imposto" placeholder="%">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="preço">tipo de tarifário</label>
                                <select class="form-control" v-model="tarifario">
                                    <option disabled value="0">selecione uma das opções possíveis</option>
                                    <option value="1">simples</option>
                                    <option value="2">bi-horário</option>
                                    <option value="3">tri-horário</option>
                                </select>
                            </div>
                        </div>
                        <hr>
                        <span v-if="tarifario == 0">selecione um tipo de tarifário em cima para definir o preço deste para cada altura do dia</span>
                        <div class="form-row">
                            <div v-if="tarifario == 1" class="form-group col-md-6">
                                <label for="preco-simples">preço da eletricidade</label>
                                <input autofocus type="number" class="form-control" id="preco-simples" placeholder="€/kWh">
                            </div>
                            <div v-if="tarifario > 1" class="form-group col-md-6">
                                <label for="vazio">preço da potência da eletricidade nas horas de vazio</label>
                                <input type="number" class="form-control" id="vazio" placeholder="€/kWh">
                            </div>
                            <div v-if="tarifario == 2" class="form-group col-md-6">
                                <label for="fora-vazio">preço da potência da eletricidade nas horas fora de vazio</label>
                                <input type="number" class="form-control" id="fora-vazio" placeholder="€/kWh">
                            </div>
                            <div v-if="tarifario == 3" class="form-group col-md-6">
                                <label for="ponta">preço da potência da eletricidade nas horas de ponta</label>
                                <input type="number" class="form-control" id="ponta" placeholder="€/kWh">
                            </div>
                        </div>   
                        <div class="form-row">                     
                            <div v-if="tarifario == 3" class="form-group col-md-6">
                                <label for="cheias">preço da potência da eletricidade nas horas cheias</label>
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