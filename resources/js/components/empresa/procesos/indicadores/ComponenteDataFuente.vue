<template>
  <b-card no-body>
    <b-tabs card>
        <b-tab v-for="(variable, index) in variables"  :title="variable.nombreVar" :key="index" :active="index===0" lazy>
           <form-data-fuente :variables="variables" :index="index" :idProceso="idProceso" @store="store"></form-data-fuente>
        </b-tab>
    </b-tabs>
</b-card>
</template>

<script>
  export default {
    props:['variables', 'idProceso'],
    data() {
      return {
        frecuencia: 0,
        context: null
      }
    },
    methods: {
      onContext(ctx) {
        this.context = ctx
      },
      store(){
        this.$emit('store')
      }
    },
    mounted(){
        // console.log(this.variables)
    }
  }
</script>
