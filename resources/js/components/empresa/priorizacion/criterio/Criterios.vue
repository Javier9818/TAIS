<template>
  <div>
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col-6">
                <b-form-group id="inputNombre" label="Nombre" label-for="nombre">
                    <b-form-input id="nombre" v-model="form.nombre" type="text" required placeholder="Ingrese el nombre de la escala"></b-form-input>
                    <p v-if="$v.form.nombre.$error" class="help text-danger">Este campo es inválido</p>
                </b-form-group>
            </div>
            <div class="col-6">
                <b-form-group id="inPutpeso" label="Peso" label-for="peso">
                    <b-form-input id="peso" v-model="form.peso" type="number" required placeholder="Ingrese el peso de la escala"></b-form-input>
                    <!-- <p v-if="$v.form.nombre.$error" class="help text-danger">Este campo es inválido</p> -->
                </b-form-group>
            </div>
            <div class="col-6">
                <b-form-group id="inputDescripcion" label="Descripcion" label-for="descripcion">
                    <b-form-input id="descripcion" v-model="form.descripcion" type="text" required placeholder="Ingrese la descripcion de la escala"></b-form-input>
                    <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
                </b-form-group>
            </div>
            <div class="col-6"><button type="submit" class="btn btn-sm btn-success mt-5"> {{ criterioSelected ? 'Actualizar' : 'Registrar'}}</button></div>
        </div>
    </form>
    <button class="btn btn-sm btn-danger float-right mb-1" @click="resetCriterios()" v-if="criterioSelected">Nuevo</button>
    <b-table
        show-empty
        small
        stacked="md"
        :items="criterios"
        :fields="fieldsCriterios"
        :filter="filter"
        :filterIncludedFields="filterOn"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :sort-direction="sortDirection"
        >

        <template v-slot:cell(nombre)="row">
            <a href="javascript::void(0)" @click=" $emit('criterio-selected', row.item.id)" >{{row.item.nombre}}</a>
        </template>

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
import {text, nombreText} from '../../../utils/expresiones'
  export default {
    mixins: [validationMixin],
    data() {
      return {
        criterios:[],
        criterioSelected: null,
        fieldsCriterios: [
         { key: 'nombre', label: 'Criterio', sortable: true },
         { key: 'peso', label: 'Peso', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ],
        sortBy: '',
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        filterOn: [],
        form:{
            nombre: '',
            peso:0,
            descripcion:'',
        }
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
        axios.post(`/api/criterio/${unidad}`, this.form).then( ({data}) => {
            this.criterios.push(data.criterio);
            this.resetCriterios();
            Swal.fire('Exito!!', 'Registro exitoso', 'success');
        }).catch(()=>{
            Swal.fire('Error', 'Ha ocurrido un error', 'error');
        });
      },
      update(){
        axios.put(`/api/criterio`, this.form).then( ({data}) => {
            let {criterio} = data;
            this.criterios.map( (item) => {
                if(item.id === this.form.id) {
                    item.nombre = this.form.nombre
                    item.descripcion = this.form.descripcion
                    item.peso = this.form.peso
                };
            });
             this.resetCriterios();
             Swal.fire('Exito!!', 'Registro exitoso', 'success');
        }).catch((error)=>{
            Swal.fire('Error', error, 'error');
        });;
      },
      resetCriterios(){
          this.form = {
            nombre: '',
            peso:0,
            descripcion:'',
          }
          this.criterioSelected = null
      },
      infoCriterio(data){
          this.criterioSelected = data.id;
          this.form = {...data};
      },
      deleteCriterio(id){
          alert(id)
      }
    },
    mounted(){
        axios.get(`/api/criterio/${unidad}`).then(({data}) => {
            this.criterios = data.criterios;
        });
    }
  }
</script>
