<template>
  <section style="width: 100%">
      <selector-proceso @changue-process='setProcess'></selector-proceso>
        <div class="row justify-content-around">
          <div class="col-md-4">
              <select-version @chagueVersion='handleVersion'></select-version>
          </div><div class="col-md-4"></div>
        </div>


        <div class="container" v-if="process">
          <b-button class="btn btn-success btn-sm float-right mb-2" @click="openModal" v-if="version == null">Agregar</b-button>
          <b-table
              show-empty
              small
              stacked="md"
              :items="items"
              :fields="fields"
            >
            <template v-slot:cell(id)="row">
                  {{row.index +1}}
            </template>

            <template v-slot:cell(flujo)="row">
                  <img :src="`/assets/img/${row.item.flujo}.png`" alt="">
            </template>

              <template v-slot:cell(actions)="row"  v-if="version == null">
                  <b-button size="sm" @click="info(row.item, row.index)" class="mr-1">
                      Editar
                  </b-button>
                  <b-button size="sm" @click="  desactivate(row.item.id, row.index)" class="btn-danger" title="Eliminar">
                    <i class="fas fa-trash-alt"></i>
                  </b-button>
              </template>
          </b-table>

        <div class="row justify-content-around">
           <table class="table col-4">
            <thead>
              <tr>
                <th scope="col">Rol</th>
                <th scope="col">Tiempo (minutos)</th>
              </tr>
            </thead>
            <tbody>
              <tr v-for=" rol in resumenRoles" :key="rol.id">
                <td v-if="rol.cant > 0">{{rol.descripcion}}</td>
                <td v-if="rol.cant > 0">{{rol.cant}} min</td>
              </tr>
            </tbody>
          </table>

          <table class="table col-4">
            <thead>
              <tr>
                <th scope="col">Operación</th>
                <th scope="col">Tiempo (minutos)</th>
              </tr>
            </thead>
            <tbody>
               <tr v-for=" rol in resumenOperaciones" :key="rol.id">
                <td>{{rol.nombre}}</td>
                <td>{{rol.cant}} min</td>
              </tr>
            </tbody>
          </table>
        </div>

            <b-modal id="modal-1" title="Nueva actividad" size="xl" hide-footer>
              <b-overlay :show="loadModal">
                  <form @submit.prevent="onSubmit">
                    <div class="row">
                      <div class="col-md-6">
                        <b-form-group label="Actividad">
                            <b-input type="text" v-model="form.actividad" required></b-input>
                            <p v-if="!$v.form.actividad.nameObjects" class="help text-danger">Este no puede contener caracteres especiales</p>
                        </b-form-group>
                      </div>
                      <div class="col-md-6">
                        <label>Roles <a href="javascript:void(0)" @click="()=>{newRol = !newRol; form.rol_new=null; form.rol = null;}">{{newRol? 'Cancelar':'Nuevo'}}</a></label>
                        <b-input type="text" v-model="form.rol_new" v-if="newRol" required></b-input>
                        <b-form-select 
                            v-model="form.rol" 
                            :options="roles"
                            value-field="id"
                            text-field="descripcion"
                            required
                            v-else
                          >
                          <template v-slot:first>
                              <b-form-select-option :value="null">-- Porfavor, seleccione una opción --</b-form-select-option>
                          </template>
                        </b-form-select>
                      </div>
                      <div class="col-md-6">
                          <b-form-group label="Tiempo (minutos)">
                            <b-input type="text" v-model="form.tiempo" required></b-input>
                            <p v-if="!$v.form.tiempo.decimalPositive" class="help text-danger">Este campo debe ser numérico</p>
                          </b-form-group>
                      </div>
                      <div class="col-md-6">
                          <b-form-group label="Flujo">
                            <b-form-select 
                                  v-model="form.flujo" 
                                  :options="flujos"
                                  value-field="id"
                                  text-field="nombre"
                                  required
                                >
                                <template v-slot:first>
                                    <b-form-select-option :value="null">-- Porfavor, seleccione una opción --</b-form-select-option>
                                </template>
                              </b-form-select>
                          </b-form-group>
                      </div>
                    </div>
                    <button class="btn btn-success btn-sm" type="submit" :disabled="(!$v.form.tiempo.decimalPositive || !$v.form.actividad.nameObjects)">{{edit ? 'Actualizar' : 'Registrar'}}</button>
                  </form>
              </b-overlay>
            </b-modal>
        </div>
  </section>
</template>

