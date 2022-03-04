<template>
  <div>
    <form @submit.prevent="onSubmit" id="myform">
        <div class="row">
            <div class="col-5">
                <b-form-group id="inputNombre" label="Grado de implementación" label-for="cbGrado">
                    <select name="cbGrado" id="cbGrado" v-model="form.nombre" class="form-control" required>
                        <option :value="null" disabled selected> -- Seleccione una opción --</option>
                        <option value="Inexistente">Inexistente</option>
                        <option value="En desarrollo">En desarrollo</option>
                        <option value="Completo">Completo</option>
                    </select>
                </b-form-group>
            </div>
            <div class="col-7">
                <b-form-group id="inputDescripcion" label="Descripcion" label-for="descripcion">
                    <b-form-input id="descripcion" v-model="form.descripcion" type="text" required placeholder="Ingrese la descripcion de la iniciativa"></b-form-input>
                    <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
                </b-form-group>
            </div>
        </div>
    </form>
    <button type="submit" class="btn btn-sm btn-success float-right mb-1" form="myform" > {{ criterioSelected ? 'Actualizar' : 'Registrar'}}</button>
    <button class="btn btn-sm btn-danger float-right mb-1 mx-1" @click="resetCriterios()" v-if="criterioSelected" >Nuevo</button>
    <b-table
        show-empty
        small
        stacked="md"
        :items="iniciativas"
        :fields="fieldsIniciativas"
        >
        <template v-slot:cell(actions)="row">
            <b-button size="sm" @click="infoCriterio(row.item)" class="mr-1 btn-success">
                <i class="fas fa-pencil-alt"></i>
            </b-button>
            <b-button size="sm" @click="deleteCriterio(row.item.id)" class="btn-danger">
                <i class="fas fa-trash-alt"></i>
            </b-button>
        </template>
    </b-table>
  </div>
</template>

<script>
import Swal from 'sweetalert2'
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
import {text, nombreText} from '../../utils/expresiones'
  export default {
    mixins: [validationMixin],
    props:['item'],
    data() {
      return {
        iniciativas:[],
        criterioSelected: null,
        fieldsIniciativas: [
         { key: 'nombre', label: 'Iniciativa' },
         { key: 'descripcion', label: 'Descripción' },
         { key: 'created_at', label: 'Fecha de creación' },
         { key: 'actions', label: 'Opciones' }
        ],
        sortBy: '',
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        filterOn: [],
        form:{
            nombre: null,
            descripcion:'',
        },
        version: 1
      }
    },
    validations:{
          form:{
            descripcion:{
              text
            },
            nombre:{
              nombreText
            }
          }
    },
    methods: {
      onSubmit(evt){
        evt.preventDefault();
        this.$v.$touch()
        if(!this.$v.$invalid)
            this.criterioSelected ? this.update() : this.store();
      },
      store(){
        axios.post(`/api/grado-implementacion/${this.item.id}`, this.form).then( ({data}) => {
            this.iniciativas.unshift(data.grado);
            this.resetCriterios();
            Swal.fire('Exito!!', 'Registro exitoso', 'success');
        }).catch(()=>{
            Swal.fire('Error', 'Ha ocurrido un error', 'error');
        });
      },
      update(){
        axios.put(`/api/grado-implementacion`, this.form).then( ({data}) => {
            let {grado} = data;
            this.iniciativas.map( (item) => {
                if(item.id === this.form.id) {
                    item.nombre = this.form.nombre
                    item.descripcion = this.form.descripcion
                };
            });
             this.resetCriterios();
             Swal.fire('Exito!!', 'Actualización exitosa', 'success');
        }).catch((error)=>{
            Swal.fire('Error', error, 'error');
        });;
      },
      resetCriterios(){
          this.form = {
            nombre: null,
            descripcion:'',
          }
          this.criterioSelected = null
      },
      infoCriterio(data){
          this.criterioSelected = data.id;
          this.form = {...data};
      },
      deleteCriterio(id){
        axios.delete(`/api/grado-implementacion/${id}`).then(({data}) => {
            let {error, message} = data
            if(error === true){
              Swal.fire('Error!', message, 'error')
            }
            else{
              this.loadIniciativas()
              Swal.fire('Exito!', 'Eliminación exitosa', 'success')
            }

        });
      },
      loadIniciativas(){
        axios.get(`/api/grado-implementacion/${this.item.id}`).then(({data}) => {
            this.iniciativas = data.grados;
        });
      }
    },
    mounted(){
        this.loadIniciativas()
        this.version = version || 1
    }
  }
</script>
