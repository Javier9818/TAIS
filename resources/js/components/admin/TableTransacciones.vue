<template>
  <b-container fluid>
    <!-- User Interface controls -->
    <div class="row my-2">
      <div class="col-6">
        <select v-model="accion" class="form-control">
          <option :value="1" selected>Todas</option>
          <option  v-for="(obj, index) in acciones" :value="obj" :key="index">{{obj}}</option>
        </select>
      </div>
    </div>
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
  </b-container>
</template>

<script>
import Swal from 'sweetalert2'
  export default {
    data() {
      return {
        items: [],
        fields: [
          { key: 'user.email', label: 'Usuario', sortable: true },
          { key: 'tabla', label: 'Tabla', sortable: true },
          { key: 'accion', label: 'Acción', sortable: true },
          { key: 'terminal', label: 'Terminal', sortable: true },
          { key: 'created_at', label: 'fecha', sortable: true },
          // { key: 'navegador', label: 'Navegador', sortable: true }
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
        accion: 1,
        acciones: []
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
        this.items = transacciones
        this.acciones = acciones
    },
    mounted() {
      // Set the initial number of items
      this.totalRows = transacciones.length
    },
    methods: {
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length
        this.currentPage = 1
      }
    },
    watch:{
      accion(){
        axios.get(`/api/transacciones/${this.accion}`).then( ({data}) => {
          this.items = data.transacciones
        });;
      }
    }
  }
</script>
1