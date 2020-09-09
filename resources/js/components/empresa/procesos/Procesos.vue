<template>
  <b-container fluid> 
    <div class="row">
        <div class="col-6">
            <h5>Procesos</h5> <button class="btn btn-sm btn-danger float-right mb-1" @click="newElement()">Nuevo</button>
            <b-table
            show-empty
            small
            stacked="md"
            :items="items"
            :fields="fields"
            :current-page="currentPage"
            :per-page="perPage"
            :filter="filter"
            :filterIncludedFields="filterOn"
            :sort-by.sync="sortBy"
            :sort-desc.sync="sortDesc"
            :sort-direction="sortDirection"
            @filtered="onFiltered"
            >

            <template v-slot:cell(nombre)="row">
                <a href="javascript::void(0)" @click="listSub(row.item.id, row.item.nombre)" v-if="row.item.estado === 1">{{row.item.nombre}}</a>
                <p v-else>{{row.item.nombre}}</p>
            </template>

            <template v-slot:cell(actions)="row">
                <b-button size="sm" @click="info(row.item, row.index, $event.target)" class="mr-1 btn-success" v-if="row.item.estado === 1">
                    <i class="fas fa-pencil-alt"></i>
                </b-button>
                <b-button size="sm" @click="desactivate(row.item.id)" class="btn-danger" v-if="row.item.estado === 1">
                    <i class="fas fa-trash-alt"></i>
                </b-button>
                <b-button size="sm" @click="desactivate(row.item.id)" class="btn-warning" v-else>
                    Activar
                </b-button>
            </template>
            </b-table>
            <b-col sm="7" md="6" class="my-1">
                <b-pagination
                v-model="currentPage"
                :total-rows="totalRows"
                :per-page="perPage"
                align="fill"
                size="sm"
                class="my-0"
                ></b-pagination>
            </b-col>
        </div>
        
        <div class="col-6">
          <b-overlay :show="loadingSubs">
            <div v-if="procesoSelected!==null">
              <h5>SubProcesos de "{{nombreProcesoSelected}}"</h5> <button class="btn btn-sm btn-danger float-right mb-1" @click="newSubProceso()" >Nuevo</button>
              <b-table
              show-empty
              small
              stacked="md"
              :items="subprocesos"
              :fields="fieldSub"
              :current-page="currentPage"
              :per-page="perPage"
              :filter="filter"
              :filterIncludedFields="filterOn"
              :sort-by.sync="sortBy"
              :sort-desc.sync="sortDesc"
              :sort-direction="sortDirection"
              @filtered="onFiltered"
              >
              <template v-slot:cell(actions)="row">
                  <b-button size="sm" @click="infoSub(row.item, row.index, $event.target)" class="mr-1 btn-success">
                      <i class="fas fa-pencil-alt"></i>
                  </b-button>
                  <b-button size="sm" @click="desactivateSub(row.item.id)" class="btn-danger" v-if="row.item.estado === 1">
                    <i class="fas fa-trash-alt"></i>
                </b-button>
                <b-button size="sm" @click="desactivateSub(row.item.id)" class="btn-warning" v-else>
                    Activar
                </b-button>
              </template>
              </b-table>
              <b-col sm="7" md="6" class="my-1">
                  <b-pagination
                  v-model="currentPage"
                  :total-rows="totalRows"
                  :per-page="perPage"
                  align="fill"
                  size="sm"
                  class="my-0"
                  ></b-pagination>
              </b-col>
            </div>
          </b-overlay>
        </div>
        
    </div>
    

    <b-modal id="formProceso" :title="infoModal.title" size="lg" scrollable hide-footer>
      <form-proceso @click="$bvModal.hide('formProceso')" @store="store" @update="update" :dataForm="infoModal"></form-proceso>
    </b-modal>

    <b-modal id="formSubProceso" :title="infoModalSub.title" size="lg" scrollable hide-footer>
      <form-subproceso @click="$bvModal.hide('formSubProceso')" @store="storeSub" @update="updateSub" :dataForm="infoModalSub"></form-subproceso>
    </b-modal>

  </b-container>

</template>

