<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
        <div class="row">
            <div class="col-md-6">
                <label class="mt-2">Proveedor</label>
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
                <label>Nivel <button  class="btn btn-danger btn-sm text-white ml-2 mb-1" @click="addLevel()" type="button">+</button> </label>
                <br>
                <b-overlay :show="loading_levels" class="d-inline-block">  
                  <b-form-select 
                  v-model="form.nivel" 
                  :options="niveles"
                  value-field="numero"
                  text-field="numero"
                  @change="loadProveedoresPadre"
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

      <b-form-group id="input-group-1" label="Provee a" label-for="cliente_two" class="col-md-6">
        <input type="checkbox" @change="comodin()" v-model="comodin_value">
        <b-overlay :show="loading_clientes_padre" class="d-inline-block">
          <multiselect 
            v-model="form.cliente_padres" 
            tag-placeholder="Add this as new tag" 
            placeholder="Search or add a tag" 
            label="nombre" 
            track-by="id" 
            :options="clientesPadre" 
            :multiple="true" 
            :taggable="true" 
            @tag="addTag"
          ></multiselect>
        </b-overlay>
      </b-form-group>

      <b-button type="submit" variant="primary">{{dataForm.mode === 'create' ? 'Registrar' : 'Actualizar'}}</b-button>
      <b-button variant="danger" @click="$emit('click')">Cancelar</b-button>
    </b-form>
    </b-overlay>
  </div>
</template>

