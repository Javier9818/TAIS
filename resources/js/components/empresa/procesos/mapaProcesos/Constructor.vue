<template>
  <div>
    <div class="row mb-2">
        <div class="col-6">
            <b-form-group label="MegaProcesos">
                <b-form-radio-group
                    id="radio-group-1"
                    v-model="megaProcesoSelected"
                    :options="megaProcesos"
                    name="radio-options"
                    @change="loadData"
                ></b-form-radio-group>
            </b-form-group>
            <b-form-group label="Procesos">
                <b-overlay :show="loadingProcesos" class="d-inline-block">
                    <b-form-select 
                        v-model="procesoSelected" 
                        :options="procesos"
                        value-field="id"
                        text-field="nombre"
                        @change="loadSubprocesos"
                        required
                        >
                        <template v-slot:first>
                            <b-form-select-option :value="null" disabled>-- Porfavor selecciona una opción --</b-form-select-option>
                        </template>
                    </b-form-select>
                </b-overlay>
            </b-form-group>
            
        </div>
    </div>
    <div class="row justify-content-end mr-2 b-2" v-if="verify.length > 0">
        <button class="btn btn-sm btn-success" @click="saveModel"><i class="fas fa-save"></i> Guardar</button>
    </div>
     <div class="row justify-content-center">
         <div id="myPaletteDiv" class="d-none"></div>
         <b-overlay :show="loadingMapa" style="width: 100%; height: 600px;">
             <div id="mapaConstructor" style="border: solid 1px grey; width: 100%; height: 600px; background-color: #282c34;"></div>
         </b-overlay>
    </div>
     
  </div>
</template>

<script>
  import jsPDF from 'jspdf';
  import Swal from 'sweetalert2'
  export default {
    props:['title'],
    data() {
      return {
        procesos:[],
        subprocesos:[],
        procesoSelected:null,
        megaProcesoSelected:null,
        maestro:null,
        loadingProcesos:false,
        loadingMapa:false,
        verify:[],
        megaProcesos:[
            {text:"Procesos Estratégicos", value: -1},
            {text:"Procesos Primarios", value: -2},
            {text:"Procesos de Apoyo", value: -3}
        ],
        content:{ "class": "go.GraphLinksModel",
            "linkFromPortIdProperty": "fromPort",
            "linkToPortIdProperty": "toPort",
            "nodeDataArray": [],
            "linkDataArray": []
        },
        image: null
      }
    },
    methods: {
        saveModel(){
            this.loadingMapa = true;
            let obj = JSON.parse(myDiagram.model.toJson());
            axios.post(`/api/mapa-proceso`, {
                nodes: obj.nodeDataArray,
                links: obj.linkDataArray,
                objeto: myDiagram.model.toJson(),
                unidad,
                maestro: this.maestro
            }).then(()=>{
                this.loadingMapa = false;
                Swal.fire('Exito!', 'El diagrama se registró correctamente', 'success');
            });
            console.log(JSON.parse(myDiagram.model.toJson()));
        },
        loadSubprocesos(id){
            this.loadingMapa = true;
            this.maestro = id;
            let sub_procesos = [], enlaces_ = [];
            axios.get(`/api/mapa-proceso/subproceso/${unidad}/${id}`).then(({data}) => {
                let {subprocesos} = data;
                this.subprocesos = subprocesos;
                sub_procesos = data.graph ? JSON.parse(data.graph.objeto).nodeDataArray : [];
                enlaces_ = data.graph ? JSON.parse(data.graph.objeto).linkDataArray : [];
                subprocesos.forEach(sub => {
                    sub_procesos.push({
                        text: sub.nombre,
                        key: sub.id
                    });
                });
            }).then(()=>{
                this.loadingMapa = false;
                this.verify = sub_procesos;
                loadBuild({ "class": "go.GraphLinksModel",
                    "linkFromPortIdProperty": "fromPort",
                    "linkToPortIdProperty": "toPort",
                    "nodeDataArray": sub_procesos,
                    "linkDataArray": enlaces_
                });
            });
        },
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
       loadData(id){
           this.loadingMapa = true;
           this.maestro = id;
           let procesos_ = [];
           let enlaces_ = [];
           this.loadingProcesos = true
           this.procesoSelected = null
           axios.get(`/api/proceso-unique/${unidad}/${id}`).then(({data})=>{
               this.procesos = data.procesos;
               this.verify = data.procesos;
               procesos_ = data.graph ? JSON.parse(data.graph.objeto).nodeDataArray : [];
               enlaces_ = data.graph ? JSON.parse(data.graph.objeto).linkDataArray : [];
               data.procesos_graph.forEach(sub => {
                    procesos_.push({
                        text: sub.nombre,
                        key: sub.id,
                        category:"proceso"
                    });
                });
           }).finally(()=>{
               this.loadingMapa = false;
               this.loadingProcesos = false;
               loadBuild({ "class": "go.GraphLinksModel",
                    "linkFromPortIdProperty": "fromPort",
                    "linkToPortIdProperty": "toPort",
                    "nodeDataArray": procesos_,
                    "linkDataArray": enlaces_
                });
           });
         //loadBuild(json);   
        }
    },
    mounted(){
      initBuild(this.content);
      //this.loadData();
    },
    watch:{
      content: function(newValue){
          console.log(newValue)
            load (newValue);
      },
      meta: function(newValue){
        console.log(newValue)
      }
    }
  }
</script>
