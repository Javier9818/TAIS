<template>
  <div>
    <b-overlay :show="loading" rounded="sm">
    <b-form @submit.prevent="onSubmit" @reset="onReset" id="form" >
        <b-row>
            <b-col md="6">
                <b-form-group id="input-group-1" label="Nombres" label-for="names">
                    <b-form-input id="names" v-model="form.names" type="text" required placeholder="Ingrese los nombres"></b-form-input>
                </b-form-group>
            </b-col>

            <b-col md="6">
            <b-form-group id="input-group-2" label="Apellido paterno" label-for="appaterno">
                    <b-form-input id="appaterno" v-model="form.appaterno" type="text" required placeholder="Ingrese el apellido paterno"></b-form-input>
                </b-form-group>
            </b-col>

            <b-col md="6">
                <b-form-group id="input-group-3" label="Apellido materno" label-for="apmaterno">
                    <b-form-input id="apmaterno" v-model="form.apmaterno" type="text" required placeholder="Ingrese el apellido materno"></b-form-input>
                </b-form-group>
            </b-col>

            <b-col md="6">
                <b-form-group id="input-group-3" label="Dirección" label-for="address">
                    <b-form-input id="address" v-model="form.address" type="text" required placeholder="Ingrese la dirección"></b-form-input>
                </b-form-group>
            </b-col>

            <b-col md="6">
                <b-form-group id="input-group-3" label="Email" label-for="email">
                    <b-form-input id="email" v-model="form.email" type="email" required placeholder="Ingrese su email"></b-form-input>
                </b-form-group>
            </b-col>

            <b-col md="6">
                <b-form-group id="input-group-3" label="Contraseña" label-for="password">
                    <b-form-input id="password" v-model="form.password" type="password" required placeholder="Ingrese su contraseña"></b-form-input>
                </b-form-group>
            </b-col>

            <b-col md="12">
               <b-form-group label="Tipo de usuario">
                  <b-form-radio-group id="radio-group-2" v-model="form.tipoUser" name="radio-sub-component" required>
                    <b-form-radio value="0">Super usuario</b-form-radio>
                    <b-form-radio value="1">Administrador</b-form-radio>
                  </b-form-radio-group>
                </b-form-group>
            </b-col>
            <hr>
            <b-col md="12" v-if="form.tipoUser === '1'">
               <b-form-group label="Empresa">
                  <b-form-select v-model="form.empresa" :options="empresas" required value-field="id" text-field="nombre">
                       <template v-slot:first>
                            <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
                        </template>
                  </b-form-select>
                </b-form-group>
            </b-col>

            <b-col md="12" v-if="form.tipoUser === '1' || form.tipoUser === 1">
               <b-form-group label="Permisos">
                  <b-form-checkbox-group
                    id="checkbox-group-1"
                    v-model="form.scopes"
                    :options="scopes"
                    value-field="id"
                    text-field="description"
                    name="flavour-1"
                  ></b-form-checkbox-group>
                </b-form-group>
            </b-col>

        </b-row>


        <b-button type="submit" variant="primary">Registrar</b-button>
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
        empresas:[],
        scopes:[],
        form: {
          names:'',
          appaterno:'',
          apmaterno:'',
          address:'',
          email:'',
          password:'',
          tipoUser:null,
          empresa: null,
          scopes:[],
          userID: userID
        }
      }
    },
    mounted(){
      let {content, mode} = this.dataForm;
      if(mode === 'edit'){
        this.loadDataForm(content);
        console.log(content)
      }
    },
    methods: {
      loadDataForm(content){
         this.form = {
            ...content
          }
      },
      onSubmit(evt) {
        evt.preventDefault();
        let {content, mode} = this.dataForm;
        if(mode === 'create')
          this.store()
        else
          this.update()
      },
      store(){
        this.loading = true;
        axios.post('/api/user', this.form).then( ({data}) => {
            this.$emit('click');
            this.$emit('store', data.user);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        }).catch( (error) => {
            Swal.fire('Error', 'Ha ocurrido un error', 'error');
        }).finally( () => {
          this.loading = false;
        });
      },
      update(){
        this.loading = true;
        axios.put('/api/user', {...this.form, userID: userID}).then( ({data}) => {
            this.$emit('click');
            this.$emit('update', this.form);
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success');
        }).catch( (error) => {
            Swal.fire('Error', 'Ha ocurrido un error', 'error');
        }).finally( () => {
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
      }
    },
    created(){
        this.empresas = empresas;
        this.scopes = scopes;
    }
  }
</script>
