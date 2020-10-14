<template>
    <section>
      <div class="row justify-content-end mb-2">
        <div class="col-2">
            <button class="btn btn-sm btn-danger" @click="pdfGenerate"><i class="fas fa-file-pdf"></i> PDF</button>
            <button class="btn btn-sm btn-success" @click="imageGenerate"><i class="fas fa-image"></i> PNG</button>
        </div>
    </div>
     <div class="row justify-content-center">
       <div id="divMap" style="border: solid 1px black; width:100%; height:700px;"></div>
    </div>
        
    </section>
</template>

<script>
import jsPDF from 'jspdf';
  export default {
    props:['objetivos', 'perspectivas'],
    data() {
      return {
          nodeDataArray: [ { key: "Pool1", text: "Pool", isGroup: true, category: "Pool" },
          { key: "Pool2", text: "Pool2", isGroup: true, category: "Pool" },
          { key: "Lane1", text: "Lane1", isGroup: true, group: "Pool1", color: "lightblue" },
          { key: "Lane2", text: "Lane2", isGroup: true, group: "Pool1", color: "lightgreen" },
          { key: "Lane3", text: "Lane3", isGroup: true, group: "Pool1", color: "lightyellow" },
          { key: "Lane4", text: "Lane4", isGroup: true, group: "Pool1", color: "orange" },
          { key: "oneA", group: "Lane1" },
          { key: "oneB", group: "Lane1" },
          { key: "oneC", group: "Lane1" },
          { key: "oneD", group: "Lane1" },
          { key: "twoA", group: "Lane2" },
          { key: "twoB", group: "Lane2" },
          { key: "twoC", group: "Lane2" },
          { key: "twoD", group: "Lane2" },
          { key: "twoE", group: "Lane2" },
          { key: "twoF", group: "Lane2" },
          { key: "twoG", group: "Lane2" },
          { key: "fourA", group: "Lane4" },
          { key: "fourB", group: "Lane4" },
          { key: "fourC", group: "Lane4" },
          { key: "fourD", group: "Lane4" },
          { key: "Lane5", text: "Lane5", isGroup: true, group: "Pool2", color: "lightyellow" },
          { key: "Lane6", text: "Lane6", isGroup: true, group: "Pool2", color: "lightgreen" },
          { key: "fiveA", group: "Lane5" },
          { key: "sixA", group: "Lane6" }],
          linkDataArray: [{ from: "oneA", to: "oneB" },
          { from: "oneA", to: "oneC" },
          { from: "oneB", to: "oneD" },
          { from: "oneC", to: "oneD" },
          { from: "twoA", to: "twoB" },
          { from: "twoA", to: "twoC" },
          { from: "twoA", to: "twoF" },
          { from: "twoB", to: "twoD" },
          { from: "twoC", to: "twoD" },
          { from: "twoD", to: "twoG" },
          { from: "twoE", to: "twoG" },
          { from: "twoF", to: "twoG" },
          { from: "fourA", to: "fourB" },
          { from: "fourB", to: "fourC" },
          { from: "fourC", to: "fourD" }]
      }
    },
    methods:{
        imageGenerate(){
          var imagenImp = myDiagram.makeImage({
              size : new go.Size (842,595)
          })
          var a = document.createElement('a');
          a.download = `Mapa estratégico`;
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
          doc.text(largo/3+17, 45, `Mapa estratégico`);
          doc.addImage(imagenImp, 'jpg', 30, 90, 742, 495);
          doc.save(`${this.title}`);
        },
        loadData(){
          let { objetivos, perspectivas} = this
          let nodeDataArray = [{ key: "grupomaster", text: "Mapa Estratégico", isGroup: true, category: "Pool" }]
          let linkDataArray = []

          for (let index = perspectivas.length-1; index >= 0; index--) {
            nodeDataArray.push({ key: (index + 1)*-1, text: perspectivas[index], isGroup: true, group: "grupomaster", color: "lightblue" })
          }

          for (let index = objetivos.length-1; index >= 0; index--) {
             nodeDataArray.push({ key: objetivos[index].id, group: (objetivos[index].perspectiva + 1)  * -1, text: objetivos[index].nombre})
          }
          
          objetivos.forEach((e) => {
            // nodeDataArray.push({ key: e.id, group: (e.perspectiva + 1 ) * -1, text: e.nombre})
            e.efecto.forEach(ef => {
              linkDataArray.push({ from: e.id, to: ef.id },);
            })
          });


          setTimeout(() => {
            init(nodeDataArray,linkDataArray)
            console.log(nodeDataArray)
            console.log(linkDataArray)
          }, 500);

        }
    },
    mounted(){
        //init(this.nodeDataArray, this.linkDataArray);
        this.loadData();
    }
  }
</script>
