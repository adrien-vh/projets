<!--
 FICHE PHASE
-->
<template>
    <div class="fs-13">
      <!-- Données d'ordre général -->
      <div class="titre-formulaire">Nom de la phase :</div>
      <inText v-model="etape.nom" :editable="editable"></inText>
      <table class="form">
        <tr>
            <th>Début : </th>
            <th>Durée : </th>
        </tr>
        <tr>
            <td><inMonth v-model="etape.debut" :editable="editable"></inMonth></td>
            <td><inDuration v-model="etape.duree" :editable="editable" :defaultValue="1" :minValue="1"></inDuration></td>
        </tr>
        <tr>
            <th>Fin : </th>
            <th>Durée : </th>
        </tr>
        <tr>
            <td>{{ $formatDate($stepEndDate(etape)) }}</td>
            <td><inTypeEtape v-model="etape.num_typeEtape" :editable="editable"></inTypeEtape></td>
        </tr>
      </table>
      <div class="titre-formulaire ">Objectifs :</div>
      <inLongText v-model="etape.objectifs" :editable="editable"></inLongText>
      <div class="titre-formulaire ">Commentaires :</div>
      <inLongText v-model="etape.commentaires" :editable="editable"></inLongText>
      <!-- FIN Données d'ordre général -->
      
      <!-- Tableau des étapes / instances -->
      <div class="titre-formulaire ">Étapes / instances :</div>
      <div v-show="!editable && etape.instances.length === 0" class="fs-12">(Aucun enregistrement)</div>
      <table class="table table-hover table-sm table-striped infos" v-show="editable || etape.instances.length > 0">
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
          <tr v-for="m in etape.instances.length" :key="m">
            <td>
                <inText v-model="etape.instances[m-1].nom" :editable="editable"></inText>
            </td>
            <td>
              <inText v-model="etape.instances[m-1].dates" :editable="editable"></inText>
            </td>
            <td>
              <inText v-model="etape.instances[m-1].commentaires" :editable="editable"></inText>
            </td>
            <td>
              <listeFichiers :fichiers="etape.instances[m-1].fichiers" :editable="editable"></listeFichiers>
              <in-file v-show="editable" @add="ajoutFichierInstance($event, etape.instances[m-1])"></in-file>
            </td>
            <td v-show="editable"><button class="btn btn-danger btn-sm pointer mb10">Supprimer</button></td>
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
      <!-- FIN Tableau des étapes / instances -->

      <!-- Moment de validation lié à la phase -->      
      <div class="titre-formulaire ">Moment de validation :</div>
      <table class="table table-sm infos">
        <thead>
          <tr>
            <th>Intitulé</th>
            <th>Date</th>
            <th>Fichier joints</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <span v-show="!editable && etape.validationIntitule == ''"><em>(non renseigné)</em></span>
              <inText v-model="etape.validationIntitule" :editable="editable"></inText>
            </td>
            <td><inText v-model="etape.validationDate" :editable="editable"></inText></td>
            <td>
              <itemFichier :numFichier="etape.validationFichier" :editable="editable" @supprime="etape.validationFichier = -1"></itemFichier>
              <in-file v-show="editable && etape.validationFichier == -1" @add="ajoutValidationFichier($event)"></in-file>
              <!--<listeFichiers :fichiers="newInstance.fichiers"></listeFichiers>
              <in-file @add="ajoutFichierInstance($event, newInstance)"></in-file>-->
            </td>
          </tr>
        </tbody>
      </table>
      <!-- FIN Moment de validation lié à la phase -->      

      <!-- Tableaux des dépenses et recettes liées à la phase -->
      <div class="titre-formulaire ">Dépenses de fonctionnement (prestation, animation, expertise ...) :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'0'"
        :fonctionnement="'1'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>
      
      <div class="titre-formulaire ">Recettes de fonctionnement :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'1'"
        :fonctionnement="'1'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>

      <div class="titre-formulaire ">Dépenses d'investissement :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'0'"
        :fonctionnement="'0'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>

      <div class="titre-formulaire ">Recettes d'investissement :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'1'"
        :fonctionnement="'0'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>
      <!-- FIN Tableaux des dépenses et recettes liées à la phase -->
    </div>
</template>

<script>
  /**
  * @prop {Object}  [projet={}]       Projet auquel est rattaché la phase
  * @prop {Object}  etape             Phase concernée par la fiche
  * @prop {Boolean} [editable=false]  Contenu éditable ?
  */

  import inLongText from './inLongText'
  import inNumber from './inNumber'
  import inText from './inText'
  import listeTransactions from './listeTransactions'
  import inMonth from './inMonth'
  import inDuration from './inDuration'
  import inTypeEtape from './inTypeEtape'
  import inFile from './inFile'
  import itemFichier from './itemFichier'
  import listeFichiers from './listeFichiers'

  export default {
      components: { inLongText, inNumber, inText, listeTransactions, inMonth, inDuration, inTypeEtape, inFile, itemFichier, listeFichiers },
      props: { projet: { type: Object, default: {} }, etape: { type: Object }, editable: { type: Boolean, default: false }},
      data () {
        return {
          /* Modèle d'une nouvelle instance */
          newInstance : {
            nom: "",
            dates: "",
            commentaires: "",
            fichiers: []
          }
        }
      },
      methods: {
        /** 
        * Ajout d'une transaction (dépense / recette) à la phase
        * 
        * @param  {Object}  transaction    Nouvelle transaction à ajouter
        */
        sauveTransaction (transaction) {
          this.etape.transactions.push(transaction)
        },

        /** 
        * Ajout d'un nouveau fichier de validation
        * 
        * @param  {String}  num_fichier  Index du fichier à joindre
        */
        ajoutValidationFichier (num_fichier) {
          this.etape.validationFichier = num_fichier
        },

        /* Ajout d'une instance à la phase et réinitialisation de l'objet nouvelle instance */
        sauveInstance () {
          this.etape.instances.push($.extend({}, this.newInstance))
          this.newInstance = { nom: "", dates: "", commentaires: "" }
        },

        /** 
        * Ajout d'un fichier à une instance
        * 
        * @param  {String}  num_fichier Index du fichier à ajouter
        * @param  {Object}  instance    Instance à laquelle ajouter le fichier
        */
        ajoutFichierInstance (num_fichier, instance) {
          if (typeof instance.fichiers === 'undefined') {
            instance.fichiers = []
          }
          instance.fichiers.push(num_fichier)
        }
      }
  }
</script>

<style scoped lang="scss">
@import "../../styles/copic";
.titre-formulaire {
  color: $CB18;
  font-family: ibm-plex-serif;
  font-size: 13px;
  font-weight: bold;
}
</style>
