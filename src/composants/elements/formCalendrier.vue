<template>
  <div>
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
      <table class="table table-hover table-sm table-striped infos">
        <thead class="">
          <tr>
            <th class="w20p"></th>
            <th class="w20p"></th>
            <th>Étape</th>
            <!--<th>Commentaires</th>-->
            <th class="w100p">Début</th>
            <th>Fin</th>
            <th class="w100p">Durée</th>
            <th class="w150p">Type</th>
            <th class="w50p" v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="m in etapes.length" :key="m">
            <td class="txt-center"><a href="#" @click.prevent="selectStep(etapes[m-1])"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></a></td>
            <td><i class="fa fa-check fa-lg txt-green" aria-hidden="true" v-show="etapes[m-1].validationFichier != '-1'"></i></td>
            <td><inText v-model="etapes[m-1].nom" :editable="editable"></inText></td>
            <!--<td>{{ etapes[m-1].description }}</td>-->
            <td><inMonth v-model="etapes[m-1].debut" :editable="editable" @input="updateGanttDiag"></inMonth></td>
            <td v-html="$formatDate(endDate(etapes[m-1]))"></td>
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
          <tr v-show="editable">
            <td></td>
            <td></td>
            <td><inText v-model="newStep.nom" :editable="editable"></inText></td>
            <td><inMonth v-model="newStep.debut" :editable="editable"></inMonth></td>
            <td>{{ $formatDate(endDate(newStep)) }}</td>
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

              <!--<inTypeEtape v-model="newStep.num_typeEtape" :editable="editable"></inTypeEtape>-->
            </td>
            <td>
              <button class="btn btn-primary btn-sm pointer mb10" @click="addStep">Valider</button>
            </td>
          </tr>
        </tbody>
      </table>
      <!--<button class="btn btn-primary btn-sm pointer mb10">Ajouter une étape</button>-->
      <gantt v-model="etapes" :update="updateGantt" :editable="editable"></gantt>
    </div>
</template>

<script>
    import inMonth from './inMonth'
    import inText from './inText'
    import inDuration from './inDuration'
    import inTypeEtape from './inTypeEtape'
    import inChoixMultiple from './inChoixMultiple'
    import gantt from './gantt'

    export default {
        data () {
          return {
            updateGantt: true,
            newStep : { debut: moment("01" + moment().add(1, "month").format("MMYY"), "DDMMYY"), duree: moment.duration(1, "months"), transactions: [], num_typeEtape: '1' }
          }
        },
        components: { inMonth, inDuration, inText, gantt, inTypeEtape, inChoixMultiple },
        props: { numProjet: { type: String }, etapes: { type: Array }, editable: { type: Boolean, default: false } },
        watch: {
          etapes () { this.updateGantt = !this.updateGantt }
        },
        computed: {
          initialEndDate () {
            var momentEnd = this.etapes.length > 0 ? this.endDateInitial(this.etapes[0]) : moment(), i
            for (i = 0; i < this.etapes.length; i += 1) {
              if (this.endDateInitial(this.etapes[i]).isAfter(momentEnd)) {
                momentEnd = this.endDateInitial(this.etapes[i])
              }
            }
            return moment(momentEnd).add(1, 'months')
          },
          realEndDate () {
            var momentEnd = this.etapes.length > 0 ? this.endDate(this.etapes[0]) : moment(), i
            for (i = 0; i < this.etapes.length; i += 1) {
              if (this.endDate(this.etapes[i]).isAfter(momentEnd)) {
                momentEnd = this.endDate(this.etapes[i])
              }
            }
            return moment(momentEnd).add(1, 'months')
          },
          delta () {
            var delta = this.realEndDate.diff(this.initialEndDate, 'months')
            this.$emit("update", delta)
            return delta
          }
        },
        methods: {
          endDate (step) { return moment(step.debut).add(step.duree).subtract(1, "d") },
          endDateInitial (step) { return moment(step.debutInitial).add(step.dureeInitial).subtract(1, "d") },
          supprimeEtape (index) {
            this.etapes.splice(index, 1)
          },
          addStep (step) {
            this.newStep.debutInitial = this.newStep.debut
            this.newStep.dureeInitial = this.newStep.duree
            this.newStep.new = true
            this.etapes.push( $.extend({}, this.newStep)) 

            this.newStep = {
              debut: moment("01" + this.realEndDate.format("MMYY"), "DDMMYY"), 
              duree: moment.duration(1, "months"), 
              transactions: [],
              num_typeEtape: '1'
            }
          },
          selectStep (step) {
            this.$emit("select", step)
          },
          updateGanttDiag () {
            this.updateGantt = !this.updateGantt
          }
        }
    }
</script>

<style scoped lang="scss">
</style>
