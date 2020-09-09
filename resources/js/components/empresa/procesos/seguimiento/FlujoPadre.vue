<template>
  <section style="width: 100%">
      <selector-proceso @changue-process='setProcess'></selector-proceso>
      <div class="container-fluid" v-if="process">
        <form @submit.prevent="onSubmit">
            <div class="row">
              <div class="col-md-11">
                <b-form-group label="Documento de caracterización">
                  <b-form-checkbox
                      id="checkbox-1"
                      v-model="form.flag_red"
                      name="checkbox-1"
                      value="accepted"
                      unchecked-value="not_accepted"
                    >
                    Rediseño
                  </b-form-checkbox>
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
                    <b-input v-model="form.descripcion" required></b-input>
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
              {{row.item.version === null ? 'Versión actual': row.item.version}}{{row.item.flag_red === 1 ? ' - rediseño':''}}
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
       enableRed:true,
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
          file_name:null,
          flag_red:'not_accepted'
        }
      }
    },
    methods: {
     setProcess(id){
       this.process = id
       this.enableRed = true;
       this.enable = true;
       this.file = null
       this.reset()
       axios.get(`/api/diagrama-flujo/${id}`).then(({data}) => {
          let {diagramas} = data
          diagramas.forEach((c)=>{
            if(c.version === null && c.flag_red === 1) this.enableRed = false;
            if(c.version === null && c.flag_red === 0) this.enable = false;
          });
          this.items = diagramas;
         
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
          formData.append('flag_red', this.form.flag_red)

          axios.post(`/api/diagrama-flujo`, formData).then(({data})=>{
              Swal.fire('Exito!', 'Registro exitoso!', 'success');
              this.setProcess(this.process)
          })
       }else Swal.fire('Error!', 'Necesita carga un documento', 'error')
      },
      edit(item, index){
          this.form = {...item, flag_red: item.flag_red == 1 ? 'accepted': 'not_accepted' ,file:null}
      },
      reset(){
        this.form={
          descripcion:'',
          file:null,
          file_name:null,
          flag_red:'not_accepted'
        }
      },
      deleteItem(id){
        axios.delete(`/api/diagrama-flujo/${id}`).then(()=>{
              Swal.fire('Exito!', 'Operación exitosa!', 'success');
              this.setProcess(this.process)
              this.enable = true;
        });
      },
      download(item){
        var a = document.createElement('a');
        a.download = `Diagrama de flujo del proceso ${item.proceso} - versión - ${item.version === null ? 'Actual' : item.version}`;
        a.target = '_blank';
        a.href= `/storage/diagrama-flujos/${item.file_name}`;
        a.click();
      }
    }
  }
</script>
