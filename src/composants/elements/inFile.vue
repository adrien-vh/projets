<template>
  <div>
    <a href="#" :id="id"><i class="fa fa-plus" aria-hidden="true"></i> Fichier</a>
  </div>
</template>

<script>

export default {
  data () {
    return {
      id: "bu" + parseInt(1000000 * Math.random())
    }
  },
  watch: { },
  props: {},
  computed: { },
  methods: {  },
  mounted () {
    var me = this, uploader = new ss.SimpleUpload({
      button: this.id,
      url: C.SERVER_URL + C.UPLOAD, // server side handler
      responseType: 'json',
      name: C.FICHIER_UPLOAD,
      dropzone: this.id,
      multiple: true,

      onComplete:   function(filename, response) {
        me.$store.commit('addInfoFichier', {
          num_fichier: response.num_fichier,
          nom: response.nom_fichier,
          nomFS: response.nom_fichier_fs,
          ext: response.ext,
          pdfExist: false
        })

        me.$emit("add", response.num_fichier)
        /*console.log(response)
          if (!response) {
            alert(filename + 'upload failed');
            return false;
          }*/
      }
    });
  }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
</style>
