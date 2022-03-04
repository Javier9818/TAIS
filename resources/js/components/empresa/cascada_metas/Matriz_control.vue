<template>
  <div class="container-fluid">
       <component-version></component-version>
       <p>En el cálculo de los objetivos de control resultantes, se consideran las relaciones Principales(P) con puntaje 2, y las relaciones Secundarias(S) con puntaje 1, cada meta objetivo de control cuyo puntaje (Suma de puntaje referidos por tipo de relación) sea mayor o igual al promedio serán seleccionadas. </p>
       <hr>
       <table class="table">
           <thead>
               <tr>
                    <td></td>
                    <td v-for="(meta, index) in metas" :key="index" class="text-center"> <a href="javascript:void(0)" :title="meta.nombre"> {{meta.sigla}} </a> </td>
               </tr>
           </thead>
           <tbody>
               <tr v-for="(control, index) in controles" :key="index">
                   <td class="text-center"><a href="javascript:void(0)" :title="control.nombre"> {{control.sigla}} </a></td>
                   <td v-for="(meta, index) in metas" :key="index" :class="buscarEnMatriz(control.id, meta.id) == null ? '' : (buscarEnMatriz(control.id, meta.id).peso === 1 ? 'text-center secundario' : 'text-center primario')"> 
                       {{ buscarEnMatriz(control.id, meta.id) == null ? '' : (buscarEnMatriz(control.id, meta.id).peso === 1 ? 'S' : 'P') }}
                    </td>
               </tr>
           </tbody>
       </table>

       <div class="row justify-content-center mt-4">
            <div class="col-11 p-4" style="border: solid 1px black;">
                    <h3>Metas de alineamiento resultantes</h3>
                    <ul style="font-size:1.2em;">
                    <li v-for="(resultante, index) in resultantes" :key="index">
                        {{resultante.control.sigla}} - {{resultante.control.nombre}}
                    </li>
                    </ul>
            </div>
        </div>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        matriz: [],
        resultantes : [],
        controles:[],
        metas: []
      }
    },
    methods: {
        buscarEnMatriz(control_fk, meta_fk){
            var item = null
            for (let index = 0; index < this.matriz.length; index++) {
                if(this.matriz[index].alineamiento_fk === meta_fk && this.matriz[index].control_fk === control_fk)
                    item = this.matriz[index]
            }

            return item
        }
    },
    mounted(){
        this.matriz = matriz
        this.resultantes = resultante
        this.controles = control
        this.metas = metas

        console.log(matriz)
    }
  }
</script>

<style scoped>
    .primario{
        background: rgb(240, 245, 245);
        color: rgb(47, 71, 207);
        font-size: 1.3em;
        font-weight: bold;
    }

    .secundario{
        background: rgb(240, 245, 245);
        color: rgb(105, 105, 105);
        font-size: 1.3em;
        font-weight: bold;
    }
</style>