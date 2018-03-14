<!--
  FORMULAIRE CALENDRIER D'UNE FICHE PROJET (TABLEAU & GANTT)
-->
<template>
  <div>
      <!-- Météo du projet -->
      <span class="fs-12 ml20 ib">Retard : </span>
      <b>{{ delta }} mois</b>
      <img v-if="delta < 2" src="../../assets/images/sun.png">
      <img v-else-if="delta < 3" src="../../assets/images/cloud.png">
      <img v-else-if="delta < 6" src="../../assets/images/rain.png">
      <img v-else src="../../assets/images/storm.png">
      <span class="fs-12">
        ( <b>{{ $formatDate(initialEndDate) }}</b>
        <i class="fa fa-arrow-right" aria-hidden="true"></i>
        <b>{{ $formatDate(realEndDate) }}</b>)
      </span>
      <!-- FIN Météo du projet -->

      <!-- Tableau des phases du projet -->
      <table class="table table-hover table-sm table-striped infos">
        <thead class="">
          <tr>
            <th class="w20p"></th>
            <th class="w20p"></th>
            <th>Étape</th>
            <th class="w100p">Début</th>
            <th>Fin</th>
            <th class="w100p">Durée</th>
            <th class="w150p">Type</th>
            <th class="w50p" v-show="editable"></th>
          </tr>
        </thead>
        <tbody>

          <!-- Phases existantes -->
          <tr v-for="m in etapes.length" :key="m">
            <td class="txt-center"><a href="#" @click.prevent="selectStep(etapes[m-1])"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></a></td>
            <td><i class="fa fa-check fa-lg txt-green" aria-hidden="true" v-show="etapes[m-1].validationFichier != '-1'"></i></td>
            <td><inText v-model="etapes[m-1].nom" :editable="editable"></inText></td>
            <td><inMonth v-model="etapes[m-1].debut" :editable="editable" @input="updateGanttDiag"></inMonth></td>
            <td v-html="$formatDate($stepEndDate(etapes[m-1]))"></td>
            <td><inDuration v-model="etapes[m-1].duree" :editable="editable" @input="updateGanttDiag"></inDuration></td>
            <td>
              <inChoixMultiple
                  v-model="etapes[m-1].num_typeEtape"
                  :listeElements="$store.state.typesEtapes"
                  :champValeur="'num_typeEtape'"
                  :champLabel="'intitule'"
                  :champCouleur="'couleur'"
                  :editable="editable"
              ></inChoixMultiple>
            </td>
            <td v-show="editable">
              <button 
                v-show="etapes[m-1].num_projet == numProjet"
                class="btn btn-danger btn-sm pointer mb10"
                @click="supprimeEtape(m-1)"
              >
                Supprimer
              </button>
            </td>
          </tr>
          <!-- FIN Phases existantes -->

          <!-- Nouvelle phase -->
          <tr v-show="editable">
            <td></td>
            <td></td>
            <td><inText v-model="newStep.nom" :editable="editable"></inText></td>
            <td><inMonth v-model="newStep.debut" :editable="editable"></inMonth></td>
            <td>{{ $formatDate($stepEndDate(newStep)) }}</td>
            <td><inDuration v-model="newStep.duree" :editable="editable" :defaultValue="1" :minValue="1"></inDuration></td>
            <td>
              <inChoixMultiple
                  v-model="newStep.num_typeEtape"
                  :listeElements="$store.state.typesEtapes"
                  :champValeur="'num_typeEtape'"
                  :champLabel="'intitule'"
                  :champCouleur="'couleur'"
                  :editable="editable"
              ></inChoixMultiple>
            </td>
            <td>
              <button class="btn btn-primary btn-sm pointer mb10" @click="addStep">Valider</button>
            </td>
          </tr>
          <!-- FIN Nouvelle phase -->

        </tbody>
      </table>
      <!-- FIN Tableau des phases du projet -->

      <!-- Gantt -->
      <gantt v-model="etapes" :update="updateGantt" :editable="editable"></gantt>
      <!-- FIN Gantt -->
    </div>
