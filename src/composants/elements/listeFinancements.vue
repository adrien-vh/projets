<template>
  <div>
    <div v-show="!editable && financements.length === 0" class="fs-12">(Aucun enregistrement)</div>
    <table class="table table-hover table-sm table-striped infos" v-show="editable || financements.length > 0">
      <thead>
        <tr>
          <th>Intitulé</th>
          <th>Type</th>
          <th>Date de réalisation <span>(paiement)</span></th>
          <th>Montant</th>
          <th v-show="editable"></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="m in financements.length" :key="m">
          <td><inText v-model="financements[m-1].intitule" :editable="editable"></inText></td>
          <td>
            <inChoixMultiple
              v-model="financements[m-1].fonctionnement"
                  :listeElements="typesIF"
                  :champValeur="'valeur'"
                  :champLabel="'nom'"
                  :editable="editable"
            ></inChoixMultiple>
            <inChoixMultiple
              v-model="financements[m-1].recette"
                  :listeElements="typesRD"
                  :champValeur="'valeur'"
                  :champLabel="'nom'"
                  :editable="editable"
            ></inChoixMultiple>
          </td>
          <td><inMonth v-model="financements[m-1].date" :editable="editable"></inMonth></td>
          <td><inNumber v-model="financements[m-1].montant" :editable="editable" :maxLength="5" suffix="k€"></inNumber></td>
          <td v-show="editable"><button class="btn btn-danger btn-sm pointer mb10">Supprimer</button></td>
        </tr>
        <tr v-show="editable">
          <td><inText v-model="newFinancement.intitule" :editable="true"></inText></td>
          <td>
            <inChoixMultiple
              v-model="newFinancement.fonctionnement"
                  :listeElements="typesIF"
                  :champValeur="'valeur'"
                  :champLabel="'nom'"
                  :editable="editable"
            ></inChoixMultiple>
            <inChoixMultiple
              v-model="newFinancement.recette"
                  :listeElements="typesRD"
                  :champValeur="'valeur'"
                  :champLabel="'nom'"
                  :editable="editable"
            ></inChoixMultiple>
          </td>
          <td><inMonth v-model="newFinancement.date" :editable="true"></inMonth></td>
          <td><inNumber v-model="newFinancement.montant" :maxLength="5" suffix="k€" :editable="true"></inNumber></td>
          <td class="txt-center">
            <button class="btn btn-primary btn-sm pointer mb10" @click="sauveFinancement">Valider</button>
          </td>
        </tr>
      </tbody>
      <tfoot>
        <tr>
          <td colspan="3" class="txt-right fs-13"><b>TOTAL INVESTISSEMENT :</b></td>
          <td class="fs-13"><b>{{ sumInvestissement }} k€</b></td>   
          <td v-show="editable"></td>
        </tr>
        <tr>
          <td colspan="3" class="txt-right fs-13"><b>TOTAL FONCTIONNEMENT :</b></td>
          <td class="fs-13"><b>{{ sumFonctionnement }} k€</b></td>   
          <td v-show="editable"></td>
        </tr>
        <tr>
          <td colspan="3" class="txt-right fs-13"><b>TOTAL :</b></td>
          <td class="fs-13"><b>{{ sumFinancements }} k€</b></td>   
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
import inChoixMultiple from "../elements/inChoixMultiple";

export default {
  components: { inText, inNumber, inMonth, inChoixMultiple },
  data () {
    return {
      newFinancement : { intitule: "", date: moment(), montant: 0, recette: '0', fonctionnement: '0' },
      typesRD : [
        { nom: "Recette", valeur: "1" },
        { nom: "Dépense", valeur: "0" }
      ],
      typesIF : [
        { nom: "Fonctionnement", valeur: "1" },
        { nom: "Investissement", valeur: "0" }
      ]
    }
  },
  props: {
    editable: { type: Boolean, default: false },
    projet: { type: Object, default: {} }
  },
  computed: {
    financements () { return this.projet[C.FINANCEMENT] || [] },
    sumFinancements () {
      return this.sumFonctionnement + this.sumInvestissement
    },
    sumFonctionnement () {
      var i, retour = 0
      for (i = 0; i < this.financements.length; i += 1 ) {
        if (this.financements[i].fonctionnement === '1') {
          if (this.financements[i].recette === '1') {
            retour += this.financements[i].montant
          } else {
            retour -= this.financements[i].montant
          }
        }
      }
      return retour
    },
    sumInvestissement () {
      var i, retour = 0
      for (i = 0; i < this.financements.length; i += 1 ) {
        if (this.financements[i].fonctionnement === '0') {
          if (this.financements[i].recette === '1') {
            retour += this.financements[i].montant
          } else {
            retour -= this.financements[i].montant
          }
        }
      }
      return retour
    }
  },
  methods: {
    sauveFinancement () {
      this.financements.push(this.newFinancement)
      this.newFinancement = {  intitule: "", date: moment(), montant: 0, recette: '0', fonctionnement: '0' }
    }
  }
};
</script>

<style scoped lang="scss"></style>
