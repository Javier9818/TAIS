<template>
    <section>
        <b-overlay :show="loading" rounded="sm">
            <b-form @submit.prevent="onSubmit"  id="form">
                <div class="row">
                    <div class="col-md-12">
                        <b-form-group label="Perspectiva">
                            <b-form-radio-group id="radio-group-2" v-model="form.perspectiva" name="radio-sub-component" @change="handlePerspectiva">
                                <b-form-radio v-for="(p, i) in payload.perspectivas" :value="i" :key="i">{{p}}</b-form-radio>
                            </b-form-radio-group>
                            <p v-if="!$v.form.perspectiva.required" class="help text-danger">Este campo es requerido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-12">
                        <b-form-group id="input-group-2" label="Nombre" label-for="nombre">
                            <b-form-input id="nombre" v-model="form.nombre" type="text" required placeholder="Ingrese el nombre del objetivo"></b-form-input>
                            <p v-if="!$v.form.nombre.nombreText" class="help text-danger">Este campo es inválido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-12">
                        <b-form-group id="input-group-3" label="Descripcion" label-for="descripcion">
                            <b-form-input id="descripcion" v-model="form.descripcion" type="text" required placeholder="Ingrese la descripción del objetivo"></b-form-input>
                            <p v-if="!$v.form.descripcion.text" class="help text-danger">Este campo es inválido</p>
                            <p v-else-if="!$v.form.descripcion.minLength" class="help text-danger">Este campo es inválido</p>
                        </b-form-group>
                    </div>
                    <div class="col-12">
                        <b-form-group id="input-group-1" label="Tiene efecto en" label-for="cliente_two" class="col-md-12">
                            <b-overlay :show="loadingObj">
                                <multiselect 
                                    v-model="form.efecto" 
                                    tag-placeholder="Add this as new tag" 
                                    placeholder="Search or add a tag" 
                                    label="nombre" 
                                    track-by="nombre" 
                                    :options="objetivosFilter" 
                                    :multiple="true" 
                                    :taggable="true"
                                ></multiselect>
                            </b-overlay>
                            <p v-if="!$v.form.efecto.requerido" class="help text-danger">Este campo es requerido</p>
                        </b-form-group>
                    </div>
                </div>
            <b-button type="submit" variant="primary">{{payload.mode === 'create' ? 'Registrar' : 'Actualizar'}}</b-button>
            <b-button variant="danger" @click="$emit('click')">Cancelar</b-button>
            </b-form>
        </b-overlay>
    </section>
</template>

<script>
import Swal from 'sweetalert2'
import Multiselect from 'vue-multiselect'
import {validationMixin} from 'vuelidate'
import { requiredIf,required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
import {text, nombreText, letterOrWord} from '../../../utils/expresiones'
  export default {
    props:['payload'],
    mixins: [validationMixin],
    data() {
      return {
        loading:false,
        loadingObj:false,
        variable: '',
        nombreVar:'',
        formulaRegex:true,
        objetivos:[],
        objetivosFilter:[],
        form: {
            perspectiva: null,
            nombre: '',
            descripcion: '',
            efecto:[]
        }
      }
    },
    components: {
      Multiselect
    },
     validations:{
          form:{
            perspectiva:{
              required
            },
            nombre:{
              required,
              nombreText
            },
            descripcion:{
              required,
              text,
              minLength: minLength(3)
            },
            efecto:{
              requerido(data){
                 if(data.length === 0){
                   return this.form.perspectiva === this.payload.perspectivas.length-1
                 }else return true
              }
            }
          }
          
    },
    methods: {
        onSubmit(){
          let {mode} = this.payload
           this.$v.$touch()
           if(!this.$v.$invalid){
              mode === 'create' ?  this.$emit('store', this.form) : this.$emit('update', this.form);
           }else{
             alert('Los datos ingresados son incorrectos!')
           }
        },
        handlePerspectiva(id, data = []){
          let {objetivosFilter, objetivos, form} = this
          form.efecto = data
          objetivosFilter = objetivosFilter.splice(0, objetivosFilter.length, ...objetivos.filter( e => e.perspectiva >= id))
        }
    },
    mounted(){
      let {mode, content, objetivos} = this.payload
      this.objetivos = objetivos
      if(mode === 'edit'){
        this.form = content
        this.handlePerspectiva(content.perspectiva, content.efecto)
      }
    }
  }
</script>