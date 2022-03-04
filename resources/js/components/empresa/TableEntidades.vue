<template>
  <b-container fluid> 
     <div class="accordion" role="tablist">
      <b-card no-body class="mb-1" v-for="(perspectiva, index) in perspectivas" :key="index">
        <b-card-header header-tag="header" class="p-1" role="tab">
          <b-button block v-b-toggle="`accordion-${index}`" variant="info">{{perspectiva.nombre}}</b-button>
        </b-card-header>
        <b-collapse :id="`accordion-${index}`" visible accordion="my-accordion" role="tabpanel">
          <b-card-body>
              <div class="row mb-2" v-for="(objetivo, index_obj) in objetivosPorPerspectiva(perspectiva.id)" :key="index_obj">
                  <div class="col-8">
                    {{objetivo.nombre}}
                  </div>
                  <div class="col-1">
                    <b-button size="sm" @click="info(objetivo, index_obj)" class="mr-1">
                      Editar
                    </b-button>
                  </div>
                  <div class="col-1">
                    <b-button size="sm" @click="desactivate(objetivo.id)" class="btn-danger" title="Eliminar">
                      <i class="fas fa-trash-alt"></i>
                    </b-button>
                  </div>
                  <div class="col-1">
                    <b-button 
                    size="sm" 
                    @click="handlePrio(objetivo.id)" 
                    :class="objetivo.IS_PRIO === 1 ? 'btn-success' : 'btn-warning'" 
                    :title="objetivo.IS_PRIO === 1 ? 'Quitar prioridad' : 'Priorizar'"
                    >
                      {{objetivo.IS_PRIO === 1 ? "Prioritario" : "No prioritario"}}
                    </b-button>
                  </div>
              </div>
              <div class="row justify-content-center" v-if="objetivosPorPerspectiva(perspectiva.id).length === 0 ">
                  <p class="text-center">No se encontraron registros.</p>
              </div>
          </b-card-body>
        </b-collapse>
      </b-card>
    </div>
  
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
        IS_PRIO: 1,
        items: [],
        perspectivas: [],
        infoModal: {
          id: 'info-modal',
          title: ``,
          mode: 'create',
          content: ''
        }
      }
    },
    computed: {},
    created(){
        this.items = objetivos;
    },
    mounted() {
      // Set the initial number of items
      this.totalRows = this.items.length
      this.perspectivas = perspectivas
    },
    methods: {
        newElement(){
            this.infoModal.content = {}
            this.infoModal.mode = 'create'
            this.infoModal.title = `Registrar objetivo estratégico`,
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
           this.items = [{...data}, ...this.items]
       },
       update: function(data) {
          this.items.map( (item) => {
              if(item.id === data.id) {item.nombre = data.nombre; item.ruc = data.ruc; item.descripcion = data.descripcion};
          });
       },
      info(item, index, button) {
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
      },
      objetivosPorPerspectiva(perspectivaID){
        return  this.items.filter((objetivo) => {
            return ( objetivo.perspectiva_fk === perspectivaID )
        });
      },
      handlePrio(objetivoID){
        axios.post('/api/prioridad-objetivo', { objetivoID }).then(({ data }) => {
            Swal.fire('success', 'Operación exitosa', 'success').then( () => {
              location.reload()
            });
        });
      }
    }
  }
</script>
