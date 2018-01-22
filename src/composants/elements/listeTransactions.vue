<template>
  <div>
    <div v-show="!editable && transactions.length === 0" class="fs-12">(Aucun enregistrement)</div>
    <table class="table table-hover table-sm table-striped infos" v-show="editable || transactions.length > 0">
      <thead>
        <tr>
          <th>Intitulé</th>
          <th>Date de réalisation <span>(paiement)</span></th>
          <th>Montant</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="transaction in transactions">
          <td><inText v-model="transaction.intitule" :editable="editable"></inText></td>
          <td><inText v-model="transaction.date" :editable="editable"></inText></td>
          <td><inText v-model="transaction.montant" :editable="editable"></inText></td>
        </tr>
        <tr v-show="editable">
          <td>
            <input type="text">
          </td>
          <td>
            <input type="text">
          </td>
          <td>
            <input type="text">
          </td>
          <td class="txt-center">
            <button class="btn btn-primary btn-sm pointer mb10">Valider</button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td class="txt-right"><b>TOTAL :</b></td>
          <td colspan="2"></td>   
        </tr>
      </tfoot>
    </table>
  </div>
</template>

<script>
import inText from "../elements/inText";

export default {
  components: { inText },
  data () {
    return {
      newTransaction : { intitule: "", date: "", montant: "" }
    }
  },
  props: {
    projet: { type: Object, default: {} },
    editable: { type: Boolean, default: false },
    transactions: { type: Array, default: [] }
  },
  methods: {
    sauveTransaction () {
      this.$emit("create", this.newTransaction)
    }
  }
};
</script>

<style scoped lang="scss"></style>
