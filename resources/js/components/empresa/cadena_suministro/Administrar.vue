<template>
  <div>
      <h5>Unidad de negocio</h5>
      <b-form-select 
        v-model="unidad_negocio" 
        :options="options"
        value-field="id"
        text-field="producto"
      >
        <template v-slot:first>
            <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
        </template>
      </b-form-select>
      <hr>  
      <section>
            <div class="row justify-content-center align-items-center content-message" v-if="unidad_negocio===null">
                No ha seleccionado una unidad de negocio.
            </div>
            <div class="detalles_cadena" v-else>
                <b-card no-body>
                  <b-tabs pills card lazy>
                    <b-tab title="Clientes" active>
                      <b-card-text>
                       <componente-cadena-cliente :unidad='unidad_negocio'></componente-cadena-cliente>
                      </b-card-text>
                    </b-tab>
                    <b-tab title="Proveedores">
                      <b-card-text>
                        <componente-cadena-proveedor :unidad='unidad_negocio'></componente-cadena-proveedor>
                      </b-card-text>
                    </b-tab>
                  </b-tabs>
                </b-card>
            </div>
      </section>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        options: unidades_negocio,
        unidad_negocio: null
      }
    },
    methods: {
     
    }
  }
</script>

<style scoped>
    .content-message{
        height: 50vh;
    }
</style>
