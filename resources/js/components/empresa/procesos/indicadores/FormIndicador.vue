<template>
    <section>
        <b-overlay :show="loading" rounded="sm">
            <b-form @submit.prevent="onSubmit"  id="form">
                <div class="row">
                    <div class="col-md-6">
                        <b-form-group id="input-group-1" label="Nombre" label-for="nombre">
                            <b-form-input id="nombre" v-model="form.nombre" type="text" required placeholder="Ingrese el nombre del indicador"></b-form-input>
                            <p v-if="!$v.form.nombre.nombreText" class="help text-danger">Este campo es inválido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-6">
                        <b-form-group id="input-group-2" label="Objetivo" label-for="objetivo">
                            <b-form-input id="objetivo" v-model="form.objetivo" type="text" required placeholder="Ingrese el objetivo del indicador"></b-form-input>
                            <p v-if="!$v.form.objetivo.text" class="help text-danger">Este campo es inválido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-12">
                        <b-form-group id="input-group-2" label="Responsable" label-for="responsable">
                            <b-form-input id="responsable" v-model="form.responsable" type="text" required placeholder="Ingrese el responsable del indicador"></b-form-input>
                            <p v-if="!$v.form.responsable.text" class="help text-danger">Este campo es inválido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-12">
                        <b-form-group id="input-group-3" label="Iniciativas" label-for="iniciativas">
                            <b-form-textarea id="iniciativas" v-model="form.iniciativas" placeholder="Ingrese las iniciativas..." rows="3"  max-rows="6"></b-form-textarea>
                            <p v-if="!$v.form.iniciativas.text" class="help text-danger">Este campo es inválido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-12">
                        <b-form-group id="input-group-4" label="Semaforo">
                            <div class="row"> 
                              <div class="col-4 text__indicador">
                                <i class="fas fa-circle indicador__red"></i>  &lt; <input type="number" class="col-8" v-model="form.limiteInferior">
                              </div>
                              <div class="col-4 text__indicador">
                                 <input type="text" class="col-3" disabled v-model="form.limiteInferior">  &lt;= <i class="fas fa-circle indicador__yellow"></i>  &lt;= <input type="text" class="col-3" disabled v-model="form.limiteSuperior">
                              </div>
                              <div class="col-4 text__indicador">
                                 <i class="fas fa-circle indicador__green"></i>  &gt; <input type="number" class="col-8" v-model="form.limiteSuperior">
                              </div>
                            </div>  
                            <p v-if="!$v.form.limiteSuperior.esMayor" class="help text-danger">Limite verde debe ser mayor que limite rojo</p>
                        </b-form-group>
                    </div>

                    <div class="col-md-6">
                        <b-form-group id="input-group-5" label="Meta (Más de)" label-for="meta">
                            <b-form-input id="meta" v-model="form.meta" type="number" required placeholder="Ingrese el valor meta"></b-form-input>
                            <p v-if="!$v.form.meta.esMayor" class="help text-danger">Este campo debe ser mayor a limite rojo</p>
                        </b-form-group>
                    </div>

                    <div class="col-md-6">
                        <b-form-group id="input-group-6" label="Linea base" label-for="lineaBase">
                            <b-form-input id="lineaBase" v-model="form.lineaBase" type="number" required placeholder="Ingrese la linea base del indicador"></b-form-input>
                            <p v-if="!$v.form.lineaBase.esMayor" class="help text-danger">Este campo debe ser mayor a limite rojo</p>
                        </b-form-group>
                    </div>

                    <hr class="col-10">

                    <div class="col-md-3">
                        <b-form-group id="input-group-7" label="Variable" label-for="variable">
                            <b-form-input id="variable" v-model="variable" type="text"  ></b-form-input>
                            <p v-if="!$v.variable.letterOrWord" class="help text-danger">Este campo solo puede contener una palabra o letra</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-3">
                        <b-form-group id="input-group-8" label="Nombre" label-for="nombreVar">
                            <b-form-input id="nombreVar" v-model="nombreVar" type="text"  ></b-form-input>
                            <p v-if="!$v.nombreVar.nombreText" class="help text-danger">Este campo no es válido</p>
                        </b-form-group>
                    </div>
                    <div class="col-md-1">
                      <b-form-group id="input-group-9" label="-" label-for="">
                        <button type="button" class="btn btn-success btn-sm" @click="addList()" :disabled="!$v.variable.letterOrWord || !$v.nombreVar.nombreText"  >>></button>
                      </b-form-group>
                    </div>
                    <div class="col-md-5">
                      <b-list-group v-for="(v, i) in form.listVar" :key="i">
                        <b-list-group-item>{{v.variable}} - {{v.nombreVar}} <button type="button" @click="removeList(i)" class="btn btn-danger btn-sm">x</button> </b-list-group-item>
                      </b-list-group>
                    </div>

                    <div class="col-md-12">
                        <b-form-group id="input-group-9" label="Formula" label-for="formula">
                            <b-form-input id="formula" v-model="form.formula" type="text" required @keyup="validaFormula()" ></b-form-input>
                            <p v-if="!formulaRegex" class="help text-danger">La fórmula ingresada es incorrecta</p>
                        </b-form-group>
                    </div>

                </div>
            <b-button type="submit" variant="primary">{{dataForm.mode === 'create' ? 'Registrar' : 'Actualizar'}}</b-button>
            <b-button variant="danger" @click="$emit('click')">Cancelar</b-button>
            </b-form>
        </b-overlay>
    </section>
