<template>
  <section>
    <div class="row">
       <div class="col-md-4">
          <b-form-group id="cnIndicadores" label="Indicador" label-for="">
            <b-form-select 
              v-model="indicador" 
              :options="indicadores"
              value-field="id"
              text-field="nombre"
              @change="loadTable"
              required
            >
            <template v-slot:first>
                <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
            </template>
            </b-form-select>
          </b-form-group>
          <b-form-group label="Frecuencia de medición" v-if="dataIndicador">
            <select class="form-control" name="cbFrec" id="cbFrec" v-model="frecuencia" @change="handleFrec">
              <option value="0" selected>Mensual</option>
              <option value="1">Anual</option>
            </select>
          </b-form-group>
      </div>
      <div class="col-md-2">
        <button class="btn btn-sm btn-success" v-b-modal.datafuente>Agregar data fuente</button>
      </div>
      <div class="col-md-2">
        <button class="btn btn-sm btn-primary" @click="$bvModal.show('formChart')">Ver gráfico de tendencia</button>
      </div>
    </div>
    <hr>
    <table class="table" border="" v-if="dataIndicador">
      <colgroup span="3" width="100"></colgroup>
      <colgroup span="4" width="100"></colgroup>
      <tr>
        <td colspan="3"> <b>Proceso: </b></td>
        <td colspan="4"> <b>Responsable: </b> {{dataIndicador.responsable}}</td>
      </tr>
      <tr>
        <td colspan="3"> <b>Objetivo: </b> {{dataIndicador.objetivo}}</td>
        <td colspan="4"> <b>Linea base: </b> {{dataIndicador.lineaBase}}</td>
      </tr>
      <tr>
        <td colspan="4"></td>
        <td colspan="3" class="text-center"> <b>Semáforo </b> </td>
      </tr>
      <tr>
        <td> <b>Indicador</b> </td>
        <td> <b>Fórmula</b> </td>
        <td> <b>Valor meta</b> </td>
        <td> <b>Frecuencia de medición</b> </td>
        <td class="bg-danger"></td>
        <td class="bg-warning"></td>
        <td class="bg-success"></td>
      </tr>
      <tr>
        <td class="text-center"> {{dataIndicador.nombre}} </td>
        <td class="text-center"> {{dataIndicador.formula}}</td>
        <td class="text-center"> {{dataIndicador.meta}}</td>
        <td class="text-center"> {{dataIndicador.Frecuencia || '-'}}</td>
        <td class="text-center">[] &lt;{{dataIndicador.limiteInferior}}</td>
        <td class="text-center">{{dataIndicador.limiteInferior}} &lt;= [] &lt;= {{dataIndicador.limiteSuperior}} </td>
        <td class="text-center">[] &gt; {{dataIndicador.limiteSuperior}}</td>
      </tr>
      <tr>
        <td colspan="2"> <b>Fecha</b> </td>
        <td colspan="1" v-for="(v, index) in variables" :key="index"> <b>{{`${v.nombreVar} (${v.variable})`}}</b> </td>
        <td colspan="3" class="text-center"> <b>Medida</b> </td>
      </tr>
      <tr :class="colorSemaforo(v.data)" v-for="(v, index) in preconsolidado" :key="index">
        <td colspan="2"> {{v.month}} </td>
        <td colspan="1" v-for="(x, index) in v.data" :key="index"> {{ x.sum || '-' }} </td>
        <td colspan="3" class="text-center"> {{calculadora(v.data)}} </td>
      </tr>
  </table>

  <b-modal id="formChart" title="Gráfico de tendencia" size="lg" scrollable hide-footer>
    <chart-component @click="$bvModal.hide('formChart')" :dataChart="dataChart"></chart-component>
  </b-modal>

  <b-modal id="datafuente" title="Data fuente" size="lg" scrollable hide-footer>
    <data-fuente-component @click="$bvModal.hide('datafuente')" :variables="variablesOriginal" :idProceso="idProceso" @store="reload"></data-fuente-component>
  </b-modal>

  

  </section>
</template>