<script>
import Swal from 'sweetalert2'
  export default {
    props:['entidad'],
    data() {
      return {
        items: [],
        procesoSelected:null,
        loadingSubs:false,
        nombreProcesoSelected:null,
        subprocesos:[],
        fields: [
         { key: 'nombre', label: 'Nombre', sortable: true },
         { key: 'megaprocesos_names', label: 'Megaproceso', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ],
        fieldSub: [
         { key: 'nombre', label: 'Nombre', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ],
        totalRows: 1,
        currentPage: 1,
        perPage: 8,
        pageOptions: [5, 10, 15],
        sortBy: '',
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        filterOn: [],
        infoModal: {
          id: 'info-modal',
          title: ``,
          mode: 'create',
          content: ''
        },
        infoModalSub: {
          id: 'info-modal',
          title: ``,
          mode: 'create',
          content: ''
        }
      }
    },
    computed: {
      sortOptions() {
        // Create an options list from our fields
        return this.fields
          .filter(f => f.sortable)
          .map(f => {
            return { text: f.label, value: f.key }
          })
      }
    },
    created(){
        this.items = procesos;
    },
    mounted() {
      this.totalRows = this.items.length
    },
    methods: {
        async listSub(id, nombre){
            this.loadingSubs = true;
            await axios.get(`/api/subproceso-all/${unidad}/${id}`).then(({data})=>{
                this.subprocesos = data.subprocesos;
            }).then(()=>{
              this.loadingSubs = false;
            });
            this.procesoSelected = id;
            this.nombreProcesoSelected = nombre;
        },
        newElement(){
            this.infoModal.content = {}
            this.infoModal.mode = 'create'
            this.infoModal.title = `Registrar proceso`,
            this.$root.$emit('bv::show::modal', 'formProceso')
        },
        newSubProceso(){
            this.infoModalSub.content = {proceso: this.procesoSelected}
            this.infoModalSub.mode = 'create'
            this.infoModalSub.title = `Registrar subproceso`,
            this.$root.$emit('bv::show::modal', 'formSubProceso')
        },
        desactivate(id){
            axios.delete(`/api/proceso/${id}/${unidad}`).then( ({data}) => {
                let {proceso, error, message} = data;
                let items = this.items;
                this.listSub(0,'')
                if(error)
                  Swal.fire('Error', message, 'error');
                else{
                  let index = items.findIndex((item) => { return item.id === id })
                  this.items.splice(index, 1, {...items[index], estado: items[index].estado === 1 ? 0 : 1})
                  
                  Swal.fire('Éxito', 'Se cambió el estado del proceso', 'success');
                }
                
            }).catch(()=>{Swal.fire('Error', 'Ha ocurrido algún error', 'error');});
        },
        desactivateSub(id){
            axios.delete(`/api/subproceso/${id}/${unidad}`).then( ({data}) => {
                let {proceso, error, message} = data;
                let items = this.subprocesos;
                if(error)
                  Swal.fire('Error', message, 'error');
                else{
                  let index = items.findIndex((item) => { return item.id === id })
                  this.subprocesos.splice(index, 1, {...items[index], estado: items[index].estado === 1 ? 0 : 1})
                  
                  Swal.fire('Éxito', 'Se cambió el estado del proceso', 'success');
                }
                
            }).catch(()=>{Swal.fire('Error', 'Ha ocurrido algún error', 'error');});
        },
        redirect(id){
            location.href=`/empresa/${id}`
        },
        store: function(data) {
            this.items = [{...data, estado:1}, ...this.items]
        },
        storeSub: function(data) {
            this.subprocesos = [{...data}, ...this.subprocesos]
        },
        update: function(data) {
            this.items.map( (item) => {
                if(item.id === data.id) {
                  item.nombre = data.nombre
                  item.descripcion = data.descripcion
                  item.megaprocesos_names = data.megaproceso.toString();
                  item.megaproceso = data.megaproceso.toString();
                };
            });
        },
        updateSub: function(data) {
         this.subprocesos.map( (item) => {
              if(item.id === data.id) {
                item.nombre = data.nombre
                item.descripcion = data.descripcion
            };
        });
       },
        info(item, index, button) {
        this.infoModal.content = {...item, megaproceso: (item.megaproceso).split(',')}
        this.infoModal.mode = 'edit'
        this.infoModal.title = `Editar proceso`,
        this.$root.$emit('bv::show::modal', 'formProceso', button)
      },
        infoSub(item, index, button) {
        this.infoModalSub.content = {...item}
        this.infoModalSub.mode = 'edit'
        this.infoModalSub.title = `Editar subproceso`,
        this.$root.$emit('bv::show::modal', 'formSubProceso', button)
      },
        resetInfoModal() {
        this.infoModal.title = ''
        this.infoModal.content = ''
      },
        onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length
        this.currentPage = 1
      }
    }
  }
</script>
