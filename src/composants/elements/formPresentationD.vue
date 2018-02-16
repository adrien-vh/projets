<template>
    <div>
      <table class="form">
        <tr>
          <th colspan="2">Objectifs et enjeux du projet :</th>
        </tr>
        <tr>
          <td colspan="2">
            <inLongText v-model="projet.objectifs" placeholder="Description du projet" :editable="editable"></inLongText>
          </td>
        </tr>
        <tr>
          <th colspan="2">Planning détaillé du projet :</th>
        </tr>
      </table>      
      <div v-show="!editable && instances.length === 0" class="fs-12">(Aucun enregistrement)</div>
      <table class="table table-hover table-sm table-striped infos" v-show="editable || instances.length > 0">
        <thead>
          <tr>
            <th>Étapes / instance</th>
            <th>Dates</th>
            <th>Commentaires</th>
            <th>Fichiers</th>
            <th v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="m in instances.length" :key="m">
            <td>
                <inText v-model="instances[m-1].nom" :editable="editable && instances[m-1].num_projet == numProjet"></inText>
            </td>
            <td>
              <inText v-model="instances[m-1].dates" :editable="editable && instances[m-1].num_projet == numProjet"></inText>
            </td>
            <td>
              <inText v-model="instances[m-1].commentaires" :editable="editable && instances[m-1].num_projet == numProjet"></inText>
            </td>
            <td>
              <listeFichiers :fichiers="instances[m-1].fichiers"></listeFichiers>
              <in-file v-show="editable" @add="ajoutFichierInstance($event, instances[m-1])"></in-file>
            </td>
            <td v-show="editable"><button v-show="instances[m-1].num_projet == numProjet" class="btn btn-danger btn-sm pointer mb10">Supprimer</button></td>
          </tr>
          <tr v-show="editable">
            <td>
              <inText v-model="newInstance.nom" :editable="true"></inText>
            </td>
            <td>
              <inText v-model="newInstance.dates" :editable="true"></inText>
            </td>
            <td>
              <inText v-model="newInstance.commentaires" :editable="true"></inText>
            </td>
            <td>
              <listeFichiers :fichiers="newInstance.fichiers"></listeFichiers>
              <in-file @add="ajoutFichierInstance($event, newInstance)"></in-file>
            </td>
            <td class="txt-center">
              <button class="btn btn-primary btn-sm pointer mb10" @click="sauveInstance">Valider</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
</template>

<script>
import inLongText from "../elements/inLongText";
import inNumber from "../elements/inNumber";
import inText from "../elements/inText";
import inFile from "../elements/inFile";
import listeFichiers from "../elements/listeFichiers";

export default {
  components: { inLongText, inNumber, inText, inFile, listeFichiers },
  data () {
    return {
      newInstance : { nom: "", dates: "", commentaires: "", fichiers: [] }
    }
  },
  props: {
    projet: { type: Object, default: {} },
    editable: { type: Boolean, default: false },
    instances: { type: Array, default: [] },
    numProjet: { type: String, default: "" }
  },
  methods: {
    sauveInstance () {
      this.instances.push($.extend({}, this.newInstance))
      this.newInstance = { nom: "", dates: "", commentaires: "" }
    },
    ajoutFichierInstance (fichier, instance) {
      if (typeof instance.fichiers === 'undefined') {
        instance.fichiers = []
      }
      instance.fichiers.push(fichier)
    }
  }
};
</script>

<style scoped lang="scss">

</style>
