<template>
  <div class="container-fluid">
    <component-version></component-version>
    <div class="row">
        <div class="col-12 col-md-6">
          <div class="accordion" role="tablist">
            <b-card no-body class="mb-1" v-for="(perspectiva, index) in perspectivas" :key="index">
              <b-card-header header-tag="header" class="p-1" role="tab">
                <b-button block v-b-toggle="`accordion-${index}`" variant="info">{{perspectiva.nombre}}</b-button>
              </b-card-header>
              <b-collapse :id="`accordion-${index}`" visible accordion="my-accordion" role="tabpanel">
                <b-card-body>
                    <div class="row mb-2" v-for="(objetivo, index_obj) in objetivosPorPerspectiva(perspectiva.id)" :key="index_obj">
                        <b-button  class="mr-1" @click="selectObjetivo(objetivo.id, objetivo.perspectiva_fk)">
                          {{index_obj + 1}}. {{objetivo.nombre}}
                        </b-button>
                    </div>
                    <div class="row justify-content-center" v-if="objetivosPorPerspectiva(perspectiva.id).length === 0 ">
                        <p class="text-center">No se encontraron registros.</p>
                    </div>
                </b-card-body>
              </b-collapse>
            </b-card>
          </div>
        </div>
        <div class="col-12 col-md-5 p-4 m-3" style="border: solid 1px black;">
            <b-overlay :show="loadMetas">
            </b-overlay>
            <h4 v-if="metas_empresariales.length > 0">Metas Empresariales - {{metas_empresariales[0].perspectiva.nombre}}</h4>
            <b-form-radio-group
              v-model="meta_empresarial_selected"
              :options="metas_empresariales"
              class="mb-3"
              value-field="id"
              text-field="nombre"
              disabled-field="notEnabled"
              stacked
              :disabled="parseInt(version) !== 1"
              @change="changeMeta"
            ></b-form-radio-group>
        </div>
    </div>

    <div class="row">
      <button class="btn btn-danger offset-10" v-b-modal.modal-1 :disabled="parseInt(version) !== 1"> <i class="fa fa-play"></i> Correr</button>
    </div>
    <b-modal id="modal-1" title="Correr cascada de metas" hide-footer>
      <form v-on:submit.prevent="correrMeta()">
          <div class="form-group">
            <label for="descripcion">Descripción</label>
            <input type="text" name="descripcion" id="descripcion" v-model="form.descripcion" class="form-control" required>
            <button class="btn btn-success mt-3 float-right" type="submit"><i class="fa fa-play"></i> Continuar</button>
          </div>
      </form>

    </b-modal>

    <div class="row justify-content-center mt-4">
      <div class="col-11 p-4" style="border: solid 1px black;">
            <h3>Metas empresariales resultantes</h3>
            <ul style="font-size:1.2em;">
              <li v-for="(meta_resultante, index) in metas_empresariales_resultantes" :key="index">
                  {{meta_resultante.sigla}} - {{meta_resultante.nombre}}
              </li>
            </ul>
      </div>
    </div>

  </div>
</template>

<script>
import Swal from 'sweetalert2'
import ComponentVersion from './ComponentVersion.vue'
  export default {
  components: { ComponentVersion },
    data() {
      return {
        objetivos: [],
        perspectivas: [],
        objetivoID:null,
        perspectivaID:null,
        metas_empresariales: [],
        meta_empresarial_selected: null,
        loadMetas: false,
        metas_empresariales_resultantes: [],
        version: 1,

        form:{
          descripcion: ''
        }
      }
    },
    mounted() {
      this.perspectivas = perspectivas
      this.objetivos = objetivos
      this.metas_empresariales_resultantes = metas_resultantes
      this.version = version || 1
    },
    methods: {
      objetivosPorPerspectiva(perspectivaID){
        return  this.objetivos.filter((objetivo) => {
            return ( objetivo.perspectiva_fk === perspectivaID )
        });
      },
      selectObjetivo(id, perspectiva_fk){
        this.objetivoID = id
        this.loadMetas = true
        this.meta_empresarial_selected = null
        axios.get(`/api/metas-empresariales-by-perspectiva/${perspectiva_fk}/${empresa.id}/${id}`).then(( { data } ) => {
            this.metas_empresariales = data.metas
            this.meta_empresarial_selected = data.meta_empresa.meta_empresarial_fk || null
        }).finally( () => {
          this.loadMetas = false
        });
      },
      changeMeta(meta_fk){
         axios.post(`/api/meta-empresarial-empresa`, {
            empresa_fk: empresa.id,
            meta_fk,
            objetivo_fk: this.objetivoID,
            userID: userID
          }).then(( { data } ) => {
            this.metas_empresariales_resultantes = data.metas_resultantes
            Swal.fire('Éxito', 'Se han guardado los cambios', 'success')
          }).finally( () => {

          });
      },
      correrMeta(){
        axios.post('/api/correr-cascada', { ...this.form, empresa: empresa.id, userID: userID }).then( ( { data } ) => {
          if(!data.error)
            Swal.fire('Éxito', 'Se corrió la cascada de metas satisfactoriamente', 'success').then( () => {
              this.$bvModal.hide('modal-1')
            })
          else{
            Swal.fire('error', data.message , 'error').then( () => {
              this.$bvModal.hide('modal-1')
            })
          }
        }); 
      }
    },
    watch:{
    }
  }
</script>
