<template>
  <div>
      <span class="fs-12">Fin initialement prévue : </span>
      <b>{{ $formatDate(initialEndDate) }}</b>
      <span class="fs-12 ml20 ib">Réelle : </span>
      <b>{{ $formatDate(realEndDate) }}</b>
      <span class="fs-12 ml20 ib">Retard : </span>
      <b>{{ delta }} mois</b>
      <img v-if="delta < 2" src="../../assets/images/sun.png">
      <img v-else-if="delta < 3" src="../../assets/images/cloud.png">
      <img v-else-if="delta < 6" src="../../assets/images/rain.png">
      <img v-else src="../../assets/images/storm.png">
      <table class="table table-hover table-sm table-striped infos">
        <thead class="">
          <tr>
            <th class="w20p"></th>
            <th class="w400p">Étape</th>
            <!--<th>Commentaires</th>-->
            <th>Début</th>
            <th>Fin</th>
            <th>Durée</th>
            <th>Type</th>
            <th v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="m in etapes.length" :key="m">
            <td class="txt-center"><a href="#" @click.prevent="selectStep(etapes[m-1])"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></a></td>
            <td><inText v-model="etapes[m-1].nom" :editable="editable"></inText></td>
            <!--<td>{{ etapes[m-1].description }}</td>-->
            <td><inMonth v-model="etapes[m-1].debut" :editable="editable" @input="updateGanttDiag"></inMonth></td>
            <td>{{ $formatDate(endDate(etapes[m-1])) }}</td>
            <td><inDuration v-model="etapes[m-1].duree" :editable="editable" @input="updateGanttDiag"></inDuration></td>
            <td><inTypeEtape v-model="etapes[m-1].num_typeEtape" :editable="editable"></inTypeEtape></td>
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
            <td><inText v-model="newStep.nom" :editable="editable"></inText></td>
            <td><inMonth v-model="newStep.debut" :editable="editable"></inMonth></td>
            <td>{{ $formatDate(endDate(newStep)) }}</td>
            <td><inDuration v-model="newStep.duree" :editable="editable" :defaultValue="1" :minValue="1"></inDuration></td>
            <td><inTypeEtape v-model="newStep.num_typeEtape" :editable="editable"></inTypeEtape></td>
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
    import gantt from './gantt'

    export default {
        data () {
          return {
            updateGantt: true,
            newStep : { debut: moment("01" + moment().add(1, "month").format("MMYY"), "DDMMYY"), duree: moment.duration(1, "months"), transactions: [], num_typeEtape: '1' }
          }
        },
        components: { inMonth, inDuration, inText, gantt, inTypeEtape },
        props: { numProjet: { type: String }, etapes: { type: Array }, editable: { type: Boolean, default: false } },
        watch: {
          etapes () { this.updateGantt = !this.updateGantt }
        },
        computed: {
          initialEndDate () {
            var momentEnd = this.etapes.length > 0 ? this.endDateInitial(this.etapes[0]) : moment()
            for (let step of this.etapes) {
              if (this.endDateInitial(step).isAfter(momentEnd)) {
                momentEnd = this.endDateInitial(step)
              }
            }
            return moment(momentEnd).add(1, 'months')
          },
          realEndDate () {
            var momentEnd = this.etapes.length > 0 ? this.endDate(this.etapes[0]) : moment()
            for (let step of this.etapes) {
              if (this.endDate(step).isAfter(momentEnd)) {
                momentEnd = this.endDate(step)
              }
            }
            return moment(momentEnd).add(1, 'months')
          },
          delta () {
            return this.realEndDate.diff(this.initialEndDate, 'months')
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
            console.log('updateGantt')
            this.updateGantt = !this.updateGantt
          }
        }
    }
</script>

<style scoped lang="scss">
</style>
