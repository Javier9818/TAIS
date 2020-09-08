<template>
  <b-form-group label="Version">
        <b-overlay :show="load">
            <b-form-select 
                v-model="version" 
                :options="versiones"
                value-field="id"
                text-field="descripcion"
                required
                @change="versionSelected"
                >
                <template v-slot:first>
                    <b-form-select-option :value="null">-- Version Actual --</b-form-select-option>
                </template>
            </b-form-select>
        </b-overlay>
    </b-form-group>
</template>

<script>
  export default {
    data() {
      return {
       version:null,
       load: false,
       versiones:[]
      }
    },
    methods: {
      loadVersions(){
          this.load = true;
          axios.get(`/api/version/${unidad}`).then(({data}) => {
              this.versiones = data.versiones;
              this.load = false;
          });
      },
      versionSelected($id){
          this.$emit('chagueVersion', this.versiones.find((version)=> {return version.id === $id}))
      }
    },
    mounted(){
        this.loadVersions()
    }
  }
</script>
