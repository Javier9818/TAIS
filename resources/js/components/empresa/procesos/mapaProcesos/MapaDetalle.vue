<template>
  <div>
    <div class="row">
        <div class="col-4">
            <select-version @chagueVersion='handleVersion'></select-version>
        </div>
    </div>
    <div class="row justify-content-end mr-5 mb-2">
        <div class="col-4" v-if="version_act === true">
          <input type="text" placeholder="Guardar versión" v-model="version">
          <button class="btn btn-sm btn-warning" @click="submitVersion">Registrar</button>
        </div>
        <div class="col-2">
            <button class="btn btn-sm btn-danger" @click="pdfGenerate"><i class="fas fa-file-pdf"></i> PDF</button>
            <button class="btn btn-sm btn-success" @click="imageGenerate"><i class="fas fa-image"></i> PNG</button>
        </div>
    </div>
     <div class="row justify-content-center">
        <div id="mapaDetalleDiv" style="border: solid 1px grey; width: 90%; height: 600px"></div>
    </div>
  </div>
</template>

<script>
  import jsPDF from 'jspdf';
import Swal from 'sweetalert2';
  export default {
    props:['title'],
    data() {
      return {
        version:'',
        version_act:true,
        nodeDataArray: [],
        linkDataArray: [],
        image: null
      }
    },
    methods: {
      imageGenerate(){
        var imagenImp = myDiagram.makeImage({
            size : new go.Size (842,595)
        })
        var a = document.createElement('a');
        a.download = `Mapa de procesos ${this.version_act === true ? '- Version actual':'-'+ this.version_act.descripcion}`;
        a.target = '_blank';
        a.href= imagenImp.src;
        a.click();
      },
      pdfGenerate(){
        var imagenImp = myDiagram.makeImage({
            size : new go.Size (842,595)
        })
        var doc = new jsPDF('l', 'pt', 'a4');
        var largo = doc.internal.pageSize.getWidth();
        var alto = doc.internal.pageSize.getHeight();
        doc.setFontType('bold');
        doc.setFontSize(16);
        doc.text(largo/3+17, 45, `Mapa de procesos ${this.version_act === true ? '- Version actual':'-'+ this.version_act.descripcion}`);
        doc.addImage(imagenImp, 'jpg', 30, 90, 742, 495);
        doc.save(`${this.title}`);
      },
      loadData(){
            this.nodeDataArray = [
                { key: -1, text: "Procesos Estratégicos", color: "black", isGroup: true },
                { key: -5, text: "", color: "white", isGroup: true },
                { key: -10, text: "", color: "white", isGroup: true },
                
                { key: -2, text: "Procesos Primarios", color: "black", isGroup: true },
                { key: -20, text: "", color: "white", isGroup: true },
                { key: -30, text: "", color: "white", isGroup: true },

                { key: -3, text: "Procesos de Apoyo", color: "black", isGroup: true },
                { key: -40, text: "", color: "white", isGroup: true },
                { key: -50, text: "", color: "white", isGroup: true },
                
            ];
             axios.get(`/api/procesos-graph/${unidad}`).then(({data}) => {
                let {groups, nodos} = data
                this.linkDataArray = data.links
                groups.forEach( (item) => {
                    switch (item.megaproceso_id) {
                        case 1:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"blue",
                                group:-1,
                                isGroup: true
                            })
                            break;
                        case 2:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"#2b48b3",
                                group:-2,
                                isGroup: true
                            })
                            break;
                        case 3:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"grey",
                                group:-3,
                                isGroup: true
                            })
                            break;
                    }
                    
                });

                 nodos.forEach( (item) => {
                    switch (item.megaproceso_id) {
                        case 1:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"lightblue",
                                group:-1,
                            })
                            break;
                        case 2:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"lightgreen",
                                group:-2,
                            })
                            break;
                        case 3:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"yellow",
                                group:-3,
                            })
                            break;
                        default:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"pink",
                                group:item.proceso_padre,
                            })
                          break;
                    }
                    
                });

            }).then(()=>{
                initDetalle(this.nodeDataArray, this.linkDataArray);
            });
        },
      submitVersion(){
          if(this.version !== '')
            axios.post(`/api/version`,{
              unidad, 
              version: this.version,
              mapaProceso: JSON.stringify({
                nodeDataArray: this.nodeDataArray,
                linkDataArray: this.linkDataArray
              })
            }).then( ({data}) => {
              if(data.error)
                Swal.fire('Error!', data.message, 'error');
              else
                Swal.fire('Exito!', 'La versión de la gestión de procesos se registró correctamente', 'success');
              this.version = ''
            });
          else
            Swal.fire('Error!', 'Ingrese una descripción de la versión', 'error');
        },
      handleVersion(obj){
        if(obj !== undefined){
          let data = JSON.parse(obj.mapa_proceso)
          this.version_act = obj
          updateDetalle(data.nodeDataArray, data.linkDataArray);
        }else{
          updateDetalle(this.nodeDataArray, this.linkDataArray);
          this.version_act = true;
        } 

          
      }
    },
    mounted(){
      //initDetalle(this.nodeDataArray, this.linkDataArray);
      this.loadData();
    },
    watch:{
      content: function(newValue){
          console.log(newValue)
        load(newValue);
      },
      meta: function(newValue){
        console.log(newValue)
      }
    }
  }
</script>
