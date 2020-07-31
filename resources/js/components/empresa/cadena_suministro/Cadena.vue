<template>
  <div>
    <div class="row justify-content-end mr-5">
        <button class="btn btn-sm btn-danger" @click="pdfGenerate"><i class="fas fa-file-pdf"></i> PDF</button>
        <button class="btn btn-sm btn-success" @click="imageGenerate"><i class="fas fa-image"></i> PNG</button>
    </div>
     <div class="row justify-content-center">
        <div id="myDiagramDiv" style="border: solid 1px grey; width: 90%; height: 600px"></div>
    </div>
  </div>
</template>

<script>
  import jsPDF from 'jspdf';
  export default {
    props:['content'],
    data() {
      return {
        image: null
      }
    },
    methods: {
      imageGenerate(){
        var imagenImp = myDiagram.makeImage({
            size : new go.Size (842,595)
        })
        var a = document.createElement('a');
        a.download = 'Cadena de suministro';
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
        doc.text(largo/3+17, 45, "Mapa de Cadena de Suministros");
        doc.addImage(imagenImp, 'jpg', 30, 90, 742, 495);
        doc.save("Reporte-1.pdf");
      }
    },
    mounted(){
      init(this.content);
    },
    watch:{
      content: function(newValue){
        load(newValue);
      }
    }
  }
</script>
