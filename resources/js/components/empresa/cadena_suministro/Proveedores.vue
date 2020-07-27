<template>
  <b-container fluid> 
    <div class="row"><h4>Proveedores</h4> <button class="btn btn-danger text-white ml-2 mb-1" @click="newElement()">+</button></div>
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
          <b-button size="sm" @click="info(row.item, row.index, $event.target)" class="mr-1" title="Editar">
              Editar
          </b-button>
          <b-button size="sm" @click="desactivate(row.item.clienteId)" class="btn-danger" title="Eliminar">
             <i class="fas fa-trash-alt"></i>
          </b-button>
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

    <b-modal id="formProveedor" :title="infoModal.title" size="lg" scrollable hide-footer>
      <form-cadena-proveedor @click="$bvModal.hide('formProveedor')" @store="store" @update="update" :dataForm="infoModal"></form-cadena-proveedor>
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
        //  { key: 'clienteId', label: 'ID', sortable: true },
         { key: 'cliente', label: 'Nombre', sortable: true },
         { key: 'nivel', label: 'Nivel', sortable: true, class: 'text-center' },
         { 
           key: 'nombrePadre', 
           label: 'Provee a',
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
        this.loadProveedores(this.unidad);
    },
    mounted() {
      this.totalRows = this.items.length
    },
    watch:{
      unidad: function(newValue, oldValue){
        this.loadProveedores(newValue);
      },
      items: function(){
          this.totalRows = this.items.length
      } 
    },
    methods: {
      loadProveedores(unidad){
        this.loading_clientes = true;
        axios.get(`/api/cadena_proveedores/${unidad}`).then( ({data}) => {
          let {proveedores} = data;
          var aux;
          proveedores.map(e => {
            aux = e.padres
             if(e.padres === null){
               e.nombrePadre = `Empresa: ${empresa.nombre}`
               e.proveedores_padre = [{nombre: empresa.nombre, id: null}];
             }else{
                e.proveedores_padre = []
                e.nombrePadre = ''
                console.log(aux)
                aux.split(',').map( (obj) => {
                  var a = obj.split('-')
                  e.nombrePadre = e.nombrePadre + `${a[0]} - `
                  e.proveedores_padre.push({id: parseInt(a[1]), nombre: a[0]})
                })
             }
             
          });
          this.items = proveedores;
        }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally( () => {
              this.loading_clientes = false;
            });
      },
        newElement(){
            this.infoModal.content = { unidad: this.unidad}
            this.infoModal.mode = 'create'
            this.infoModal.title = `Agregar proveedor a cadena`,
            this.$root.$emit('bv::show::modal', 'formProveedor')
        },
        desactivate(id){
          Swal.fire({
            title: '¿Estás seguro?',
            text: "Tú no puedes revertir esto",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Si, borrar proveedor!'
          }).then((result) => {
            if (result.value) {
              axios.delete(`/api/cadena_proveedores/${this.unidad}/${id}`).then( ({data}) => {
                  if (data.error)
                    Swal.fire('ERROR', data.message, 'error');
                  else{
                    this.items.map((item, i) => {
                        if(item.clienteId === id) this.items.splice(i, 1)
                    });
                    Swal.fire('ÉXITO!', data.message, 'success');
                  }
                  
              }).catch(()=>{Swal.fire('Error', 'Ha ocurrido algún error', 'error');});
            }
          })

            
        },
        redirect(id){
            location.href=`/empresa/${id}`
        },
       store(data) {
           this.items = [{...data, estado:true}, ...this.items]
           this.$root.$emit('bv::hide::modal', 'formProveedor')
       },
       update(data) {
          this.loadProveedores(this.unidad)
       },
      info(item, index, button) {
        this.infoModal.content = { 
          unidad: this.unidad,
          ...item
        }
        this.infoModal.mode = 'edit'
        this.infoModal.title = `Editar proveedor en cadena`,
        this.$root.$emit('bv::show::modal', 'formProveedor', button)
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
