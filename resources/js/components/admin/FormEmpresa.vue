<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
      <b-form-group id="input-group-1" label="RUC" label-for="ruc">
        <b-form-input id="ruc" v-model="form.ruc" type="number" required placeholder="Ingrese el RUC"></b-form-input>
        <p v-if="$v.form.ruc.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-2" label="Nombre" label-for="nombre">
        <b-form-input id="nombre" v-model="form.nombre" type="text" required placeholder="Ingrese el nombre de la empresa"></b-form-input>
        <p v-if="$v.form.nombre.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-3" label="Direccion" label-for="direccion">
        <b-form-input id="direccion" v-model="form.direccion" type="text" required placeholder="Ingrese la dirección de la empresa"></b-form-input>
        <p v-if="$v.form.direccion.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-4" label="Descripcion" label-for="textarea">
       <b-form-textarea id="textarea" v-model="form.descripcion" placeholder="Ingrese alguna descripción..." rows="3"  max-rows="6"></b-form-textarea>
       <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

        <b-button type="submit" variant="primary">{{edit? 'Actualizar': 'Registrar'}}</b-button>
        <b-button variant="danger" @click="$emit('click')" v-if="!edit">Cancelar</b-button>
    </b-form>
    </b-overlay>
  </div>
</template>

<script>
import Swal from 'sweetalert2'
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, helpers} from 'vuelidate/lib/validators'
const text = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ.\u00f1\u00d1\s]*$/)
const nombreText = helpers.regex('alpha', /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/)
  export default {
    mixins: [validationMixin],
    props:['empresas', 'edit'],
    data() {
      return {
        loading: false,
        form: {
          ruc:'',
          nombre:'',
          direccion:'',
          descripcion:''
        },
      }
    },
    validations:{
          form:{
            ruc:{
              required,
              numeric,
              minLength: minLength(11),
              maxLength: maxLength(11)
            },
            nombre:{
              required,
              nombreText
            },
            direccion:{
              required,
              text,
              minLength: minLength(5),
            },
            descripcion:{
              required,
              text,
              minLength: minLength(4),
            }
          }
    },
    mounted(){
      this.form = empresa
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault();
        this.$v.$touch()
        if(!this.$v.$invalid)
          if(this.edit)
            this.update()
          else
            this.store()
      },
      store(){
        this.loading = true;
        axios.post('/api/empresa', this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('store', data.empresa);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        }).catch( () => {Swal.fire('Error', 'Ha sucedido un error', 'error');});
      },
      update(){
          this.loading = true;
          axios.put(`/api/empresa/${empresa.id}`, this.form).then( ({data}) => {
              this.$emit('click');
              this.loading = false;
              this.$emit('store', data.empresa);
              Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
          }).catch( () => {Swal.fire('Error', 'Ha sucedido un error', 'error');});;
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
