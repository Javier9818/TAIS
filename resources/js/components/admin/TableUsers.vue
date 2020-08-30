<template>
  <b-container fluid>
    <!-- User Interface controls -->

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
        <b-button size="sm" @click="desactivate(row.item.id)" class="btn-danger">
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
    <!-- <b-modal :id="infoModal.id" :title="infoModal.title" ok-only @hide="resetInfoModal">
      <form-user @click="$bvModal.hide('formUser')" @store="store" :dataForm ="infoModal"></form-user>
    </b-modal> -->
    <div class="contenedor">
        <button class="botonF1" @click="newElement()">
            <span>+</span>
        </button>
    </div>
    <b-modal id="formUser" :title="infoModal.title" size="lg" scrollable hide-footer>
      <form-user @click="$bvModal.hide('formUser')" @store="store" @update="update" :dataForm ="infoModal"></form-user>
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
          { key: 'email', label: 'Email', sortable: true },
          { key: 'isAdmin', label: 'Cargo', sortable: true,formatter: (value, key, item) => {
              return (value === 1 || value === true) ? 'Super usuario' : 'Administrador'
          }},
          { key: 'name_empresa', label: 'Empresa', sortable: true, class: 'text-center' , formatter: (value, key, item) => {
              return value === null ? '*' : value
          }},
        //   {
        //     key: 'estado',
        //     label: 'Estado',
        //     formatter: (value, key, item) => {
        //       return value ? 'Activo' : 'Inactivo'
        //     },
        //     sortable: true,
        //     sortByFormatted: true,
        //     filterByFormatted: true
        //   },
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
          content: '',
          mode: 'create'
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
        users.forEach( e => {
          this.items.push({
            ...e,
            scopes: e.scopes ? e.scopes.split(",") : e.scopes
          })
        })
    },
    mounted() {
      // Set the initial number of items
      this.totalRows = this.items.length
    },
    methods: {
        desactivate(id){
           Swal.fire({
            title: '¿Estás seguro?',
            text: "Tú no puedes revertir esto",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, borrar usuario!'
          }).then((result) => {
            if (result.value) {
                axios.delete(`/api/user/${id}`).then( ({data}) => {
                  Swal.fire('Éxito', 'Se ha eliminado el usuario', 'success');
                  this.items.map((item, i) => {
                      if(item.id === id) this.items.splice(i,1);
                  });
                }).catch(()=>{Swal.fire('Error', 'Ha ocurrido algún error', 'error');});
            }
          })
            
        },
        redirect(id){
            location.href=`/empresa/${id}`
        },
       store: function(data) {
           this.items = [{...data, estado:true}, ...this.items]
       },
       update: function(data) {
         console.log(data)
         this.items.map( (e) => {
           if(e.id === data.id)
              e = data
         });
       },
      info(item, index, button) {
        this.infoModal.content = { 
          ...item
        }
        this.infoModal.mode = 'edit'
        this.infoModal.title = `Editar usuario`,
        this.$root.$emit('bv::show::modal', 'formUser', button)
      },
      newElement(){
            this.infoModal.content = { }
            this.infoModal.mode = 'create'
            this.infoModal.title = `Nuevo usuario`,
            this.$root.$emit('bv::show::modal', 'formUser')
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
