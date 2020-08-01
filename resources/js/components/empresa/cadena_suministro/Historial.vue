<template>
   <b-list-group>
       <b-overlay :show="loading">
            <b-list-group-item v-for="historia in historias" :key="historia.id">
                <div class="row">
                    <b-avatar button @click="onClick(historia)" variant="success" :text="(historia.id)+''" class="align-baseline"></b-avatar>
                    <p class="ml-3">{{historia.created_at}}</p>
                    <p class="ml-3">"{{historia.comentario}}"</p>
                </div>
            </b-list-group-item>
            <!-- <b-list-group-item>
            <b-avatar button @click="onClick" src="https://placekitten.com/300/300"></b-avatar>
            Button Image Avatar
            </b-list-group-item>
            <b-list-group-item>
            <b-avatar button @click="onClick" icon="star-fill" class="align-center"></b-avatar>
            Button Icon Avatar
            </b-list-group-item> -->
       </b-overlay>
       <b-modal id="historia" :title="modal.title" size="lg" scrollable hide-footer>
            <div class="container">
                <componente-grafico-cadena :content="modal.content" :title="modal.title"/>
            </div>
        </b-modal>
  </b-list-group>
</template>

<script>
  export default {
    props:['unidad'],
    data() {
      return {
        loading: false,
        historias: [],
        modal:{
            title:"",
            content:{}

        }
      }
    },
    mounted(){
         this.loadHistory(this.unidad);
    },
    watch:{
        unidad(newValue){
            this.loadHistory(this.unidad);
        }
    },
    methods: {
        loadHistory(unidad){
            this.loading = true;
            axios.get(`/api/cadena-historial/${unidad}`).then( ({data}) => {
                let {historias} = data
                this.historias = historias;
            }).finally( () => {
                this.loading = false;
            });
        },
        onClick(item) {
            this.modal.title = `Historia - ${item.comentario} - ${(item.created_at).substring(0, 19)}`
            this.$root.$emit('bv::show::modal', 'historia')
            setTimeout(() => {
                this.modal.content = JSON.parse(item.contenido)
            }, 500)
            
        }
    }
  }
</script>