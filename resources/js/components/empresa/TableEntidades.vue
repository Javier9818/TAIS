<template>
  <b-container fluid> 
    <!-- User Interface controls -->
    <!-- <b-row class="mb-3">
      <b-col lg="6" class="my-1">
        <b-form-group
          label="Buscar"
          label-cols-sm="3"
          label-align-sm="right"
          label-size="sm"
          label-for="filterInput"
          class="mb-0"
        >
          <b-input-group size="sm">
            <b-form-input
              v-model="filter"
              type="search"
              id="filterInput"
              placeholder="Ingresa para buscar"
            ></b-form-input>
          </b-input-group>
        </b-form-group>
      </b-col>
    </b-row> -->
    <!-- Main table element -->
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
        <b-button size="sm" @click="desactivate(row.item.id)" class="btn-danger" title="Eliminar">
          <i class="fas fa-trash-alt"></i>
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

    <!-- Info modal -->
    <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
      <pre>{{ infoModal.content }}</pre>
    </b-modal>
    <div class="contenedor">
        <button class="botonF1" @click="newElement()">
            <span>+</span>
        </button>
    </div>
    <b-modal id="formCliente" :title="infoModal.title" size="lg" scrollable hide-footer>
      <form-entidad @click="$bvModal.hide('formCliente')" @store="store" @update="update" :dataForm="infoModal" :entidad="entidad"></form-entidad>
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
        fields: [
         { key: 'nombre', label: 'Nombre', sortable: true },
         { key: 'ruc', label: 'RUC', sortable: true, class: 'text-center' },
         { key: 'descripcion', label: 'Descripción', sortable: true },
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
        this.items = entidades;
    },
    mounted() {
      // Set the initial number of items
      this.totalRows = this.items.length
    },
    methods: {
        newElement(){
            this.infoModal.content = {}
            this.infoModal.mode = 'create'
            this.infoModal.title = `Registrar ${this.entidad}`,
            this.$root.$emit('bv::show::modal', 'formCliente')
        },
        desactivate(id){
          if(this.entidad === 'cliente')
              axios.delete(`/api/cliente/${id}`).then( ({data}) => {
                if(data.error){
                    Swal.fire('Error', data.message, 'error');
                }else{
                  Swal.fire('Éxito', 'El cliente se eliminó correctamente', 'success');
                  this.items.map((item, i) => {
                      if(item.id === id) this.items.splice(i, 1);
                  });
                }
              }).catch(()=>{ire('Error', 'Ha ocurrido algún error', 'error');});
          else if(this.entidad === 'proveedor')
            axios.delete(`/api/proveedor/${id}`).then( ({data}) => {
                   if(data.error){
                        Swal.fire('Error', data.message, 'error');
                    }else{
                      Swal.fire('Éxito', 'El proveedor se eliminó correctamente', 'success');
                      this.items.map((item, i) => {
                          if(item.id === id) this.items.splice(i, 1);
                      });
                    }
                }).catch(()=>{ire('Error', 'Ha ocurrido algún error', 'error');});
        },
        redirect(id){
            location.href=`/empresa/${id}`
        },
       store: function(data) {
           this.items = [{...data, estado:true}, ...this.items]
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
