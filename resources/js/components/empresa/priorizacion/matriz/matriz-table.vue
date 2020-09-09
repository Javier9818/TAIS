<template>
 <form @submit.prevent="save">
    <div class="row">
      <div class="col-md-5">
        <select-version @chagueVersion='handleVersion'></select-version>
      </div>
    </div>
    <div class="row">
    <div class="col-6 col-lg-6">
      <h4>Procesos priorizados: </h4>
      <b-table striped hover :items="priority_process_temp" small>
      </b-table>
    </div>  
    <div class="col-6" v-if="editable">
        <b-form-group label="Número de procesos a priorizar">
            <input type="number" class="form-group" v-model="PC.cant" @change="pre">
            <b-overlay :show="load"> 
              <button class=" btn btn-primary" type="submit" :disabled="!(PC.process.length > 0 && PC.criterio.length > 0 && PC.cant >0)">Guardar</button>
              <a class=" btn btn-danger" @click="deleteMatriz" :disabled="!(PC.process.length > 0 && PC.criterio.length > 0)">Eliminar</a>
            </b-overlay>
        </b-form-group>
    </div>    
    <div class="col-12 mt-3" id='tabla-priorizacion'>
      <b-overlay :show="show" rounded="sm">
          <table class="table table-striped ">
            <thead class=" thead-light">
              <tr>
                <th ></th>  
                <th scope="col" :colspan="PC.criterio.length" class="text-center">Factores críticos</th>       
              </tr>  
            </thead>
            <thead class="thead-dark">     
              <tr>
                <th class=" bg-light text-dark">Pesos</th>
                <!--pesos-->
                <th scope="col" v-for="(item, index) in PC.criterio" :key="index">{{item.weight}}</th>       
              </tr>
              <tr>
                <th scope="col" class=" bg-light text-dark">Procesos</th>
                <!--Criterios-->
                <th scope="col" v-for="(item, index) in PC.criterio" :key="index">{{item.name}}</th>
                <th scope="col">Total</th>       
              </tr>
            </thead>
            <tbody>
              <tr v-for="(item, index) in PC.process" :key="index">
                <!--Procesos-->
                <th scope="row" class=" bg-dark text-light" >{{item.name}}</th>
                <!--Procesos x Criterios-->
                <td v-for="(item2, index) in sortItems(item.idprocess)" :key="index">                  
                  <!-- <b-form-spinbutton id="demo-sb" @change="sortPriority()" :min="item2.min" :max="item2.max" v-model="PC.items[(searchItem(item2))].point" ></b-form-spinbutton> -->
                  <select name="scale" id="sclae" v-model="PC.items[(searchItem(item2))].point" @change="sortPriority(PC.cant)" :disabled="!editable" required>
                      <option :value="null" disabled >Elegir</option>
                      <option :value="v.puntaje" v-for="v in item2.scales" :key="v.id" >{{v.descripcion}}</option>
                  </select>
                </td>      
                <th scope="row" class=" bg-dark text-light" >{{Total(item.idprocess)}}</th>
              </tr>   
            </tbody>
          </table>
      </b-overlay>
      <div id='prue'>
        </div>   
    </div>
  </div>
 </form>
</template>

<script> 
import Swal from 'sweetalert2';
 
export default {
  props:['table'],
  data()
  {
    return{
      editable:true,
      load:false,
      cant:0,
      items: [
      ],
      show:true,
      PC:{
        criterio:[],
        process:[],
        items:[],
        cant:0
      },
      priority_process:[],
      priority_process_temp:[],
      disabled:true
    }
  },
  methods:
  {    
    refresh(data)
    {
      this.show=true 
      this.priority_process = []
      setTimeout(() => { 
        
        data.process.forEach(element => {
          this.priority_process.push(
            {
              idprocess:element.idprocess, 
              name:element.name,
              total:0
            }
          )
        });     
        this.sortPriority(data.cant | 0) 
        this.PC={...data, cant: data.cant}
        this.show=false
      },2000);
    },
    changeCant(cant){
        alert(cant)
    },
    sortItems(id)
    {
      let items=[]
      this.PC.items.forEach(element => {
        this.PC.criterio.forEach(criterio => {
          if (element.idprocess==id && element.idcriterio==criterio.idcriterio) {
            items.push(element)
          }
        })        
      })
      return items
    },
    Total(id)
    {
      
      let total=0       
      this.PC.items.forEach(element => {
        this.PC.criterio.forEach(criterio => {
          if (element.idprocess==id && element.idcriterio==criterio.idcriterio) {
            total+=(element.point*criterio.weight)
          }
        })        
      }) 
      this.priority_process.forEach(element => {
        if (element.idprocess==id) {
          element.total= total
        }
      })
          
      return total
    },
    searchItem(element){
      for (let index = 0; index < this.PC.items.length; index++) {
        if (element.idcriterio==this.PC.items[index].idcriterio && element.idprocess==this.PC.items[index].idprocess) {
          return index
        }
      }
    },
    sortPriority(cant){
      setTimeout(() => {
        let array= this.quickSort(JSON.parse( JSON.stringify( this.priority_process ) ))
        this.priority_process_temp=[]
        for (let index = 0; index <cant; index++) {         
          this.priority_process_temp.push(array[index])
        }   
      }, 250);   
    },
    quickSort: array => {
      for (let i = 0; i < array.length; i++) {
        for (let j = i+1; j < array.length; j++) {
          if(array[i].total < array[j].total){
            [ array[i], array[j] ] = [ array[j], array[i] ];
          }
        }
      }
      return array;
    },
    go(id){
      location.href="/adjuntables/"+id
    },
    save(){
        this.load = true;
        console.log(this.priority_process_temp)
        if((this.priority_process_temp).length > 0)
          axios.post('/api/matriz-priorizacion', {
            unidad,
            matriz: JSON.stringify(this.PC),
            procesos: this.priority_process_temp
          }).then(({data}) => {
              Swal.fire('Exito!!', 'El registro fue exitoso', 'success');
              this.load = false;
          });
        else{
          this.load = false;
          Swal.fire('Error', 'Debe de llenar todos los campos', 'error');
        }
    },
    pre(){
      this.sortPriority(this.PC.cant)
    },
    handleVersion(obj){
      if(obj){
        this.refresh(JSON.parse(obj.priorizacion))
        this.editable = false;
      }
      else{
        this.refresh(this.table)
        this.editable = true
      }
    },
    deleteMatriz(){
      axios.delete(`/api/matriz-priorizacion/${unidad}`).then(()=>{
          Swal.fire('Éxito!', 'Los datos de priorización se eliminaron correctamente', 'success').then(()=>{
            location.reload()
          });
      });
    }
  }, 
  watch:{
    table(obj){
      this.refresh(obj)
    }
  },
  mounted(){
    this.refresh(this.table)
  }
}
</script>

<style>

</style>