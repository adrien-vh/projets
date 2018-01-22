<template>
    <div class="fs-13">
      <div class="serif fs-13 mt10 bold">Nom de l'étape :</div>
      <inText v-model="etape.nom" :editable="editable"></inText>
      <div class="serif fs-13 mt10 bold">Objectifs :</div>
      <inLongText v-model="etape.objectifs" :editable="editable"></inLongText>
      <div class="serif fs-13 mt10 bold">Commentaires :</div>
      <inLongText v-model="etape.commentaires" :editable="editable"></inLongText>

      <div class="serif fs-13 mt10 bold">Dépenses de fonctionnement (prestation, animation, expertise ...) :</div>
      <listeTransactions :projet="projet" :transactions="transactions(etape, '0', '1')" :editable="editable"></listeTransactions>

      <div class="serif fs-13 mt10 bold">Dépenses d'investissement :</div>
      <listeTransactions :projet="projet" :transactions="transactions(etape, '0', '0')" :editable="editable"></listeTransactions>

      <div class="serif fs-13 mt10 bold">Recettes de fonctionnement :</div>
      <listeTransactions :projet="projet" :transactions="transactions(etape, '1', '1')" :editable="editable"></listeTransactions>

      <div class="serif fs-13 mt10 bold">Recettes d'investissement :</div>
      <listeTransactions :projet="projet" :transactions="transactions(etape, '1', '0')" :editable="editable"></listeTransactions>
    </div>
</template>

<script>
    import inLongText from './inLongText'
    import inNumber from './inNumber'
    import inText from './inText'
    import listeTransactions from './listeTransactions'

    export default {
        components: { inLongText, inNumber, inText, listeTransactions },
        props: { projet: { type: Object, default: {} }, etape: { type: Object, default: {} }, editable: { type: Boolean, default: false }},
        methods: {
          transactions (etape, recette, fonctionnement) {
            var retour = []

            etape.transactions = etape.transactions || []

            for (let transaction in etape.transactions) {
              if (transaction.recette === recette && transaction.fonctionnement === fonctionnement) {
                retour.push(transaction)
              }
            }
            return retour
          }
        }
    }
</script>

<style scoped lang="scss">
</style>