</template>

<script>
import Swal from 'sweetalert2'
import {validationMixin} from 'vuelidate'
import {required, numeric, minValue, maxValue, maxLength, minLength, email, helpers} from 'vuelidate/lib/validators'
import {text, nombreText, letterOrWord} from '../../../utils/expresiones'
  export default {
    props:['dataForm'],
    mixins: [validationMixin],
    data() {
      return {
        loading:false,
        variable: null,
        nombreVar:null,
        formulaRegex:true,
        form: {
          nombre:'',
          objetivo:'',
          responsable:'',
          iniciativas:'',
          limiteInferior:null,
          limiteSuperior: null,
          meta:null,
          lineaBase:null,
          listVar:[],
          formula:null
        }
      }
    },
     validations:{
          form:{
            nombre:{
              required,
              nombreText
            },
            objetivo:{
              required,
              text
            },
            responsable:{
              required,
              text,
            },
            iniciativas:{
              required,
              text,
            },
            limiteSuperior:{
              required,
              esMayor(value){
                let {form} = this
                return form.limiteInferior == null ? true : (parseFloat(form.limiteInferior || 0)<= value)
              }
            },
            meta:{
              required,
              esMayor(value){
                let {form} = this
                return (parseFloat(form.limiteInferior || -1000)<= value)
              }
            },
            lineaBase:{
              required,
              esMayor(value){
                let {form} = this
                return (parseFloat(form.limiteInferior || -1000)<= value)
              }
            }
          },
          variable:{
            letterOrWord
          },
          nombreVar:{
            nombreText
          }
    },
    methods: {
        onSubmit(){
           this.$v.$touch()
           if(!this.$v.$invalid && this.formulaRegex){
             alert('asas')
           }else{
             alert('Los datos ingresados son incorrectos!')
           }
            // validaFormula()
        },
        validaFormula(){
          let  {formula, listVar}  = this.form
          if((formula || '').length > 0){
            listVar.forEach(e => {
              formula = formula.replace(new RegExp(`[${e.variable}]`, "g"),"1")
            });

            try {
              console.log(eval(formula))
              this.formulaRegex = true
            } catch (error) {
              this.formulaRegex = false
            }
          }
        },
        removeList(index){
          this.form.listVar.splice(index,1)
        },
        addList(){
          let { variable, nombreVar, form} = this
          form.listVar.push({
            variable,
            nombreVar,
          })
          setTimeout(()=>{
            this.variable = null,
            this.nombreVar = null
          }, 500)
        }
    }
  }
</script>

<style scoped>
    .indicador__red{
        color: red;
    }

    .indicador__yellow{
        color: yellow;
    }

    .indicador__green{
        color: green;
    }
    .text__indicador{
      font-size: 1.4em;
    }
</style>