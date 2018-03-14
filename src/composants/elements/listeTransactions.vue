<template>
  <div>
    <div v-show="!editable && transactionsFiltrees.length === 0" class="fs-12">(Aucun enregistrement)</div>
    <table class="table table-hover table-sm table-striped infos" v-show="editable || transactionsFiltrees.length > 0">
      <thead>
        <tr>
          <th>Intitulé</th>
          <th>Date de réalisation <span>(paiement)</span></th>
          <th>Montant</th>
          <th v-show="editable"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in transactionsFiltrees.length" :key="m">
          <td><inText v-model="transactionsFiltrees[m-1].intitule" :editable="editable"></inText></td>
          <td><inMonth v-model="transactionsFiltrees[m-1].date" :editable="editable"></inMonth></td>
          <td><inNumber v-model="transactionsFiltrees[m-1].montant" :editable="editable" :maxLength="5" suffix="k€"></inNumber></td>
          <td v-show="editable"><button class="btn btn-danger btn-sm pointer mb10">Supprimer</button></td>
        </tr>
        <tr v-show="editable">
          <td><inText v-model="newTransaction.intitule" :editable="true"></inText></td>
          <td><inMonth v-model="newTransaction.date" :editable="true"></inMonth></td>
          <td><inNumber v-model="newTransaction.montant" :maxLength="5" suffix="k€" :editable="true"></inNumber></td>
          <td class="txt-center">
            <button class="btn btn-primary btn-sm pointer mb10" @click="sauveTransaction">Valider</button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="2" class="txt-right fs-13"><b>TOTAL :</b></td>
          <td class="fs-13"><b>{{ sumTransactions }} k€</b></td>   
          <td v-show="editable"></td>
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
import inText from "../elements/inText";
import inNumber from "../elements/inNumber";
import inMonth from "../elements/inMonth";

export default {
  components: { inText, inNumber, inMonth },
  data () {
    return {
      newTransaction : { intitule: "", date: moment(), montant: 0 }
    }
  },
  props: {
    projet: { type: Object, default: {} },
    editable: { type: Boolean, default: false },
    transactions: { type: Array },
    recette: String,
    fonctionnement: String
  },
  computed: {
    sumTransactions () {
      var i, retour = 0
      for (i = 0; i < this.transactions.length; i += 1 ) {
        if (this.transactions[i].recette === this.recette && this.transactions[i].fonctionnement === this.fonctionnement) {
          retour += this.transactions[i].montant
        }
      }
      return retour
    },
    transactionsFiltrees () {
      var i, retour = []
      for (i = 0; i < this.transactions.length; i += 1 ) {
        if (this.transactions[i].recette === this.recette && this.transactions[i].fonctionnement === this.fonctionnement) {
          retour.push(this.transactions[i])
        }
      }
      return retour
    }
  },
  methods: {
    sauveTransaction () {
      this.newTransaction.recette = this.recette
      this.newTransaction.fonctionnement = this.fonctionnement
      this.$emit("create", this.newTransaction)
      this.newTransaction = { intitule: "", date: moment(), montant: 0 }
    }
  }
};
</script>

<style scoped lang="scss"></style>
