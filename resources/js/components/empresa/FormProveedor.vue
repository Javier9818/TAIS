<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form">
      <b-form-group id="input-group-1" label="RUC" label-for="ruc">
        <b-form-input id="ruc" v-model="form.ruc" type="text" required placeholder="Ingrese el RUC"></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-2" label="Nombre*" label-for="nombre">
        <b-form-input id="nombre" v-model="form.razon_social" type="text" required placeholder="Ingrese el nombre de la empresa"></b-form-input>
      </b-form-group>

      <b-form-group id="input-group-4" label="Descripcion*" label-for="textarea">
       <b-form-textarea id="textarea" v-model="form.descripcion" placeholder="Ingrese alguna descripción..." rows="3"  max-rows="6"></b-form-textarea>
      </b-form-group>

        <b-button type="submit" variant="primary">{{dataForm.mode === 'create' ? 'Registrar' : 'Actualizar'}}</b-button>
        <b-button variant="danger" @click="$emit('click')">Cancelar</b-button>
    </b-form>
    </b-overlay>
  </div>
</template>

<script>

import Swal from 'sweetalert2'
  export default {
    props:['dataForm'],
    data() {
      return {
        loading: false,
        form: {
          ruc:'',
          razon_social:'',
          descripcion:'',
          empresa_id: empresa.id,
        }
      }
    },
    mounted(){
        if(this.dataForm.mode === 'edit'){this.form = {...this.dataForm.content}}
    },
    methods: {
      onSubmit(evt) {
        evt.preventDefault();
        this.loading = true;
        if(this.dataForm.mode === 'create') this.store();
        else if(this.dataForm.mode === 'edit') this.update();
      },
      store(){
        axios.post('/api/proveedor', this.form).then( ({data}) => {
            this.$emit('click');
            this.loading = false;
            this.$emit('store', data.proveedor);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        });
      },
      update(){
        axios.put(`/api/proveedor/${this.dataForm.content.id}`, this.form).then( ({data}) => {
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
