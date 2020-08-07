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
            <div v-else>
              <div class="detalles_cadena">
                  <b-card no-body>
                    <b-tabs pills card lazy>
                      <b-tab title="Gráfico" active>
                        <b-card-text>
                            <b-button v-b-modal.historia class="btn btn-primary ml-3 my-2"><i class="fas fa-save"></i> Guardar en historial</b-button>
                            <componente-grafico-cadena :content="content" title = "Mapa cadena de suministro"/>
                            <b-modal id="historia" title="Detalles de historia" size="lg" scrollable hide-footer>
                                <b-overlay :show="loading">
                                  <label for="historia-input">Comentario de historia</label>
                                  <input type="text" id="historia-input" class="form-control" v-model="historia">
                                  <p v-if="$v.historia.$error" class="help text-danger">Este campo es inválido</p>  
                                  <button class="btn btn-success mt-2" @click="saveHistory">Guardar</button>
                                </b-overlay>
                            </b-modal>
                        </b-card-text>
                      </b-tab>
                      <b-tab title="Historial">
                        <b-card-text>
                            <componente-historial-cadena :unidad="unidad_negocio"/>
                        </b-card-text>
                      </b-tab>
                    </b-tabs>
                  </b-card>
              </div>
              
              
            </div>
      </section>
  </div>
</template>

<script>
import Swal from 'sweetalert2'
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
const text = helpers.regex('alpha', /^[a-zA-Z0-9À-ÿ.\u00f1\u00d1\s]*$/)
const nombreText = helpers.regex('alpha', /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]*$/)
  export default {
    mixins: [validationMixin],
    data() {
      return {
        options: unidades_negocio,
        loading: false,
        unidad_negocio: null,
        historia:"",
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
    validations:{
        historia:{
          required,
          text,
          minLength: minLength(4)
        }
    },
    mounted(){
      console.log(unidades_negocio)
    },
    methods: {
      saveHistory(){
          this.$v.$touch()
          if(!this.$v.$invalid){
            this.loading = true
            var content = JSON.stringify(this.content);
            axios.post('/api/cadena-historial', {
              unidad_negocio: this.unidad_negocio,
              historia: this.historia,
              content: content
            }).then( ({data}) => {
                Swal.fire('Éxito!', 'Se guardó historia correctamente', 'success').then( () => {
                  this.$root.$emit('bv::hide::modal', 'historia')
                  this.historia = ""
                });
            }).catch( () => {
               Swal.fire('Error', 'Ha sucedido un error', 'error');
            }).finally( () => {
               this.loading = false
            });
          }
      },
     loadDataCadena(){
       let nodeDataArray = [{"key": 0, "type": "Project", "name": empresa.nombre }];
       let linkDataArray = [];
       axios.get(`/api/entidades-cadena/${this.unidad_negocio}`).then(({data}) => {
          const { clientes, proveedores} = data;
          proveedores.forEach(c => {
            if (nodeDataArray.findIndex(e =>  e.key === c.id ) === -1)
              nodeDataArray.push({
                "key": c.id, 
                "type":"Group", 
                "name": c.name,
                "picture":c.foto
              });
            linkDataArray.push({
              "from": c.id,
              "frompid":"OUT" ,
              "to": c.padre === null ? 0 : c.padre
            });
          });

          clientes.forEach(c => {
            if (nodeDataArray.findIndex(e =>  e.key === c.id ) === -1)
              nodeDataArray.push({
                "key": c.id, 
                "type":"Group2", 
                "name": c.name,
                "picture":c.foto
              });
            linkDataArray.push({
              "from": c.padre === null ? 0 : c.padre,
              "frompid":"OUT" ,
              "to":c.id
            });
          });

       })
       .finally( () => {
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