</template>

<script>
  /**
  * @prop {String}  numProjet         Index du projet concerné
  * @prop {Array}   etapes            Listes des phases
  * @prop {Boolean} [editable=false]  Contenu éditable ?
  */

  import inMonth from './inMonth'
  import inText from './inText'
  import inDuration from './inDuration'
  import inTypeEtape from './inTypeEtape'
  import inChoixMultiple from './inChoixMultiple'
  import gantt from './gantt'

  /* Modèle de nouvelle étape */
  let modeleNouvelleEtape = {
      debut: moment("01" + moment().add(1, "month").format("MMYY"), "DDMMYY"),
      duree: moment.duration(1, "months"),
      transactions: [],
      instances: [],
      num_typeEtape: '1',
      validationFichier: '-1'
    }

  export default {
      data () {
        return {
          updateGantt: true,  // Trigger pour la mise à jour du Gantt
          newStep : $.extend({}, modeleNouvelleEtape) // Nouvelle étape
        }
      },
      components: { inMonth, inDuration, inText, gantt, inTypeEtape, inChoixMultiple },
      props: { numProjet: { type: String }, etapes: { type: Array }, editable: { type: Boolean, default: false } },
      watch: {
        /* Mise à jour du Gantt lors de la modification de la liste des étapes */
        etapes () { this.updateGantt = !this.updateGantt }
      },
      computed: {

        /** @return  {moment}  Date de fin initialement prévue du projet */
        initialEndDate () {
          var momentEnd = this.etapes.length > 0 ? this.endDateInitial(this.etapes[0]) : moment(), i
          for (i = 0; i < this.etapes.length; i += 1) {
            if (this.endDateInitial(this.etapes[i]).isAfter(momentEnd)) {
              momentEnd = this.endDateInitial(this.etapes[i])
            }
          }
          return moment(momentEnd).add(1, 'months')
        },

        /** @return  {moment}  Date de fin réelle du projet */
        realEndDate () {
          var momentEnd = this.etapes.length > 0 ? this.$stepEndDate(this.etapes[0]) : moment(), i
          for (i = 0; i < this.etapes.length; i += 1) {
            if (this.$stepEndDate(this.etapes[i]).isAfter(momentEnd)) {
              momentEnd = this.$stepEndDate(this.etapes[i])
            }
          }
          return moment(momentEnd).add(1, 'months')
        },

        /** @return  {int}  Décalage temporel du projet en mois */
        delta () {
          var delta = this.realEndDate.diff(this.initialEndDate, 'months')
          this.$emit("update", delta)
          return delta
        }
      },
      methods: {
        
        /** 
        * Calcul de la fin initialement prévue d'une étape
        * 
        * @param  {Object}  step    Étape
        *
        * @return {moment}  Date de fin de l'étape
        */
        endDateInitial (step) { return moment(step.debutInitial).add(step.dureeInitial).subtract(1, "d") },

        /** 
        * Suppression d'une phase
        * 
        * @param  {int}  index    Index de la phase dans le tableau
        */
        supprimeEtape (index) { this.etapes.splice(index, 1) },

        /* Ajout d'une étape */
        addStep () {
          this.newStep.debutInitial = this.newStep.debut
          this.newStep.dureeInitial = this.newStep.duree
          this.newStep.new = true
          this.etapes.push( $.extend({}, this.newStep)) 

          this.newStep = $.extend({}, modeleNouvelleEtape)
        },

        /* Émission de l'évènement de sélection d'une étape pour l'affichage */
        selectStep (step) { this.$emit("select", step) },

        /* Mise à jour du Gantt */
        updateGanttDiag () { this.updateGantt = !this.updateGantt }
      }
  }
</script>

<style scoped lang="scss">
</style>
