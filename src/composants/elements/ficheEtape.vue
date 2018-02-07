<template>
    <div class="fs-13">
      <div class="serif fs-13 mt10 bold">Nom de l'étape :</div>
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
      <div class="serif fs-13 mt10 bold">Objectifs :</div>
      <inLongText v-model="etape.objectifs" :editable="editable"></inLongText>
      <div class="serif fs-13 mt10 bold">Commentaires :</div>
      <inLongText v-model="etape.commentaires" :editable="editable"></inLongText>

      <div class="serif fs-13 mt10 bold">Dépenses de fonctionnement (prestation, animation, expertise ...) :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'0'"
        :fonctionnement="'1'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>
      
      <div class="serif fs-13 mt10 bold">Recettes de fonctionnement :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'1'"
        :fonctionnement="'1'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>

      <div class="serif fs-13 mt10 bold">Dépenses d'investissement :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'0'"
        :fonctionnement="'0'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>

      <div class="serif fs-13 mt10 bold">Recettes d'investissement :</div>
      <listeTransactions
        :projet="projet"
        :transactions="etape.transactions"
        :recette="'1'"
        :fonctionnement="'0'"
        :editable="editable"
        @create="sauveTransaction($event)"
      ></listeTransactions>
    </div>
</template>

<script>
    import inLongText from './inLongText'
    import inNumber from './inNumber'
    import inText from './inText'
    import listeTransactions from './listeTransactions'
    import inMonth from './inMonth'
    import inDuration from './inDuration'
    import inTypeEtape from './inTypeEtape'

    export default {
        components: { inLongText, inNumber, inText, listeTransactions, inMonth, inDuration, inTypeEtape },
        props: { projet: { type: Object, default: {} }, etape: { type: Object }, editable: { type: Boolean, default: false }},
        methods: {
          sauveTransaction (transaction) {
            this.etape.transactions.push(transaction)
          }
        }
    }
</script>

<style scoped lang="scss">
</style>
