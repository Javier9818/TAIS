<template>
    <div class="container-fluid">
       <component-version></component-version>
       <b-table
        show-empty
        small
        stacked="md"
        :items="items"
        :fields="fields"
        :current-page="currentPage"
        :per-page="perPage"
        :filter="filter"
        :filterIncludedFields="filterOn"
        :sort-by.sync="sortBy"
        :sort-desc.sync="sortDesc"
        :sort-direction="sortDirection"
        @filtered="onFiltered"
        >

        <template v-slot:cell(actions)="row">
            <b-button size="sm" @click="iniciativas(row.item, row.index)" class="mr-1 btn-success">
                Iniciativas
            </b-button>
            <b-button size="sm" @click="grados(row.item, row.index)" class="btn-warning">
                Grado de implementación
            </b-button>
        </template>
        </b-table>

        <b-modal  size="lg" id="modal-iniciativas" :title="titleIniciativas" hide-footer>
                <iniciativas :item = item></iniciativas>
        </b-modal>

        <b-modal size="lg" id="modal-grado" :title="titleGrado" hide-footer>
            <grado-implementacion :item = item></grado-implementacion>
        </b-modal>
    </div>
</template>

<script>
import GradoImplementacion from './GradoImplementacion.vue'
import Iniciativas from './Iniciativas.vue'
  export default {
  components: { Iniciativas, GradoImplementacion },
    data() {
      return {
        items: [],
        fields: [
         { key: 'control.sigla', label: 'Código', sortable: true },
         { key: 'control.nombre', label: 'Objetivo de control', sortable: true },
         { key: 'actions', label: 'Opciones' }
        ],
        totalRows: 1,
        currentPage: 1,
        perPage: 8,
        pageOptions: [5, 10, 15],
        sortBy: '',
        sortDesc: false,
        sortDirection: 'asc',
        filter: null,
        filterOn: [],

        titleIniciativas: "Iniciativas de objetivo de control",
        titleGrado: "Grado de implementación de objetivos de control",
        item: null
      }
    },
    methods: {
        iniciativas(item, index){
            this.item = item
            this.titleIniciativas = `Iniciativas de objetivo de control - ${item.control.sigla}`,
            this.$root.$emit('bv::show::modal', 'modal-iniciativas')
        },
        grados(item, index){
            this.item = item
            this.titleGrado = `Grado de implementación de objetivo de control - ${item.control.sigla}`,
            this.$root.$emit('bv::show::modal', 'modal-grado')
        }
    },
    mounted(){
        this.items = objetivos_control;
    }
  }
</script>
