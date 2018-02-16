<template>
  <div>
      <span v-if="fichier">
        <a v-if="fichier.pdfExist" href="#" @click.prevent="montrePdf(fichier)">{{ fichier.nom }}</a>
        <span v-else>{{ fichier.nom }}</span>
        &nbsp;<a :href="fichierUrl(fichier)"><i class="fa fa-download" aria-hidden="true"></i></a>
        <a href="#" @click.prevent="supprime" class="txt-red" v-show="editable"><i class="fa fa-times fa-lg" aria-hidden="true"></i></a>
      </span>
  </div>
</template>

<script>
export default {
    props: {
        editable: { type: Boolean, default: true },
        numFichier: {},
    },
    data () {
      return { }
    },
    computed: {
      fichier () {
        return this.$store.state.infosFichiers[parseInt(this.numFichier)]
      }
    },
    methods: {
      fichierUrl () {
        return C.SERVER_URL + 'fichier.php?num_fichier=' + this.numFichier
      },
      montrePdf () {
        this.$showModal(
          this.fichier.nom,
          '<iframe src="' + C.SERVER_URL + 'pdf.php?num_fichier=' + this.numFichier + '"></iframe>',
          true,
          false
        )
      },
      supprime () {
        var me = this
        this.$showModal(
          'Confirmer la suppression',
          'Êtes-vous sûr de vouloir supprimer le fichier ' + this.fichier.nom,
          false,
          true,
          function () { me.$emit('supprime') }
        )
      }
    }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
</style>
