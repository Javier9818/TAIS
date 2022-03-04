<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
      <b-form-group id="input-group-1" label="Nombre" label-for="nombre">
        <b-form-input id="nombre" v-model="form.nombre" type="text" required placeholder="Ingrese el nombre"></b-form-input>
        <p v-if="$v.form.nombre.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-4" label="Descripcion*" label-for="textarea">
       <b-form-textarea id="textarea" v-model="form.descripcion" placeholder="Ingrese alguna descripción..." rows="3"  max-rows="6"></b-form-textarea>
       <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group label="Perspectiva">
          <b-form-radio-group id="checkbox-group-2" v-model="form.perspectiva_fk" name="flavour-2" >
              <b-form-radio v-for="(perspectiva, index) in perspectivas" :value="perspectiva.id" :key="index" required>
                {{perspectiva.nombre}}
              </b-form-radio>
          </b-form-radio-group>
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
import PictureInput from 'vue-picture-input'
const text = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ.\u00f1\u00d1\s]*$/)
const nombreText = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ\u00f1\u00d1\s]*$/)
  export default {
    mixins: [validationMixin],
    props:['dataForm', 'entidad'],
    data() {
      return {
        loading: false,
        edit:false,
        perspectivas:[],
        form: {
          nombre:'',
          descripcion:'',
          perspectiva_fk:'',
          empresaID: empresa.id,
          userID: userID
        }
      }
    },
    components: {
            PictureInput
        },
     validations:{
          form:{
            nombre:{
              required,
              text,
              minLength: minLength(2),
            },
            descripcion:{
              required,
              text,
              minLength: minLength(4),
            }
          }
    },
    mounted(){
        if(this.dataForm.mode === 'edit'){this.form = {...this.dataForm.content}}
        this.perspectivas = perspectivas
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault();
        this.$v.$touch()
        if(!this.$v.$invalid)
        {
          this.loading = true;
          if(this.dataForm.mode === 'create') this.store();
          else if(this.dataForm.mode === 'edit') this.update();
        }
      },
      store(){
        axios.post(`/api/objetivo-estrategico`, this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('store', data.objetivo);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success').then( () => {
              location.reload()
            });
        });
      },
      update(){
        axios.put(`/api/objetivo-estrategico/${this.dataForm.content.id}`, {...this.form, userID:userID}).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('update', this.form);
            Swal.fire('Éxito', 'Se han actualizado los cambios', 'success').then( () => {
              location.reload()
            });
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

<style scoped>
  .group{
    height: 500px !important;
  }
</style>