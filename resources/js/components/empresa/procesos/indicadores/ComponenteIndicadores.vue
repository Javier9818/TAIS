<template>
    <section>
        <div class="row justify-content-end mr-2" @click="nuevoIndicador"><button class="btn btn-sm btn-danger">Nuevo</button></div>
        <div class="row">
            <div class="col-md-6">
                <b-card>
                   <b>Indicador</b>
                   <p>Indicador 01</p>

                   <b>Objetivo</b>
                   <p>Reducir costos</p>

                   <b>Metas</b>
                   <p>Reducir costos</p>

                   <b>Responsable</b>
                   <p>Reducir costos</p>

                   <b>Iniciativas</b>
                   <p>Reducir costos</p>

                   <b>Semáforo</b>
                   <p><i class="fas fa-circle indicador__red"></i> Menos de 5.0</p>
                   <p><i class="fas fa-circle indicador__yellow"></i> Entre 5.0 y 7.0</p>
                   <p><i class="fas fa-circle indicador__green"></i> Más de 7.0</p>

                   <b>Fórmula</b>
                   <p>x - número de pieles</p>
                   <p>y - número de pieles</p>
                   <p>f = x / y</p>
                   <b-row class="justify-content-end">
                       <button class="btn btn-sm btn-warning">Editar</button>
                       <button class="btn btn-sm btn-danger ml-2">Eliminar</button>
                   </b-row>
                </b-card>
            </div>
        </div>

        <b-modal id="formIndicador" :title="info.title" size="lg" scrollable hide-footer>
            <form-indicador @click="$bvModal.hide('formIndicador')" @store="storeSub" @update="updateSub" :dataForm="info"></form-indicador>
        </b-modal>
    </section>
</template>

<script>
  export default {
    data() {
      return {
        info:{
            title:'',
            content:null,
            mode:null
        }
      }
    },
    methods:{
        nuevoIndicador(){
            this.info.title = 'Nuevo indicador'
            this.info.content = {}
            this.info.mode = 'create'
            this.$root.$emit('bv::show::modal', 'formIndicador')
        },
        storeSub(){

        },
        updateSub(){
            
        }
    }
  }
</script>

<style scoped>
    .indicador__red{
        color: red;
    }

    .indicador__yellow{
        color: yellow;
    }

    .indicador__green{
        color: green;
    }
</style>
