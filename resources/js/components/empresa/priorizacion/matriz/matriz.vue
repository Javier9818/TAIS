<template>
  <div>        
    <matriz-table :table="PC" :editable="true"></matriz-table>
  </div>
</template>

<script>
export default {
  data() {
    return {
      
      PC:{
         process:[],
         criterio:[],
         items:[],
         cant:0
      },
    }
  },
  mounted()
  {
    this.getData();
     
  },
  methods:{
    getData: async function(){
      let matriz_data = null;
      await axios.get(`/api/criterios-matriz/${unidad}`).then(({data}) => {
        let {procesos, criterios, matriz} = data;
        if(matriz !== null){
            this.PC = JSON.parse(matriz);
            matriz_data = matriz;
        }else{
          this.PC.process = procesos;
          this.PC.criterio = criterios;
        }
      }).then(() => {
        if(matriz_data == null)
        this.PC.criterio.forEach(({escala}, index) => {
          let escala_c = [];
          escala.split(',').forEach(e => {
            escala_c.push({
              id: e.split('-')[0],
              puntaje: e.split('-')[2], 
              descripcion: e.split('-')[1]
            })
          });
          this.PC.criterio[index].escala = escala_c;
        })
      }).then(() => {
          let items=[]
        if(matriz_data == null){
          this.PC.criterio.forEach(criterio => {
            this.PC.process.forEach(process => {
            items.push(
                {            
                  idcriterio:(criterio.idcriterio),
                  idprocess:(process.idprocess),
                  point: null,
                  scales:criterio.escala
                }
              ) 
            })
          })    
          this.PC.items=items   
        }
      });
    }
  }
}
</script>

<style>

</style>