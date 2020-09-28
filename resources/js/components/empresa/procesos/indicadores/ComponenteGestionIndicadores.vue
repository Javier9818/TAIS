<template>
  <section style="width: 100%">
      <selector-proceso @changue-process='setProcess'></selector-proceso>
      <div class="row justify-content-around">
        <div class="col-4">
            <select-version @chagueVersion='handleVersion'></select-version>
        </div><div class="col-4"></div>
      </div>
     <div class="container">
          <b-card no-body>
            <b-tabs pills card lazy>
                <b-tab title="Indicadores de desempeño" active style="background: rgb(212 207 207)">
                    <componente-indicadores :idProceso='idProceso'></componente-indicadores>
                </b-tab>
                <b-tab title="Mapa estratégico" lazy>
                    <b-card-text>
                        
                    </b-card-text>
                </b-tab>
                <b-tab title="Tablero de comnado" lazy>
                    <b-card-text>
                        
                    </b-card-text>
                </b-tab>
            </b-tabs>
        </b-card>
     </div>
  </section>
</template>

<script>
  export default {
    data() {
      return {
        idProceso: null,
        idVersion: null,
      }
    },
    methods:{
     setProcess(idProceso){
         this.idProceso = idProceso
     },
     handleVersion(idVersion){
         this.idVersion = idVersion
     }
    }
  }
</script>
