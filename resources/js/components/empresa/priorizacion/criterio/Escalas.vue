<template>
  <div v-if="criterio">
    <form @submit.prevent="onSubmit">
        <div class="row">
            <div class="col-6">
                <b-form-group id="inputDes" label="Descripcion" label-for="descripcion">
                    <b-form-input id="descripcion" v-model="form.descripcion" type="text" required placeholder="Ingrese la descripcion de la escala"></b-form-input>
                    <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
                </b-form-group>
            </div>
            <div class="col-6">
                <b-form-group id="inPunt" label="Puntaje" label-for="puntaje">
                    <b-form-input id="puntaje" v-model="form.puntaje" type="number" required placeholder="Ingrese el puntaje"></b-form-input>
                    <!-- <p v-if="$v.form.nombre.$error" class="help text-danger">Este campo es inválido</p> -->
                </b-form-group>
            </div>
            <div class="col-6"><button type="submit" class="btn btn-sm btn-success mb-2"> {{ escalaSelected ? 'Actualizar' : 'Registrar'}}</button></div>
        </div>
    </form>
    <button class="btn btn-sm btn-danger float-right mb-1" @click="resetCriterios()" v-if="escalaSelected">Nuevo</button>
    <b-table
        show-empty
        small
        stacked="md"
        :items="escalas"
        :fields="fieldsEscalas"
        :filter="filter"
        :filterIncludedFields="filterOn"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :sort-direction="sortDirection"
        >

        <template v-slot:cell(nombre)="row">
            <a href="javascript::void(0)" @click="listSub(row.item.id, row.item.nombre)" >{{row.item.nombre}}</a>
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
      props:['criterio'],
    data() {
      return {
        escalas:[],
        escalaSelected: null,
        fieldsEscalas: [
         { key: 'descripcion', label: 'Escala', sortable: true },
         { key: 'puntaje', label: 'Puntaje', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ],
        sortBy: '',
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        filterOn: [],
        form:{
            descripcion: '',
            puntaje:0
        }
      }
    },
    validations:{
          form:{
            descripcion:{
              text
            }
          }
    },
    methods: {
      onSubmit(evt){
        evt.preventDefault();
        this.$v.$touch()
        if(!this.$v.$invalid)
            this.escalaSelected ? this.update() : this.store();
      },
      store(){
        axios.post(`/api/escala`, {...this.form, criterio: this.criterio}).then( ({data}) => {
            this.escalas.push(data.escala);
            this.resetCriterios();
            Swal.fire('Exito!!', 'Registro exitoso', 'success');
        }).catch((error)=>{
            Swal.fire('Error', 'No se puede registrar dos escalas con el mismo puntaje', 'error');
        });
      },
      update(){
        axios.put(`/api/escala`, this.form).then( ({data}) => {
            this.escalas.map( (item) => {
                if(item.id === this.form.id) {
                    item.descripcion = this.form.descripcion
                    item.puntaje = this.form.puntaje
                };
            });
             this.resetCriterios();
             Swal.fire('Exito!!', 'Registro exitoso', 'success');
        }).catch((error)=>{
           Swal.fire('Error', 'No se puede registrar dos escalas con el mismo puntaje', 'error');
        });
      },
      resetCriterios(){
          this.form = {
            puntaje:0,
            descripcion:'',
          }
          this.escalaSelected = null
      },
      infoCriterio(data){
          this.escalaSelected = data.id;
          this.form = {...data};
      },
      deleteCriterio(id){
          alert(id)
      }
    },
    mounted(){},
    watch:{
        criterio(id){
            axios.get(`/api/escala/${id}`).then(({data}) => {
                this.escalas = data.escalas;
            });
            this.resetCriterios();
        }
    }
  }
</script>
