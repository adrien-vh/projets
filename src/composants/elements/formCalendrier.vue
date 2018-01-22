<template>
  <div>
      <table class="table table-hover table-sm table-striped infos">
        <thead class="">
          <tr>
            <th class="w20p"></th>
            <th class="w400p">Étape</th>
            <!--<th>Commentaires</th>-->
            <th>Début</th>
            <th>Fin</th>
            <th>Durée</th>
            <th v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="etape in etapes" :key="etape.num_etape">
            <td class="txt-center"><a href="#"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></a></td>
            <td><inText v-model="etape.nom" :editable="editable"></inText></td>
            <!--<td>{{ etape.description }}</td>-->
            <td><inMonth v-model="etape.debut" :editable="editable"></inMonth></td>
            <td>{{ $formatDate(endDate(etape)) }}</td>
            <td><inDuration v-model="etape.duree" :editable="editable"></inDuration></td>
            <td v-show="editable"><button class="btn btn-danger btn-sm pointer mb10">Supprimer</button></td>
          </tr>
          <tr v-show="editable">
            <td class="txt-center"><a href="#"><i class="fa fa-eye fa-lg" aria-hidden="true"></i></a></td>
            <td><inText v-model="newStep.nom" :editable="editable"></inText></td>
            <td><inMonth v-model="newStep.debut" :editable="editable"></inMonth></td>
            <td>{{ $formatDate(endDate(newStep)) }}</td>
            <td><inDuration v-model="newStep.duree" :editable="editable" :defaultValue="1" :minValue="1"></inDuration></td>
            <td>
              <button class="btn btn-primary btn-sm pointer mb10">Valider</button>
            </td>
          </tr>
        </tbody>
      </table>
      <!--<button class="btn btn-primary btn-sm pointer mb10">Ajouter une étape</button>-->
      <gantt v-model="etapes" :update="updateGantt"></gantt>
    </div>
</template>

<script>
    import inMonth from './inMonth'
    import inText from './inText'
    import inDuration from './inDuration'
    import gantt from './gantt'

    export default {
        data () {
          return {
            newStep: { debut: moment("01" + moment().add(1, "month").format("MMYY"), "DDMMYY"), duree: moment.duration(1, "months") },
            updateGantt: true
          }
        },
        components: { inMonth, inDuration, inText, gantt },
        props: { projet: { type: Object, default: {} }, etapes: { type: Array, default: [] }, editable: { type: Boolean, default: false }},
        methods: {
          endDate (step) { return moment(step.debut).add(step.duree).subtract(1, "d") }
        }
    }
</script>

<style scoped lang="scss">
</style>