<script>
import Swal from 'sweetalert2';
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
import {nameObjects, decimalPositive} from '../../../utils/expresiones'
  export default {
    mixins: [validationMixin],
    data() {
      return {
       process:null,
       version:null,
       versiones: [],
       items: [],
       roles:[],
       resumenOperaciones:[
          {id:1, nombre: 'Operación', cant: 0},
          {id:2, nombre: 'Transporte', cant: 0},
          {id:3, nombre: 'Inspección', cant: 0},
          {id:4, nombre: 'Demora', cant: 0},
          {id:5, nombre: 'Almacenaje', cant: 0},
          {id:6, nombre: 'Actividad-Combinada', cant: 0},
       ],
       resumenRoles:[],
       edit:false,
       newRol:false,
       loadModal:false,
       flujos:[
         {id:1, nombre: 'Operación'},
         {id:2, nombre: 'Transporte'},
         {id:3, nombre: 'Inspección'},
         {id:4, nombre: 'Demora'},
         {id:5, nombre: 'Almacenaje'},
         {id:6, nombre: 'Actividad-Combinada'},
       ],
        fields: [
         { key: 'id', label: 'Nro', sortable: true },
         { key: 'actividad', label: 'Actividad', sortable: true, class: 'text-center' },
         { key: 'rol_new', label: 'Rol', sortable: true },
         { key: 'flujo', label: 'Flujo' },
         { key: 'tiempo', label: 'Tiempo (minutos)' },
         { key: 'actions', label: 'Opciones' }
        ],
        form:{
          actividad:'',
          rol:null,
          tiempo:0,
          flujo:null,
          rol_new:null,
        }
      }
    },
    validations:{
      form:{
        actividad:{
          nameObjects
        },
        tiempo:{
          decimalPositive
        }
      }
    },
    methods: {
     setProcess(id){
       this.resetResumen()
       this.process = id
       this.loadSeguimiento(id, this.version)
     },
     loadSeguimiento(id, version){
      axios.get(`/api/seguimiento/${id}/${version}`).then(({data}) => {
          this.items = data.seguimiento;
          this.loadResumen(data.seguimiento)
       });
     },
     onSubmit(){
       this.loadModal = true;
       this.resetResumen()
        axios.post('/api/seguimiento', {...this.form, proceso: this.process, unidad}).then(({data}) => {
            if(this.edit){
                this.items.splice(this.form.index, 1, data.seguimiento);
            }else{
                this.items.push(data.seguimiento)
            }
        }).then(()=>{
          this.loadModal = false;
          this.form = {
              actividad:'',
              rol:null,
              tiempo:0,
              flujo:null,
              rol_new:null,
            }
            this.$root.$emit('bv::hide::modal', 'modal-1')
            this.loadResumen(this.items)
        })
     },
     openModal(){
       this.loadRoles()
       this.$root.$emit('bv::show::modal', 'modal-1')
       this.edit = false;
     },
     loadRoles(){
      axios.get(`/api/rol/${unidad}`).then(({data}) => {
          this.roles = data.roles; 
       });
     },
     info(item, index){
      this.form = {...item, index}
      this.loadRoles()
      this.$root.$emit('bv::show::modal', 'modal-1')
      this.edit = true;
     },
     desactivate(id, index){
       axios.delete(`/api/seguimiento/${id}`).then(()=>{
         this.items.splice(index,1);
         Swal.fire('Exito!', 'Se eliminó correctamente', 'success')
       });
     },
     loadResumen(data){
      data.forEach(i => {
        this.resumenOperaciones.splice(i.flujo-1,1,{...this.resumenOperaciones[i.flujo-1], cant:this.resumenOperaciones[i.flujo-1].cant + parseFloat(i.tiempo)})
        let index = this.resumenRoles.findIndex( rol => rol.id === parseInt(i.rol) );
        if(index !== -1)
          this.resumenRoles.splice(index, 1, {...this.resumenRoles[index], cant: this.resumenRoles[index].cant + parseFloat(i.tiempo)})
      });
     },
     resetResumen(){
       this.resumenRoles = []
        this.roles.forEach(i=>{
            this.resumenRoles.push({
              ...i,
              cant:0
            })
          })
        this.resumenOperaciones=[
          {id:1, nombre: 'Operación', cant: 0},
          {id:2, nombre: 'Transporte', cant: 0},
          {id:3, nombre: 'Inspección', cant: 0},
          {id:4, nombre: 'Demora', cant: 0},
          {id:5, nombre: 'Almacenaje', cant: 0},
          {id:6, nombre: 'Actividad-Combinada', cant: 0},
       ]
     },
     handleVersion(obj){
       this.resetResumen()
       this.version = obj ? obj.id : null;
       this.loadSeguimiento(this.process, obj ? obj.id : null)
     }
    },
    mounted(){
      this.loadRoles()
    }
  }
</script>
