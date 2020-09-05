<template>
  <div class="container-fluid mt-4" >
    <b-card no-body>
        <b-tabs pills card lazy>
            <b-tab title="Criterios" active>
                <b-card-text>
                    <componente-criterio></componente-criterio>
                </b-card-text>
            </b-tab>
            <b-tab title="Priorización" lazy>
                <b-card-text>
                    <matriz-priorizacion></matriz-priorizacion>
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
