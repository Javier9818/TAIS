<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
        <div class="row">
            <div class="col-md-6">
                <label class="mt-2">Cliente</label>
                <b-overlay :show="loading_clientes" v-if="dataForm.mode === 'create'">
                  <b-form-select 
                      v-model="form.cliente" 
                      :options="clientes"
                      value-field="id"
                      text-field="nombre"
                      class="mt-1"
                      required
                      >
                  <template v-slot:first>
                      <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
                  </template>
                  </b-form-select>
                </b-overlay>
                <h4 v-else>{{dataForm.content.cliente}}</h4>
            </div>

            <div class="col-md-6">
                <label>Nivel <button class="btn btn-danger btn-sm text-white ml-2 mb-1" @click="addLevel()" type="button">+</button> </label>
                <b-overlay :show="loading_levels" class="d-inline-block">  
                  <b-form-select 
                  v-model="form.nivel" 
                  :options="niveles"
                  value-field="numero"
                  text-field="numero"
                  @change="loadClientesPadre"
                  required
                  >
                  <template v-slot:first>
                      <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
                  </template>
                  </b-form-select>
                </b-overlay>
            </div>
        </div>

      <hr>

      <b-form-group id="input-group-1" label="Es cliente de" label-for="cliente_two" class="col-md-6">
        <b-overlay :show="loading_clientes_padre" class="d-inline-block">
          <b-form-select 
              v-model="form.cliente_padre" 
              :options="clientesPadre"
              value-field="id"
              text-field="nombre"
              required
          >
          <template v-slot:first>
              <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
          </template>
          </b-form-select>
        </b-overlay>
      </b-form-group>



      <b-button type="submit" variant="primary">{{dataForm.mode === 'create' ? 'Registrar' : 'Actualizar'}}</b-button>
      <b-button variant="danger" @click="$emit('click')">Cancelar</b-button>
    </b-form>
    </b-overlay>
  </div>
</template>

<script>

import Swal from 'sweetalert2'
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
const text = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ.\u00f1\u00d1\s]*$/)
const nombreText = helpers.regex('alpha', /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/)
  export default {
    mixins: [validationMixin],
    props:['dataForm'],
    data() {
      return {
        empresa,
        loading:false,
        loading_clientes: false,
        loading_levels: false,
        loading_clientes_padre:false,
        unidad_negocio: null,
        options:[],
        clientes:[],
        clientesPadre:[],
        niveles:[],
        form:{
          cliente: null,
          nivel: null,
          cliente_padre: null
        }
      }
    },
    mounted(){
        let {content, mode} = this.dataForm;
        this.loadLevels(content.unidad);
        if(mode === 'edit'){
          this.loadDataForm(content);
        }else{
           this.loadClientes(content.unidad, false);
        }
    },
    methods: {
      loadDataForm: async function (content){
        this.form = {
            cliente: content.clienteId,
            nivel: content.nivel,
            cliente_padre: content.cliente_padre,
            unidad: content.unidad
          }
          this.loading = true;
          await this.loadClientesPadre(content.nivel);
          this.loading = false;
      },
      addLevel: function(){
        let {content} = this.dataForm;
        this.loading_levels = true
        axios.post(`/api/niveles-clientes-cadena/${content.unidad}`).then( ({data}) => {
              this.niveles.push(data.nivel);
              Swal.fire('Registro exitoso!', 'Se agregó un nuevo nivel', 'success').then( () => {
                this.loading_levels = false
              });
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          });
      },
      loadClientesPadre: async function(id){
        this.loading_clientes_padre = true;
        let {content, mode} = this.dataForm;
        if(id === 1) {
          this.clientesPadre = [{id:'d', nombre: `Empresa: ${empresa.nombre}`}];
          this.form.cliente_padre = 'd';
          this.loading_clientes_padre = false;
        }else{
          await axios.get(`/api/clientes-padre-cadena/${content.unidad}/${id - 1}`).then( ({data}) => {
              this.clientesPadre = data.clientes;
              if(mode === 'edit'){
                if(id === content.nivel) this.form.cliente_padre = content.cliente_padre;
                else this.form.cliente_padre = null;
                
                if(id - 1 === content.nivel) //Para evitar que el mismo cliente se autoasigne
                  this.clientesPadre.map( (c, i) => {
                        if(c.id === content.clienteId) this.clientesPadre.splice(i, 1)
                  });
              }else
                this.form.cliente_padre = null;
              
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally(this.loading_clientes_padre = false);
        }
      },
      loadClientes: function(unidad_negocio, comodin){
          this.loading_clientes = true;
          axios.get(`/api/clientes-cadena-libres/${unidad_negocio}/${comodin}`).then( ({data}) => {
              this.clientes = data.clientes;
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally(this.loading_clientes = false);
      },
      loadLevels: function(unidad_negocio){
          this.loading_levels = true
          axios.get(`/api/niveles-clientes-cadena/${unidad_negocio}`).then( ({data}) => {
              this.niveles = data.niveles;
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally(this.loading_levels = false);
      },
      onSubmit(evt) {
        this.loading = true;
        let {content, mode} = this.dataForm;
        if(mode === 'create') this.store(content.unidad);
        else if(mode === 'edit') this.update();
        
      },
      store(unidad_negocio){
        axios.post(`/api/cadena_clientes/${unidad_negocio}`, this.form).then( ({data}) => {
            this.$emit('click');
            let cliente_cadena = data.cliente, cliente;
            this.clientes.forEach((c) => {
              if (c.id === cliente_cadena.cliente_id) cliente = c;
            });
            this.loading = false;
            this.$emit('store', {
              cliente: cliente.nombre,
              clienteId: this.form.cliente,
              nivel: this.form.nivel,
              cliente_padre: this.form.cliente_padre
            }); //DATA DEL CLIENTE AGREGADO
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        }).catch( () => {
          Swal.fire('Error', 'Ha sucedido un error', 'error');
        });
      },
      update(){
        axios.put(`/api/cadena_clientes`, this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('update', true);
            Swal.fire('Éxito', 'Se han actualizado los cambios', 'success');
        });
      },
      onReset(evt) {
        evt.preventDefault()
        // Reset our form values
        this.form.email = ''
        this.form.name = ''
        this.form.food = null
        this.form.checked = []
        // Trick to reset/clear native browser form validation state
        this.show = false
        this.$nextTick(() => {
          this.show = true
        })
      }
    }
  }
</script>
