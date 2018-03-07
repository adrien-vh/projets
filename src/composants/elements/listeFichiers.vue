<template>
  <div>
    <span v-for="num_fichier in fichiers" :key="num_fichier">
      <itemFichier :numFichier="num_fichier" :editable="editable" @supprime="supprime(num_fichier)"></itemFichier>
    </span>
  </div>
</template>

<script>
import itemFichier from './itemFichier'

export default {
    components: { itemFichier },
    props: {
        editable: Boolean,
        fichiers: Array
    },
    data () {
      return { }
    },
    methods: {
      supprime(num_fichier) {
        var i
        for (i = 0; i < this.fichiers.length; i += 1) {
          if (this.fichiers[i]) {
            this.fichiers.splice(i, 1)
            return
          }
        }
      }
    }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
</style>
