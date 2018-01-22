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
            <th v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="instance in instances">
            <td>
                <inText v-model="instance.nom" :editable="editable && instance.num_projet == numProjet"></inText>
            </td>
            <td>
              <inText v-model="instance.dates" :editable="editable && instance.num_projet == numProjet"></inText>
            </td>
            <td>
              <inText v-model="instance.commentaires" :editable="editable && instance.num_projet == numProjet"></inText>
            </td>
            <td v-show="editable"><button v-show="instance.num_projet == numProjet" class="btn btn-danger btn-sm pointer mb10">Supprimer</button></td>
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

export default {
  components: { inLongText, inNumber, inText },
  data () {
    return {
      newInstance : { nom: "", dates: "", commentaires: "" }
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
    }
  }
};
</script>

<style scoped lang="scss">

</style>
