<template>
  <div>
      <h5>Unidad de negocio</h5>
      <b-form-select 
        v-model="unidad_negocio" 
        :options="options"
        value-field="id"
        text-field="producto"
        @change="loadDataCadena()"
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
            
            <componente-grafico-cadena v-else :content="content"></componente-grafico-cadena>
            
      </section>
  </div>
</template>

<script>
  export default {
    data() {
      return {
        options: unidades_negocio,
        unidad_negocio: null,
        content:{ "class": "go.GraphLinksModel",
                "nodeCategoryProperty": "type",
                "linkFromPortIdProperty": "frompid",
                "linkToPortIdProperty": "topid",
                "nodeDataArray": [
                  // {"key":1, "type":"Table", "name":"Product"},
                  // {"key":2, "type":"Table", "name":"Sales"},
                  // {"key":3, "type":"Table", "name":"Period"},
                  // {"key":4, "type":"Table", "name":"Store"},
                  // {"key":11, "type":"Join", "name":"Product, Class"},
                  // {"key":12, "type":"Join", "name":"Period"},
                  // {"key":13, "type":"Join", "name":"Store"},
                  // {"key":21, "type":"Project", "name":"Product, Class"},
                  // {"key":31, "type":"Filter", "name":"Boston, Jan2014"},
                  // {"key":32, "type":"Filter", "name":"Boston, 2014"},
                  // {"key":41, "type":"Group", "name":"Sales"},
                  // {"key":42, "type":"Group", "name":"Total Sales"},
                  // {"key":51, "type":"Join", "name":"Product Name"},
                  // {"key":61, "type":"Sort", "name":"Product Name"},
                  // {"key":71, "type":"Export", "name":"File"}
                ],
                "linkDataArray": [
                  // {"from":1, "frompid":"OUT", "to":11, "topid":"L"},
                  // {"from":2, "frompid":"OUT", "to":11, "topid":"R"},
                  // {"from":3, "frompid":"OUT", "to":11, "topid":"R"},
                  // {"from":4, "frompid":"OUT", "to":13, "topid":"R"},
                  // {"from":11, "frompid":"M", "to":12, "topid":"L"},
                  // {"from":12, "frompid":"M", "to":13, "topid":"L"},
                  // {"from":13, "frompid":"M", "to":21},
                  // {"from":21, "frompid":"OUT", "to":31},
                  // {"from":21, "frompid":"OUT", "to":32},
                  // {"from":31, "frompid":"OUT", "to":41},
                  // {"from":32, "frompid":"OUT", "to":42},
                  // {"from":41, "frompid":"OUT", "to":51, "topid":"L"},
                  // {"from":42, "frompid":"OUT", "to":51, "topid":"R"},
                  // {"from":51, "frompid":"OUT", "to":61},
                  // {"from":61, "frompid":"OUT", "to":71}
                ]
        }
      }
    },
    methods: {
     loadDataCadena(){
       let nodeDataArray = [{"key": 0, "type": "Project", "name": empresa.nombre }];
       let linkDataArray = [];
       axios.get(`/api/entidades-cadena/${this.unidad_negocio}`).then(({data}) => {
          const { clientes, proveedores} = data;
          proveedores.forEach(c => {
            nodeDataArray.push({
              "key": c.id, 
              "type":"Group", 
              "name": c.name
            });
            linkDataArray.push({
              "from": c.id,
              "frompid":"OUT" ,
              "to": c.padre === null ? 0 : c.padre
            });
          });

          clientes.forEach(c => {
            nodeDataArray.push({
              "key": c.id, 
              "type":"Group", 
              "name": c.name
            });
            linkDataArray.push({
              "from": c.padre === null ? 0 : c.padre,
              "frompid":"OUT" ,
              "to":c.id
            });
          });
       }).finally( () => {
         this.content = { 
          "class": "go.GraphLinksModel",
            "nodeCategoryProperty": "type",
            "linkFromPortIdProperty": "frompid",
            "linkToPortIdProperty": "topid",
            "nodeDataArray":nodeDataArray,
            "linkDataArray":linkDataArray
          };
       });
        
     }
    }
  }
</script>

<style scoped>
    .content-message{
        height: 50vh;
    }
</style>
