<template>
    <section>
        <div class="row justify-content-end mr-2" v-if="idProceso">
            <button class="btn btn-sm btn-warning mr-2" v-b-modal.crudVariable>Variables de control</button>
            <button class="btn btn-sm btn-danger" @click="nuevoIndicador">Nuevo</button>
        </div>
        <div class="row">
            <div class="col-md-6" v-for="(item, index) in indicadores" :key="index">
                <b-card>
                   <b>Indicador</b>
                   <p>{{item.nombre}}</p>

                   <b>Objetivo</b>
                   <p>{{item.objetivo}}</p>

                   <b>Meta</b>
                   <p>{{item.meta}}</p>

                   <b>Responsable</b>
                   <p>{{item.responsable}}</p>

                   <b>Iniciativas</b>
                   <p>{{item.iniciativas}}</p>

                   <b>Semáforo</b>
                   <p><i class="fas fa-circle indicador__red"></i> Menos de {{item.limiteInferior}}</p>
                   <p><i class="fas fa-circle indicador__yellow"></i> Entre {{item.limiteInferior}} y {{item.limiteSuperior}}</p>
                   <p><i class="fas fa-circle indicador__green"></i> Más de {{item.limiteSuperior}}</p>

                   <b>Fórmula</b>
                   <p v-for="(v, i) in item.listVar" :key="i">{{v.variable}} - {{v.nombreVar}}</p>
                   <p>f = {{item.formula}}</p>
                   <b-row class="justify-content-end">
                       <button class="btn btn-sm btn-warning" @click='editarIndicador(item)'>Editar</button>
                       <button class="btn btn-sm btn-danger ml-2">Eliminar</button>
                   </b-row>
                </b-card>
            </div>
        </div>

        <b-modal id="formIndicador" :title="info.title" size="lg" scrollable hide-footer>
            <form-indicador @click="$bvModal.hide('formIndicador')" @store="storeSub" @update="updateSub" :dataForm="info" :variables="variables"></form-indicador>
        </b-modal>

        <b-modal id="crudVariable" title="Variables de control" size="lg" scrollable hide-footer>
            <crud-varaible @click="$bvModal.hide('crudVariable')" :variables="variables" :idProceso="idProceso" :indicadores="indicadores" @handleVars="handleVars"></crud-varaible>
        </b-modal>
    </section>
</template>

<script>
  import Swal from 'sweetalert2'
  export default {
    props:['idProceso'],
    data() {
      return {
        indicadores:[], 
        variables:[],
        info:{
            title:'',
            content:null,
            mode:null
        }
      }
    },
    methods:{
        handleVars(value){
           this.variables = value
        },
        nuevoIndicador(){
            this.info.title = 'Nuevo indicador'
            this.info.content = {}
            this.info.mode = 'create'
            this.$root.$emit('bv::show::modal', 'formIndicador')
        },
        editarIndicador(item){
            this.info.title = 'Editar indicador'
            this.info.content = item
            this.info.mode = 'edit'
            this.$root.$emit('bv::show::modal', 'formIndicador')
        },
        storeSub(dataIndicador){ 
            axios.post('/api/indicador', {data: JSON.stringify(dataIndicador), idProceso: this.idProceso}).then(({data}) => {
                let {indicador} = data
                this.indicadores.push({...dataIndicador, id: indicador.id});
                this.$root.$emit('bv::hide::modal', 'formIndicador')
                Swal.fire('Exito!!', 'Registro exitoso', 'success');
            });
        },
        obtenerIndicadores(idProceso){
            this.indicadores = [];
            axios.get(`/api/indicador/${idProceso}`).then(({data}) => {
                data.indicadores.forEach(e => {
                    this.indicadores.push({...JSON.parse(e.descripcion), id: e.id});
                });
                this.variables = JSON.parse(data.proceso.variables_control || '[]')
            });
        },
        updateSub(dataIndicador){
            axios.put(`/api/indicador/${dataIndicador.id}`, {data: JSON.stringify(dataIndicador)}).then(({data}) => {
                let {indicador} = data
                this.indicadores.map( i => {
                    if(i.id === indicador.id)
                        i = {...dataIndicador, id: indicador.id}
                })
                this.$root.$emit('bv::hide::modal', 'formIndicador')
                Swal.fire('Exito!!', 'Actualización exitosa', 'success');
            });
        }
    },
    watch:{
        idProceso(id){
            this.obtenerIndicadores(id);
        }
    },
    mounted(){
        if(this.idProceso) this.obtenerIndicadores(this.idProceso);
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
