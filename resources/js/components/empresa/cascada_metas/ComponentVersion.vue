<template>
  <div>
        <div class="row">
            <div class="col-6">
                <form action="/version" method="POST" id="formVersion">
                  <select name="cbVersion" id="cbVersion" class="form-control" @change="submit" v-model="version">
                      <option v-for="(version_, index) in versiones" :value="version_.id" :key="index"> {{version_.id === 1 ? 'Versión actual' : getVersion(version_.id)}}</option>
                  </select>
                </form>
            </div>

            <div class="col-6">
                <b>Descripción:</b>
                <p>{{version === 1 ? 'Versión actual' : getVersion(version) }}</p>
            </div>
        </div>
        <hr>
  </div>
</template>

<script>
  export default {
    data() {
      return {
          versiones: [],
          version: 1
      }
    },
    methods: {
      submit(){
          axios.post('/version', { version: this.version}, {
            headers: {
              'X-CSRF-TOKEN': document.querySelector("meta[name='csrf-token']").getAttribute("content")
            }
          }).then(() => {
            location.reload()
          });
          // document.getElementById('formVersion').submit();
      },
      readCookie(name) {
      var nameEQ = name + "="; 
      var ca = document.cookie.split(';');

      for(var i=0;i < ca.length;i++) {

        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) {
          return decodeURIComponent( c.substring(nameEQ.length,c.length) );
        }

      }

      return null;

    },
    getVersion(version_){ 
       var pos = versiones.map(function(e) { return e.id; }).indexOf(parseInt(version_));
       return versiones[pos].descripcion
    }
    },
    mounted(){
        this.versiones = versiones
        this.version = version || 1
    }
  }
</script>
