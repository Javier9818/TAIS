<template>
    <section>
        <form @submit.prevent="onSubmit"  id="form">
            <div class="row">
                <div class="col-6">
                    <b-form-group label="Fecha">
                        <b-input type="date" required v-model="form.fecha"></b-input>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group label="Emisor">
                        <b-input type="text" required v-model="form.emisor"></b-input>
                        <p v-if="!$v.form.emisor.textOracion" class="help text-danger">Este campo es inválido</p>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group label="Motivo">
                        <b-input type="text" required v-model="form.motivo"></b-input>
                        <p v-if="!$v.form.motivo.textOracion" class="help text-danger">Este campo es inválido</p>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group label="Monto o cantidad">
                        <b-input type="text" required v-model="form.monto"></b-input>
                        <p v-if="!$v.form.monto.decimal" class="help text-danger">Este campo es inválido</p>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-group label="Estado">
                        <b-input type="text" required v-model="form.estado"></b-input>
                        <p v-if="!$v.form.estado.textOracion" class="help text-danger">Este campo es inválido</p>
                    </b-form-group>
                </div>
                <div class="col-6">
                    <b-form-checkbox
                    id="checkbox-1"
                    v-model="form.util"
                    name="checkbox-1"
                    value="accepted"
                    >
                        útil
                    </b-form-checkbox>
                    <button class="btn btn-success btn-sm" type="submit" v-if="indexEdit !== 'null'">Actualizar</button>
                    <button class="btn btn-info btn-sm" type="submit" v-if="indexEdit !== 'null'" @change="indexEdit='null'">Nuevo</button>
                    <button class="btn btn-success btn-sm" type="submit" v-else>Registrar</button>
                </div>
            </div>
        </form>

        <b-table
        show-empty
        small
        stacked="md"
        :items="dataFuente"
        :fields="fields"
        >
        <template v-slot:cell(actions)="row">
            <b-button size="sm" @click="editaObjetivo(row.item, row.index)" class="mr-1 btn-success">
                <i class="fas fa-pencil-alt"></i>
            </b-button>
            <b-button size="sm" @click="eliminaObjetivo(row.index)" class="btn-danger">
                <i class="fas fa-trash"></i>
            </b-button>
        </template>
        </b-table>
    </section>
</template>

<script>
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
import {text, nombreText, letterOrWord, decimal, textOracion} from '../../../utils/expresiones'
  export default {
    props:['variables', 'index', 'idProceso'],
    data() {
      return {
        indexEdit:'null',
        fields: [
         { key: 'fecha', label: 'Fecha' },
         { key: 'emisor', label: 'Emisor' },
         { key: 'motivo', label: 'Motivo'},
         { key: 'monto', label: 'Monto' },
         { key: 'estado', label: 'Estado'},
         { key: 'actions', label: 'Opciones'}
        ],
        status: true,
        dataFuente:[],
        form:{
            fecha:null,
            emisor:'',
            motivo:'',
            monto:'',
            estado:'',
            util:'accepted'
        }
      }
    },
    validations:{
        form:{
            emisor:{
                textOracion
            },
            motivo:{
                textOracion
            },
            monto:{
               decimal
            },
            estado:{
                textOracion
            }
        }
    },
    methods: {
        onSubmit(){
            let { variables, index, form, dataFuente, idProceso, indexEdit, orderData, formatChart} = this
            if(!isNaN(indexEdit)){
                var dataF = orderData(dataFuente.map( (e,i) => { return i === indexEdit ? {...form} : e }))
                variables[index] = { ...variables[index], dataFuente: dataF }
                this.dataFuente = dataF
            }
            else{
                var dataF = orderData([...dataFuente, form])
                variables[index] = { ...variables[index], dataFuente: dataF}
                this.dataFuente = dataF
            }
            setTimeout(() => {
                axios.post(`/api/variables`,{data: JSON.stringify(variables), idProceso}).then(({data}) => {
                    if(!isNaN(indexEdit)){
                        this.clearForm()
                        this.indexEdit = 'null'
                    }
                    else{
                        this.indexEdit = 'null'
                    }
                    this.$emit('store');
                })
            }, 500);
            
        },
        editaObjetivo(item, index){
            this.form.motivo = item.motivo
            this.form.emisor = item.emisor
            this.form.fecha = item.fecha
            this.form.monto = item.monto
            this.form.util = item.util
            this.form.estado = item.estado
            this.indexEdit = index
        },
        clearForm(){
            this.form.motivo = ''
            this.form.emisor = ''
            this.form.fecha = null
            this.form.monto = ''
            this.form.util = 'accepted'
            this.form.estado = ''
        },
        eliminaObjetivo(indexDelete){
            let { variables, index, form, dataFuente, idProceso} = this
                variables[index] = { ...variables[index], dataFuente: dataFuente.filter( (e,i) => { return i === indexDelete ? false : true }) }
                this.dataFuente = dataFuente.filter( (e,i) => { return i === indexDelete ? false : true })
            setTimeout(() => {
                axios.post(`/api/variables`,{data: JSON.stringify(variables), idProceso})
            }, 500);
        },
        orderData(data){
            return _.orderBy(data, ['fecha'], ['asc'])
        }
    },
    mounted(){
        let { variables, index, idProceso} = this
        // console.log(variables);
        // console.log(index);
        // console.log(idProceso);
        this.dataFuente = variables[index].dataFuente || []
    }
  }
</script>