<script>
import Multiselect from 'vue-multiselect'
import Swal from 'sweetalert2'
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
const text = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ.\u00f1\u00d1\s]*$/)
const nombreText = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ\u00f1\u00d1\s]*$/)
  export default {
     components: {
      Multiselect
    },
    mixins: [validationMixin],
    props:['dataForm'],
    data() {
      return {
        empresa,
        comodin_value: false,
        blockLevel: false,
        loading:false,
        loading_clientes: false,
        loading_levels: false,
        loading_clientes_padre:false,
        unidad_negocio: null,
        options:[],
        clientes:[],
        clientesPadre:[],
        clientesPadreCopy:[],
        niveles:[],
        form:{
          cliente: null,
          nivel: null,
          cliente_padre: null,
          cliente_padres:[]
        }
      }
    },
    mounted(){
        let {content, mode} = this.dataForm;
        this.loadLevels(content.unidad);
        if(mode === 'edit'){
          this.loadDataForm(content);
        }else{
           this.loadProveedores(content.unidad, false);
        }
    },
    methods: {
      comodin: function(){
        this.loading_clientes_padre = true;
        let {nivel} = this.form;
        let {content} = this.dataForm;
        this.clientesPadreCopy = [...this.clientesPadre];
        if(this.comodin_value && nivel !== null)
          axios.get(`/api/proveedores-padre-comodin/${content.unidad}/${nivel-1}`).then( ({data}) => {
            this.clientesPadre.push(...data.proveedores);
          }).then( () => {
            this.clientesPadre.push({nombre: empresa.nombre, id: null});
          }).finally( () => {
             this.loading_clientes_padre = false;
          });
        else{
           this.clientesPadre = [...this.clientesPadreCopy];
           this.loading_clientes_padre = false;
        }
         
      },
      verifyCliente: function(){
        this.loading_levels = true;
        let {content} = this.dataForm;
        axios.get(`/api/verifyProveedor/${content.unidad}/${this.form.cliente}`).then(({data}) => {
          if(data.exists){
            this.niveles = [{numero: data.nivel}];
            this.form.nivel = null;
            this.blockLevel = true;
          }else{
            this.blockLevel = false;
            this.loadLevels(content.unidad);
          }
           this.loading_levels = false;
        });
      },
      loadDataForm: async function (content){
        this.form = {
            cliente: content.clienteId,
            nivel: content.nivel,
            cliente_padre: content.proveedor_padre,
            unidad: content.unidad,
            cliente_padres:content.proveedores_padre //e
          }
          this.loading = true;
          await this.loadProveedoresPadre(content.nivel);
          this.loading = false;
      },
      addLevel(){
        let {content} = this.dataForm;
        this.loading_levels = true
        axios.post(`/api/niveles-proveedores-cadena/${content.unidad}`).then( ({data}) => {
              this.niveles.push(data.nivel);
              Swal.fire('Registro exitoso!', 'Se agregó un nuevo nivel', 'success').then( () => {
                this.loading_levels = false
              });
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          });
      },
      loadProveedoresPadre: async function(id){
        this.loading_clientes_padre = true;
        let {content, mode} = this.dataForm;
        if(id === 1) {
          this.clientesPadre = [{id:'d', nombre: `Empresa: ${empresa.nombre}`}];
          this.form.cliente_padres = [{id:'d', nombre: `Empresa: ${empresa.nombre}`}];
          this.loading_clientes_padre = false;
        }else{
          await axios.get(`/api/proveedores-padre-cadena/${content.unidad}/${id - 1}`).then( ({data}) => {
              this.clientesPadre = data.proveedores;
              if(mode === 'edit'){
                if(id === content.nivel) this.form.cliente_padre = content.proveedor_padre;
                else this.form.cliente_padre = null;
                
                if(id - 1 === content.nivel) //Para evitar que el mismo cliente se autoasigne
                  this.clientesPadre.map( (c, i) => {
                        if(c.id === content.clienteId) this.clientesPadre.splice(i, 1)
                  });
              }else
                this.form.cliente_padres = [];
              
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally(this.loading_clientes_padre = false);
        }
      },
      loadProveedores(unidad_negocio, comodin){
          this.loading_clientes = true;
          axios.get(`/api/proveedores-cadena-libres/${unidad_negocio}/${comodin}`).then( ({data}) => {
              this.clientes = data.proveedores;
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally(this.loading_clientes = false);
      },
      loadLevels(unidad_negocio){
          this.loading_levels = true
          axios.get(`/api/niveles-proveedores-cadena/${unidad_negocio}`).then( ({data}) => {
              this.niveles = data.niveles;
          }).catch( ()=> {
              Swal.fire('Error', 'Ha sucedido un error', 'error');
          }).finally(this.loading_levels = false);
      },
      onSubmit(evt) {
        if(this.form.cliente_padres.length > 0){
          this.loading = true;
          let {content, mode} = this.dataForm;
          if(mode === 'create') this.store(content.unidad);
          else if(mode === 'edit') this.update();
        }
      },
      store(unidad_negocio){
        axios.post(`/api/cadena_proveedores/${unidad_negocio}`, this.form).then( ({data}) => {
            this.$emit('click');
            let proveedor_cadena = data.proveedor, proveedor;
            this.clientes.forEach((c) => {
              if (c.id === proveedor_cadena.proveedor_id) proveedor = c;
            });
            this.loading = false;
            this.$emit('store', {
              cliente: proveedor.nombre,
              clienteId: this.form.cliente,
              nivel: this.form.nivel,
              proveedor_padre: this.form.cliente_padre,
              proveedores_padre: this.form.cliente_padres, //e
              nombrePadre: data.padre === null ?  null : data.padre //e
            }); //DATA DEL CLIENTE AGREGADO
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        }).catch( () => {
          Swal.fire('Error', 'Ha sucedido un error', 'error');
        });
      },
      update(){
        axios.put(`/api/cadena_proveedores`, this.form).then( ({data}) => {
            if(data.error)
              Swal.fire('Error!', data.message, 'error');
            else{
              this.$emit('update', true);
              Swal.fire('Éxito', 'Se han actualizado los cambios', 'success');
            }
            
        }).catch(() => {
           Swal.fire('Error!', "Ha ocurrido un problema", 'error');
        }).finally( ()=>{
            this.$emit('click');
            this.loading = false;
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
      },
      addTag (newTag) {
        console.log(newTag)
      }
    }
  }
</script>