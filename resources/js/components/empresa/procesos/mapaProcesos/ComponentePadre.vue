<template>
  <div class="container-fluid">
    <b-card no-body>
        <b-tabs pills card lazy>
            <b-tab title="Mapa de Procesos" active>
                <b-card-text>
                    <mapa-macro></mapa-macro>
                </b-card-text>
            </b-tab>
            <b-tab title="Mapa de procesos - Detalle" lazy>
                <b-card-text>
                    <mapa-detalle></mapa-detalle>
                </b-card-text>
            </b-tab>
            <b-tab title="Constructor" lazy>
                <b-card-text>
                    <mapa-constructor></mapa-constructor>
                </b-card-text>
            </b-tab>
        </b-tabs>
    </b-card>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        value: '',
        context: null
      }
    },
    methods: {
      
    }
  }
</script>
