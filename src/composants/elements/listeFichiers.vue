<template>
  <div>
    <span v-for="num_fichier in fichiers" :key="num_fichier">
      <itemFichier :numFichier="num_fichier" :editable="editable"></itemFichier>
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
    methods: { }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
</style>