<script>
import Swal from 'sweetalert2'
  export default {
    props:['idProceso'],
    data() {
      return {
        frecuencia:0,
        variables:[],
        variablesOriginal:[],
        indicadores: [],
        indicador: null,
        dataIndicador: null,

        preconsolidado:[],
        dataChart:[]
      }
    },
    methods: {
      obtenerIndicadores(idProceso){
        this.indicadores = [];
        axios.get(`/api/indicador/${idProceso}`).then(({data}) => {
            let { indicadores, proceso } = data
            this.variablesOriginal = JSON.parse(proceso.variables_control)
            this.formatoDataFuente(JSON.parse(proceso.variables_control))
            indicadores.forEach(e => {
                this.indicadores.push({...JSON.parse(e.descripcion), id: e.id});
                // console.log({...JSON.parse(e.descripcion), id: e.id})
            });
        });
      },
      handleFrec(id){
        let { dataIndicador, frecuencia } = this
        alert(this.frecuencia)
        axios.put(`/api/indicador/${dataIndicador.id}`, {data: JSON.stringify({...dataIndicador, frecuencia })}).then(({data}) => {
            Swal.fire('Exito!!', 'Actualización exitosa', 'success');
        });
      },
      loadTable(value){

      },
      formatoDataFuente(data){
        this.variables = data.map((v) => {
            let dataFuenteFormat = this.monthAndYearFormat(v.dataFuente)
            return{
              ...v,
              dataFuente: _.chain(dataFuenteFormat)
              .groupBy("month")
              .map(function(currentItem) {
                return {
                    month:currentItem[0].month,
                    year:currentItem[0].year,
                    sum: _.sumBy(currentItem, function(o) { return o.util === 'accepted' ? parseInt(o.monto): 0; })
                }
              })
              .value()
              // dataFuente: this.monthAndYearFormat(v.dataFuente)
            }
        })
      },
      monthAndYearFormat(data){
        return (data || []).map((d) => {
                let date = new Date(d.fecha)
                return {
                  ...d,
                  month: `${date.getFullYear()} - ${date.getMonth() + 1}`,
                  year: date.getFullYear()
                }
              })
      },
      loadConsolidado(){
        let { dataIndicador, variables } = this 
        let temp = [];

        // variables.listVar.forEach((e) => {
            
        // })
        console.log(variables)
        console.log(dataIndicador)

        variables.forEach((e) => {
          let array =  e.dataFuente.forEach((d) => {
            temp.push(
              {
                ...d,
                var: e.variable
              }
            )
          })
        })
        setTimeout(() => {
          this.preconsolidado = _.chain(temp)
          .groupBy("month")
          .map(function (currentItem) {
              return _.zipObject(["month", "data"], [currentItem[0].month, currentItem]);
          })
          .value()

          console.log(
            _.chain(temp)
            .groupBy("month")
            .map(function (currentItem) {
                return _.zipObject(["month", "data"], [currentItem[0].month, currentItem]);
            })
            .value()
          )
        }, 500);
      },
      calculadora(data){
        let { dataIndicador } = this
        let formula = dataIndicador.formula
        data.forEach((e) => {
          formula = formula.replace(new RegExp(`[${e.var}]`, "g"), `${e.sum}`)
        })
        try {
          return eval(formula)
        } catch (error) {
          return "No definido"
        }
      },
      colorSemaforo(data){
        let num = this.calculadora(data)
        return  isNaN(num) ? '' :
          (num < this.dataIndicador.limiteInferior) ? 
          'bg-danger': 
          (num > this.dataIndicador.limiteSuperior) ? 
          'bg-success':'bg-warning'
      },
      storeDataFuente(data){
          this.formatoDataFuente(data)
          setTimeout(() => {
            this.loadConsolidado()
          }, 500);
      },
      reload(){
        this.obtenerIndicadores(this.idProceso);
        setTimeout(() => {
          this.loadConsolidado();
        }, 500);
      }
    },
    watch:{
        idProceso(id){
            this.obtenerIndicadores(id);
        },
        indicador(id){
          let {dataIndicador, indicadores} = this
            indicadores.forEach( (e) => {
              if(e.id === id){
                this.dataIndicador = e
                this.frecuencia = e.frecuencia || 0
                // console.log(e)
              } 
            });
            this.loadConsolidado()
        },
        preconsolidado(data){
          this.dataChart = data.map((e,i) => {
            return {
              month: e.month,
              value: this.calculadora(e.data),
              lineaBase: this.dataIndicador.lineaBase
            }
          })
        }
    },
    mounted(){
      if(this.idProceso) this.obtenerIndicadores(this.idProceso);
    }
  }
</script>
