<template>
  <section>
      <div class="row">
        <div class="col-12">
            <form @submit.prevent="onSubmit">
                <b-form-group label="Variable" label-for="nombre">
                    <input type="text" id="nombre" v-model="variable"  placeholder="Símbolo de variable" required>
                    <input type="text" id="nombre" v-model="nombreVar" placeholder="Nombre de variable" required>
                    <button class="btn btn-sm btn-primary" type="submit" v-if="!edit" :disabled="$v.$invalid">Insertar</button>
                    <button class="btn btn-sm btn-primary" type="submit" v-if="edit" :disabled="$v.$invalid">Actualizar</button>
                    <button class="btn btn-sm btn-danger" type="submit" v-if="edit" @click="edit = false; nombreVar=''; variable=''; editId=null" :disabled="$v.$invalid">Nuevo</button>
                </b-form-group>
                
                <p v-if="!$v.nombreVar.repeat" class="help text-danger">Nombre de variable ya registrado</p>
                <p v-else-if="!$v.variable.repeat" class="help text-danger">Variable ya registrada</p>
                <p v-else-if="!$v.nombreVar.text || !$v.variable.letterOrWord" class="help text-danger">No se aceptan caracteres especiales</p>
                <!-- <p v-else-if="!$v.name.minLength" class="help text-danger">Este campo es inválido</p> -->
                <!-- <p v-else-if="!$v.name.text" class="help text-danger">Este campo es inválido</p> -->
            </form>
        </div>
        <div class="col-12">
            <b-table
            show-empty
            small
            stacked="md"
            :items="variablesCopy"
            :fields="fields"
            >
            <template v-slot:cell(actions)="row">
                <b-button size="sm" @click="editaObjetivo(row.item, row.index)" class="mr-1 btn-success">
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
    props:['variables', 'idProceso', 'indicadores'],
    data() {
      return {
        indexEdit:null,
        variablesCopy:[],
        variable:'',
        nombreVar:'',
        edit: false,
        editId: null,
        fields: [
         { key: 'variable', label: 'Variable'},
         { key: 'nombreVar', label: 'Nombre'},
         { key: 'actions', label: 'Opciones' }
        ]
      }
    },
    validations:{
        nombreVar:{
          text,
          minLength: minLength(3),
          repeat(value){
            let {variables, edit} = this
            return  edit ? true: (variables.find(e => e.nombreVar === value) ? false : true);
          }
        },
        variable:{
           letterOrWord,
           repeat(value){
            let {variables, edit} = this
            return  edit ? true: (variables.find(e => e.variable === value) ? false : true);
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
          let { nombreVar, variable, idProceso, variablesCopy} = this
          let dataJson  = JSON.stringify([...variablesCopy, {nombreVar, variable}])
          axios.post(`/api/variables`, {data: dataJson, idProceso}).then(({data}) => {
              let {error, message} = data
              if(error)
                Swal.fire('Error!!', message, 'error')
              else{
                variablesCopy.push({nombreVar, variable})
                Swal.fire('Exito!!', 'Registro exitoso', 'success').then(() => {
                    this.nombreVar = ''
                    this.variable = ''
                })
              }
          });
      },
      update(){
        let { nombreVar, variable, idProceso, variablesCopy , indexEdit} = this
        this.middleware(variable)
        let variables = variablesCopy.map((e,i) => { return i === indexEdit ? {nombreVar, variable} : e})
        this.variablesCopy = variables
        axios.post(`/api/variables`, {data: JSON.stringify(variables), idProceso}).then(({data}) => {
              let {error, message} = data
              if(error)
                Swal.fire('Error!!', message, 'error')
              else{
                Swal.fire('Exito!!', 'Registro exitoso', 'success').then(() => {
                    this.nombreVar = ''
                    this.variable = ''
                    this.indexEdit = null
                })
              }
          });
      },
      deleteObj(id){
        Swal.fire('Error', 'No es posible eliminar', 'error')
      },
      middleware(variable){
        let { indicadores } = this
        indicadores.forEach(e => {
          e.listVar.forEach((d) => {
            if (d.variable === variable) 
             return true
          })
        })
      },
      loadData(dataP, perspectivas){
        for (let index = perspectivas.length -1 ; index >= 0; index--) {
            dataP.push({id:index, value: perspectivas[index]}) 
        }
      },
      editaObjetivo({variable, nombreVar}, index){
        this.variable = variable
        this.nombreVar = nombreVar
        this.edit = true
        this.indexEdit = index
      }
    },
    mounted(){
        this.variablesCopy = this.variables
        console.log(this.indicadores)
    },
    watch:{
        variablesCopy(newValue){
            this.$emit('handleVars', newValue)
        }
    }
  }
</script>
