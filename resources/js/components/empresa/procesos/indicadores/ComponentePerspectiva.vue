<template>
  <section>
      <div class="row">
        <div class="col-12">
            <form @submit.prevent="onSubmit">
                <b-form-group label="Nombre" label-for="nombre">
                    <input type="text" id="nombre" v-model="name" required>
                    <button class="btn btn-sm btn-primary" type="submit" v-if="!edit" :disabled="!$v.name.repeat || !$v.name.minLength || !$v.name.text">Insertar</button>
                    <button class="btn btn-sm btn-primary" type="submit" v-if="edit" :disabled="!$v.name.repeat || !$v.name.minLength || !$v.name.text">Actualizar</button>
                    <button class="btn btn-sm btn-danger" type="submit" v-if="edit" @click="edit = false; name=''; editId=null">Nuevo</button>
                </b-form-group>
                <p v-if="!$v.name.repeat" class="help text-danger">Este campo ya se encuetra registrado</p>
                <p v-else-if="!$v.name.minLength" class="help text-danger">Este campo es inválido</p>
                <p v-else-if="!$v.name.text" class="help text-danger">Este campo es inválido</p>
            </form>
        </div>
        <div class="col-12">
            <b-table
            show-empty
            small
            stacked="md"
            :items="dataP"
            :fields="fields"
            >
            <template v-slot:cell(actions)="row">
                <b-button size="sm" @click="editaObjetivo(row.item)" class="mr-1 btn-success">
                    <i class="fas fa-pencil-alt"></i>
                </b-button>
                <b-button size="sm" @click="deleteObj(row.item.id)" class="btn-danger">
                   <i class="fas fa-trash"></i>
                </b-button>
            </template>
            </b-table>
        </div>
    </div>
  </section>
</template>

<script>
  import Swal from 'sweetalert2'
  import {validationMixin} from 'vuelidate'
  import {required, minLength, maxValue, helpers} from 'vuelidate/lib/validators'
  import {text, nombreText, letterOrWord} from '../../../utils/expresiones'
  export default {
    props:['perspectivas', 'idProceso'],
    data() {
      return {
        dataP:[],
        name:'',
        edit: false,
        editId: null,
        fields: [
         { key: 'id', label: 'id'},
         { key: 'value', label: 'Perspectiva'},
         { key: 'actions', label: 'Opciones' }
        ]
      }
    },
    validations:{
        name:{
          text,
          minLength: minLength(3),
          repeat(value){
            let {dataP} = this
            return dataP.find(e => e.value === value) ? false : true;
          }
        }
    },
    methods: {
      onSubmit(){
         if(this.edit)
            this.update()
         else
            this.store()
      },
      store(){
          let { name, idProceso, perspectivas, loadData, dataP} = this
          let dataJson  = JSON.stringify([...perspectivas, name])
          dataP.splice(0, dataP.length)
          axios.post(`/api/perspectiva`, {data: dataJson, idProceso}).then(({data}) => {
              let {error, message} = data
              if(error)
                Swal.fire('Error!!', message, 'error')
              else{
                loadData(dataP, JSON.parse(dataJson))
                name = ''
                Swal.fire('Exito!!', 'Registro exitoso', 'success')
              }
          });
      },
      update(){
        let { name, idProceso, perspectivas, loadData, dataP, editId, edit} = this
        perspectivas.splice(editId, 1, name)
        dataP.splice(0, dataP.length)

        axios.post(`/api/perspectiva`, {data: JSON.stringify(perspectivas), idProceso}).then(({data}) => {
            let {error, message} = data
            if(error)
                Swal.fire('Error!!', message, 'error')
            else{
                loadData(dataP, perspectivas)
                this.name = ''
                this.edit = false
                this.editId = null
                Swal.fire('Exito!!', 'Actualización exitosa', 'success')
            }
        })
      },
      deleteObj(id){
        let { idProceso, perspectivas, loadData, dataP} = this
        perspectivas.splice(id, 1)
        dataP.splice(0, dataP.length)

        axios.post(`/api/perspectiva`, {data: JSON.stringify(perspectivas), idProceso}).then(({data}) => {
            let {error, message} = data
            if(error)
                Swal.fire('Error!!', message, 'error')
            else{
                loadData(dataP, perspectivas)
                Swal.fire('Exito!!', 'Se eliminó correctamente', 'success')
            }
        })
      },
      loadData(dataP, perspectivas){
        for (let index = perspectivas.length -1 ; index >= 0; index--) {
            dataP.push({id:index, value: perspectivas[index]}) 
        }
      },
      editaObjetivo({id, value}){
        this.name = value
        this.edit = true
        this.editId = id
      }
    },
    mounted(){
        let { dataP, perspectivas, loadData} = this
        loadData(dataP, perspectivas); 
    }
  }
</script>
