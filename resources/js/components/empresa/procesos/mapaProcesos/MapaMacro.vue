<template>
  <div>
    <div class="row justify-content-end mr-5">
        <button class="btn btn-sm btn-danger" @click="pdfGenerate"><i class="fas fa-file-pdf"></i> PDF</button>
        <button class="btn btn-sm btn-success" @click="imageGenerate"><i class="fas fa-image"></i> PNG</button>
    </div>
     <div class="row justify-content-center">
        <div id="mapaDetalleDiv" style="border: solid 1px grey; width: 90%; height: 600px"></div>
    </div>
  </div>
</template>

<script>
  import jsPDF from 'jspdf';
  export default {
    props:['title'],
    data() {
      return {
        nodeDataArray: [{ key: 1, text: "Alpha", color: "lightblue" },
        { key: 2, text: "Beta", color: "orange" },
        { key: 3, text: "Gamma", color: "lightgreen", group: 5 },
        { key: 4, text: "Delta", color: "green", group: 5, isGroup: true },
        { key: 5, text: "Epsilon", color: "green", isGroup: true },
        { key: 6, text: "Brice", color: "orange", group:4 }],
        linkDataArray: [
        // { from: 1, to: 2, color: "blue" },
        // { from: 2, to: 2 },
        // { from: 3, to: 4, color: "green" },
        // { from: 3, to: 1, color: "purple" }
        ],
        image: null
      }
    },
    methods: {
      imageGenerate(){
        var imagenImp = myDiagram.makeImage({
            size : new go.Size (842,595)
        })
        var a = document.createElement('a');
        a.download = `Mapa de procesos`;
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
        doc.text(largo/3+17, 45, `${this.title}`);
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
             axios.get(`/api/proceso/${unidad}`).then(({data}) => {
                let {procesos} = data
                procesos.forEach( (item) => {
                    switch (item.megaproceso_id) {
                        case 1:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"lightblue",
                                group:-1
                            })
                            break;
                        case 2:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"lightgreen",
                                group:-2
                            })
                            break;
                        case 3:
                            this.nodeDataArray.push({
                                key:item.id,
                                text:item.nombre,
                                color:"yellow",
                                group:-3
                            })
                            break;
                    }
                    
                });
            }).then(()=>{
                initDetalle(this.nodeDataArray, this.linkDataArray);
            });
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
