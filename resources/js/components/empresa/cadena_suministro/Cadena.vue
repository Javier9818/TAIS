<template>
  <div class="row justify-content-center">
      <div id="myDiagramDiv" style="border: solid 1px grey; width: 90%; height: 600px"></div>
  </div>
</template>

<script>
  export default {
    props:['content'],
    data() {
      return {
      }
    },
    methods: {
     
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
