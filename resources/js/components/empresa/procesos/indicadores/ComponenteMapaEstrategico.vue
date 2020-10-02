<template>
<b-container fluid> 
    <div class="row">
        <div class="col-12">
            <h5>Objetivos</h5> 
            <button class="btn btn-sm btn-primary float-right mb-1 mr-1" @click="nuevoObjetivo()">Nuevo</button>
            <button class="btn btn-sm btn-danger float-right mb-1 mr-1" @click="verMapa()">Ver Gráfico</button>
            <button class="btn btn-sm btn-warning float-right mb-1 mr-1" @click="verPerspectivas()"><i class="fas fa-cogs"></i></button>
            <b-table
            show-empty
            small
            stacked="md"
            :items="objetivos"
            :fields="fields"
            >
            <template v-slot:cell(actions)="row">
                <b-button size="sm" @click="editaObjetivo(row.item)" class="mr-1 btn-success">
                    <i class="fas fa-pencil-alt"></i>
                </b-button>
                <b-button size="sm" @click="eliminaObjetivo(row.item.id)" class="btn-danger">
                   <i class="fas fa-trash"></i>
                </b-button>
            </template>
            <template v-slot:cell(perspectiva)="row">
                {{perspectivas[row.item.perspectiva]}}
            </template>
            </b-table>
        </div>
    </div>


    <b-modal id="formObjetivo" :title="modalObjetivo.title" size="lg" scrollable hide-footer>
      <form-objetivo @click="$bvModal.hide('formObjetivo')" @store="store" @update="update" :payload="modalObjetivo"></form-objetivo>
    </b-modal>

    <b-modal id="crudPerspectica" :title="modalObjetivo.title" size="lg" scrollable hide-footer>
      <!-- <form-subproceso @click="$bvModal.hide('crudPerspectica')" @store="storeSub" @update="updateSub" :dataForm="modalObjetivo"></form-subproceso> -->
    </b-modal>

  </b-container>
</template>

<script>
  import Swal from 'sweetalert2'
  export default {
    props:['idProceso'],
    data() {
      return {
        objetivos:[],
        perspectivas:[],
        modalObjetivo:{
            title:'',
            mode:'',
            content:'',
            perspectivas:[],
            objetivos:[]
        },
        fields: [
         { key: 'perspectiva', label: 'Perspectiva', sortable: true },
         { key: 'nombre', label: 'Nombre', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ]
      }
    },
    methods: {
        nuevoObjetivo(){
            let { modalObjetivo, perspectivas, objetivos } = this
            modalObjetivo.title = 'Nuevo Objetivo'
            modalObjetivo.content = {}
            modalObjetivo.perspectivas = perspectivas
            modalObjetivo.objetivos = objetivos
            modalObjetivo.mode = 'create'
            this.$root.$emit('bv::show::modal', 'formObjetivo')
        },
        verMapa(){

        },
        verPerspectivas(){

        },
        editaObjetivo(item){
            let { modalObjetivo, perspectivas, objetivos } = this
            modalObjetivo.title = 'Editar Objetivo'
            modalObjetivo.content = item
            modalObjetivo.mode = 'edit'
             modalObjetivo.perspectivas = perspectivas
            modalObjetivo.objetivos = objetivos
            this.$root.$emit('bv::show::modal', 'formObjetivo')
        },
        eliminaObjetivo(){

        },
        getPerpesctivas(idProceso){
            axios.get(`/api/perspectivas/${idProceso}`).then(({data}) => {
                this.perspectivas = JSON.parse(data.perspectivas);
                console.log(JSON.parse(data.perspectivas))
            });

            axios.get(`/api/objetivo-mapa/${idProceso}`).then(({data}) => {
                data.objetivos.forEach(e => {
                    this.objetivos.push({...JSON.parse(e.descripcion), id: e.id});
                });
            });
        },
        store(dataObjetivo){
            let {idProceso} = this
            axios.post(`/api/objetivo-mapa`, {data: JSON.stringify(dataObjetivo), idProceso}).then(({data}) => {
                let {objetivo} = data
                this.objetivos.push({...dataObjetivo, id: objetivo.id});
                this.$root.$emit('bv::hide::modal', 'formObjetivo')
                Swal.fire('Exito!!', 'Registro exitoso', 'success');
            });
        },
        update(dataObjetivo){
            axios.put(`/api/objetivo-mapa/${dataObjetivo.id}`, {data: JSON.stringify(dataObjetivo)}).then(({data}) => {
                let {objetivo} = data
                this.objetivos.map( i => {
                    if(i.id === objetivo.id)
                        i = {...dataObjetivo, id: objetivo.id}
                })
                this.$root.$emit('bv::hide::modal', 'formObjetivo')
                Swal.fire('Exito!!', 'Actualización exitosa', 'success');
            });
        }
    },
    mounted(){
        this.idProceso ? this.getPerpesctivas(this.idProceso) : () => {}
    },
    watch:{
        idProceso(id){
            this.getPerpesctivas(id)
        }
    }
  }
</script>
