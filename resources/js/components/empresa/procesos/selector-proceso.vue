<template>
  <section style="width: 100%">
      <div class="row justify-content-around">
          <div class="col-md-4">
              <b-form-group id="cbProcesos" label="Procesos priorizados" label-for="">
                <b-overlay :show="loadP">
                  <b-form-select 
                    v-model="proceso" 
                    :options="procesos"
                    value-field="id"
                    text-field="nombre"
                    @change="loadSubProcesos"
                    required
                  >
                  <template v-slot:first>
                      <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
                  </template>
                  </b-form-select>
                </b-overlay>
              </b-form-group> 
          </div>
          <div class="col-md-4">
               <b-form-group id="cbSubProcesos" label="SubProcesos" label-for="">
                 <b-overlay :show="loadSP">
                    <b-form-select 
                      v-model="subproceso" 
                      :options="subprocesos"
                      value-field="id"
                      text-field="nombre"
                      required
                    >
                    <template v-slot:first>
                        <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
                    </template>
                    </b-form-select>
                 </b-overlay>
              </b-form-group> 
          </div>
      </div>
  </section>
</template>

<script>
  export default {
    data() {
      return {
        proceso:null,
        subproceso:null,
        loadP:false,
        loadSP:false,
        procesos:[],
        subprocesos: []
      }
    },
    mounted(){
      this.getProcesos()
    },
    methods: {
      getProcesos(){
        this.loadP = true
        axios.get(`/api/procesos-prio/${unidad}`).then(({data}) => {
          this.procesos = data.procesos;
          this.subproceso = null
          this.loadP = false
        });
      },
      loadSubProcesos(proceso){
        this.loadSP = true
        axios.get(`/api/subproceso/${unidad}/${proceso}`).then(({data}) => {
          this.subprocesos = data.subprocesos;
          this.loadSP = false
        });
      }
    },
    watch:{
      proceso(id){
        this.$emit('changue-process', id);
        this.subproceso = null;
      },
      subproceso(id){
        this.$emit('changue-process', id);
      }
    }
  }
</script>
