<template>
  <section style="width: 100%">
      <selector-proceso @changue-process='setProcess'></selector-proceso>
      <div class="container-fluid" v-if="process">
        <form @submit.prevent="onSubmit" v-if="enable">
            <div class="row">
              <div class="col-md-11">
                <b-form-group label="Documento de caracterización">
                    <b-form-file
                      v-model="file"
                      :state="Boolean(file)"
                      placeholder="Cargar archivo o arrastrar aquí"
                      drop-placeholder="Borrar archivo..."
                      @change="getFile"
                      
                    ></b-form-file>
                </b-form-group>
              </div>
              <div class="col-md-11">
                <b-form-group label="Descripcion">
                    <b-input v-model="form.descripcion" ></b-input>
                </b-form-group>
              </div>
            </div>
            <button class="btn btn-sm btn-success my-1">Guardar cambios</button>
        </form>

        <b-table
          show-empty
          small
          stacked="md"
          :items="items"
          :fields="fields"
          >
          <template v-slot:cell(actions)="row">
              <b-button size="sm" @click="edit(row.item, row.index)" class="mr-1 btn-success" v-if="row.item.version === null">
                  <i class="fas fa-pencil-alt"></i>
              </b-button>
              <b-button size="sm" @click="deleteItem(row.item.id)" class="btn-danger" v-if="row.item.version === null">
                  <i class="fas fa-trash-alt"></i>
              </b-button>
              <b-button size="sm" @click="download(row.item)" class="btn-warning">
                  <i class="fas fa-arrow-down"></i>
              </b-button>
          </template>
          <template v-slot:cell(version)="row">
              {{row.item.version === null ? 'Versión actual': row.item.version}}
          </template>
        </b-table>
      </div>
  </section>
</template>

<script>
import Swal from 'sweetalert2';
  export default {
    data() {
      return {
       file: null,
       descripcion:'',
       process:null,
       enable:true,
       items:[],
       fields: [
         { key: 'proceso', label: 'Proceso', sortable: true },
         { key: 'descripcion', label: 'Descripcion', sortable: true },
         { key: 'version', label: 'Version', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ],
        form:{
          descripcion:'',
          file:null,
          file_name:null
        }
      }
    },
    methods: {
     setProcess(id){
       this.process = id
       this.enable = true;
       axios.get(`/api/caracterizacion/${id}`).then(({data}) => {
          let {caracterizaciones} = data
          caracterizaciones.forEach((c)=>{
            if(c.version === null) this.enable = false;
          });
          this.items = caracterizaciones;
          this.reset()
       });
     },
     getFile(e){
       let file = e.target.files[0];
       console.log(file)
       this.form.file = file
     },
     onSubmit(){
       if(this.form.file !== null){
         let formData = new FormData()
          formData.append('descripcion', this.form.descripcion)
          formData.append('file', this.form.file)
          formData.append('proceso_id', this.process)
          formData.append('file_name', this.form.file_name)

          axios.post(`/api/caracterizacion`, formData).then(({data})=>{
              Swal.fire('Exito!', 'Registro exitoso!', 'success');
              this.setProcess(this.process)
          })
       }else Swal.fire('Error!', 'Necesita carga un documento', 'error')
      },
      edit(item, index){
          this.form = {...item, file:null}
          this.enable = true  
      },
      reset(){
        this.form={
          descripcion:'',
          file:null,
          file_name:null
        }
      },
      deleteItem(id){
        axios.delete(`/api/caracterizacion/${id}`).then(()=>{
              Swal.fire('Exito!', 'Operación exitosa!', 'success');
              this.setProcess(this.process)
              this.enable = true;
        });
      },
      download(item){
        var a = document.createElement('a');
        a.download = `Cararacterizacion del proceso ${item.proceso} - versión - ${item.version === null ? 'Actual' : item.version}`;
        a.target = '_blank';
        a.href= `/storage/caracterizaciones/${item.file_name}`;
        a.click();
      }
    }
  }
</script>
