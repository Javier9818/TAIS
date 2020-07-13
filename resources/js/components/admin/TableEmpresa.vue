<template>
  <b-container fluid>
    <!-- User Interface controls -->
    <b-row class="mb-3">
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
      <!-- <b-col lg="6" class="my-1">
        <b-form-group
          label="Buscar por"
          label-cols-sm="3"
          label-align-sm="right"
          label-size="sm"
          class="mb-0">
          <b-form-checkbox-group v-model="filterOn" class="mt-1">
            <b-form-checkbox value="name">Name</b-form-checkbox>
            <b-form-checkbox value="age">Age</b-form-checkbox>
            <b-form-checkbox value="isActive">Active</b-form-checkbox>
          </b-form-checkbox-group>
        </b-form-group>
      </b-col> -->
    </b-row>
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
          Info modal
        </b-button>
        <b-button size="sm" @click="redirect(row.item.id)" class="btn-success">
            Ir a panel
        </b-button>
        <b-button size="sm" @click="desactivate(row.item.id, row.item.estado)" :class="row.item.estado===1 ? 'btn-danger': 'btn-warning'">
            {{row.item.estado===1 ? 'Desactivar': 'Activar'}}
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
        <button class="botonF1" @click="$bvModal.show('formEmpresa')">
            <span>+</span>
        </button>
    </div>
    <b-modal id="formEmpresa" title="Nueva empresa" size="lg" scrollable hide-footer>
      <form-empresa @click="$bvModal.hide('formEmpresa')" @store="store"></form-empresa>
    </b-modal>
  </b-container>

</template>

<script>
import Swal from 'sweetalert2'
  export default {
    data() {
      return {
        items: [],
        fields: [
          { key: 'nombre', label: 'Nombre', sortable: true },
          { key: 'ruc', label: 'RUC', sortable: true, class: 'text-center' },
          {
            key: 'estado',
            label: 'Estado',
            formatter: (value, key, item) => {
              return value ? 'Activo' : 'Inactivo'
            },
            sortable: true,
            sortByFormatted: true,
            filterByFormatted: true
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
          title: '',
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
        this.items = empresas;
    },
    mounted() {
      // Set the initial number of items
      this.totalRows = this.items.length
    },
    methods: {
        desactivate(id, estado){
            axios.post(`/api/empresa/${id}`).then( ({data}) => {
                Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
                this.items.map((item) => {
                    if(item.id === id) item.estado = estado === 1 ? 0 : 1;
                });
            }).catch(()=>{ire('Error', 'Ha ocurrido algún error', 'error');});
        },
        redirect(id){
            location.href=`/empresa/${id}`
        },
       store: function(data) {
           this.items = [{...data, estado:true}, ...this.items]
       },
      info(item, index, button) {
        this.infoModal.title = `Row index: ${index}`
        this.infoModal.content = JSON.stringify(item, null, 2)
        this.$root.$emit('bv::show::modal', this.infoModal.id, button)
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
