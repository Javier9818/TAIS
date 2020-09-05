<template>
    <section>
        <div class="row">
            <div class="col-md-6">
                <criterios @criterio-selected="eventCriterio"></criterios>
            </div>
            <div class="col-md-6">
                <escalas :criterio="criterioSelected"></escalas>
            </div>
        </div>
    </section>
</template>

<script>
  export default {
    data() {
      return {
          criterioSelected: null
      }
    },
    methods: {
        eventCriterio(id){
            this.criterioSelected = id;
        }
    }
  }
</script>
