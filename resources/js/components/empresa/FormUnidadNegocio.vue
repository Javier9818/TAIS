<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
      <b-form-group id="input-group-1" label="Producto" label-for="ruc">
        <b-form-input id="producto" v-model="form.producto" type="text" required placeholder="Ingrese el nombre del producto"></b-form-input>
        <p v-if="$v.form.producto.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-4" label="Descripcion*" label-for="textarea">
       <b-form-textarea id="textarea" v-model="form.descripcion" placeholder="Ingrese alguna descripción..." rows="3"  max-rows="6"></b-form-textarea>
       <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
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
        loading: false,
        form: {
          producto:'',
          descripcion:'',
          empresa_id: empresa.id,
        }
      }
    },
     validations:{
          form:{
            producto:{
              required,
              nombreText
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
        axios.post(`/api/unidad-negocio`, this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('store', data.unidad);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        });
      },
      update(){
        axios.put(`/api/unidad-negocio/${this.dataForm.content.id}`, this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('update', this.form);
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
