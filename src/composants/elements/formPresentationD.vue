<!--
  FORMULAIRE DE PRÉSENTATION DÉTAILLÉE D'UNE FICHE PROJET
-->
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
      </table>      
    </div>
</template>

<script>
  /**
  * @prop {Object}  [projet={}]       Projet concerné
  * @prop {Boolean} [editable=false]  Contenu éditable ?
  */

  import inLongText from "../elements/inLongText";
  import inNumber from "../elements/inNumber";
  import inText from "../elements/inText";
  import inFile from "../elements/inFile";
  import listeFichiers from "../elements/listeFichiers";

  export default {
    components: { inLongText, inNumber, inText, inFile, listeFichiers },
    props: {
      projet: { type: Object, default: {} },
      editable: { type: Boolean, default: false }
    }
  };
</script>

<style scoped lang="scss"></style>
