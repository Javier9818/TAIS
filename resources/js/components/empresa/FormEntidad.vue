<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
      <b-form-group id="input-group-1" label="RUC" label-for="ruc">
        <b-form-input id="ruc" v-model="form.ruc" type="number" required placeholder="Ingrese el RUC"></b-form-input>
        <p v-if="$v.form.ruc.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-2" label="Nombre*" label-for="nombre">
        <b-form-input id="nombre" v-model="form.nombre" type="text" required placeholder="Ingrese el nombre de la empresa"></b-form-input>
        <p v-if="$v.form.nombre.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-4" label="Descripcion*" label-for="textarea">
       <b-form-textarea id="textarea" v-model="form.descripcion" placeholder="Ingrese alguna descripción..." rows="3"  max-rows="6"></b-form-textarea>
       <p v-if="$v.form.descripcion.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-2" label="Teléfono o celular*" label-for="nombre">
        <b-form-input id="nombre" v-model="form.celular" type="number" required placeholder="Ingrese el teléfono o celular de la empresa"></b-form-input>
        <p v-if="$v.form.celular.$error" class="help text-danger">Este campo es inválido</p>
      </b-form-group>

      <b-form-group id="input-group-2" label="Email*" label-for="nombre">
        <b-form-input id="nombre" v-model="form.email" type="email" required placeholder="Ingrese el email de la empresa"></b-form-input>
        <p v-if="$v.form.email.$error" class="help text-danger">Este campo es inválido</p>  
      </b-form-group>
      <b-form-group id="input-group-5" label="Foto" label-for="foto">
        <b-aspect :aspect="3" v-if="form.foto !== null && !edit">
          <b-img :src="form.foto ? '/storage/images/entidad/'+form.foto : '/assets/img/empty.jpg'" fluid alt="Responsive image" width="70%"></b-img>
          <a href="javascript:void(0)" @click="edit=true">Cambiar</a>
        </b-aspect>
         <input type="file" name="image" @change="getImage" accept="image/*" v-else>
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
        form: {
          image:null,
          ruc:'',
          nombre:'',
          descripcion:'',
          celular: '',
          email: '',
          empresa_id: empresa.id,
        }
      }
    },
    components: {
            PictureInput
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
              nombreText,
              minLength: minLength(2),
            },
            celular:{
              required,
              numeric,
              minLength: minLength(6),
              maxLength: maxLength(11)
            },
            descripcion:{
              required,
              text,
              minLength: minLength(4),
            },
            email:{
              required,
              email
            }
          }
    },
    mounted(){
        if(this.dataForm.mode === 'edit'){this.form = {...this.dataForm.content}}
    },
    methods: {
      getImage(e){
        let image = e.target.files[0];
        let reader = new FileReader();
        reader.readAsDataURL(image);
        reader.onload = e => {
            this.form.image = e.target.result;
        }
        this.loaded = true;
    }, 
     onChange (image) {
      if (image) {
         this.image = image;
      } else {
          console.log('FileReader API not supported: use the <form>, Luke!')
      }
      },
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
        axios.post(`/api/${this.entidad}`, this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('store', data.entidad);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        });
      },
      update(){
        axios.put(`/api/${this.entidad}/${this.dataForm.content.id}`, this.form).then( ({data}) => {
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

<style scoped>
  .group{
    height: 500px !important;
  }
</style>