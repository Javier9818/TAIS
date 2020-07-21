<template>
  <b-container fluid> 
    <div class="row"><h4>Clientes</h4> <button class="btn btn-danger text-white ml-2 mb-1" @click="newElement()">+</button></div>
    <b-overlay :show="loading_clientes">
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

        <template v-slot:cell(actions)="row">
          <b-button size="sm" @click="info(row.item, row.index, $event.target)" class="mr-1">
              Editar
          </b-button>
          <!-- <b-button size="sm" @click="desactivate(row.item.id, row.item.estado)" :class="row.item.estado===1 ? 'btn-danger': 'btn-warning'">
              {{row.item.estado===1 ? 'Desactivar': 'Activar'}}
          </b-button> -->
        </template>
      </b-table>
    </b-overlay>
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

    <!-- Info modal -->
    <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
      <pre>{{ infoModal.content }}</pre>
    </b-modal>

    <b-modal id="formCliente" :title="infoModal.title" size="lg" scrollable hide-footer>
      <form-cadena-cliente @click="$bvModal.hide('formCliente')" @store="store" @update="update" :dataForm="infoModal"></form-cadena-cliente>
    </b-modal>
  </b-container>

</template>

<script>
import Swal from 'sweetalert2'
  export default {
    props:['unidad'],
    data() {
      return {
        loading_clientes:false,
        items: [],
        fields: [
         { key: 'cliente', label: 'Nombre', sortable: true },
         { key: 'nivel', label: 'Nivel', sortable: true, class: 'text-center' },
         { 
           key: 'cliente_padre', 
           label: 'Cliente de',
           formatter: (value, key, item) => {
              return (value === 'd' || value === null) ? `Empresa: ${empresa.nombre}` : value;
            }, 
           sortable: true 
         },
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
        this.loadClientes(this.unidad);
    },
    mounted() {
      this.totalRows = this.items.length
    },
    watch:{
      unidad: function(newValue, oldValue){
        this.loadClientes(newValue);
      }
    },
    methods: {
      loadClientes: function(unidad){
        this.loading_clientes = true;
        axios.get(`/api/cadena_clientes/${unidad}`).then( ({data}) => {
          this.items = data.clientes;
        }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally( () => {
              this.loading_clientes = false;
            });
      },
        newElement(){
            this.infoModal.content = { unidad: this.unidad}
            this.infoModal.mode = 'create'
            this.infoModal.title = `Agregar cliente a cadena`,
            this.$root.$emit('bv::show::modal', 'formCliente')
        },
        // desactivate(id, estado){
        //     axios.post(`/api/empresa/${id}`).then( ({data}) => {
        //         Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        //         this.items.map((item) => {
        //             if(item.id === id) item.estado = estado === 1 ? 0 : 1;
        //         });
        //     }).catch(()=>{ire('Error', 'Ha ocurrido algún error', 'error');});
        // },
        redirect(id){
            location.href=`/empresa/${id}`
        },
       store: function(data) {
           this.items = [{...data, estado:true}, ...this.items]
           this.$root.$emit('bv::hide::modal', 'formCliente')
       },
       update: function(data) {
           this.items.map( (item) => {
               if(item.id === data.id) {item.nombre = data.nombre; item.ruc = data.ruc; item.descripcion = data.descripcion};
           });
       },
      info(item, index, button) {
        // this.infoModal.title = `Row index: ${index}`
        // this.infoModal.content = JSON.stringify(item, null, 2)
        // console.log(item)
        this.infoModal.content = item
        this.infoModal.mode = 'edit'
        this.infoModal.title = `Editar ${this.entidad}`,
        this.$root.$emit('bv::show::modal', 'formCliente', button)
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
