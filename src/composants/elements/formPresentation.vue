<!--
  FORMULAIRE DE PRÉSENTATION GÉNÉRALE D'UNE FICHE PROJET
-->
<template>
    <table class="form">
        <tr>
            <th>Axe du projet de territoire : </th>
            <th>Sous-axe : </th>
        </tr>
        <tr>
            <td><inChoixMultiple
                v-model="projet.num_axe"
                :listeElements="$store.state.axes"
                :champValeur="'num_axe'"
                :champLabel="'nom'"
                :champCouleur="'couleur'"
                :editable="editable"
            ></inChoixMultiple></td>
            <td><inChoixMultiple
                v-model="projet.num_sousAxe"
                :listeElements="$store.state.sousAxes"
                :champValeur="'num_sousAxe'"
                :champLabel="'nom'"
                :champCouleur="'couleur'"
                :editable="editable"
            ></inChoixMultiple></td>
        </tr>
        <tr>
            <th colspan="2">Descriptif sommaire :</th>
        </tr>
        <tr>
            <td colspan="2">
                <inLongText v-model="projet.description" placeholder="Description du projet" :editable="editable"></inLongText>
            </td>
        </tr>
        <tr>
            <th>Pilote politique du projet : </th>
            <th>Chef de projet : </th>
        </tr>
        <tr>
            <td><inText v-model="projet.pilotePolitique" :editable="editable"></inText></td>
            <td>
                <inAc
                    :source="$store.state.utilisateurs"
                    v-model="projet.chefProjet"
                    labelField="nom"
                    valueField="login"
                    :editable="editable"
                >
                </inAc>
            </td>
        </tr>
        <tr>
            <th colspan="2">Equipe projet interne :  </th>
        </tr>
        <tr>
            <td colspan="2"><inText v-model="projet.equipeProjet" :editable="editable"></inText></td>
        </tr>
        <tr>
            <th colspan="2">Direction :  </th>
        </tr>
        <tr>
            <td colspan="2">
                <inChoixMultiple
                    v-model="projet.num_direction"
                    :listeElements="$store.state.directions"
                    :champValeur="'num_direction'"
                    :champLabel="'nom'"
                    :editable="editable"
                ></inChoixMultiple>
            </td>
        </tr>
        <tr>
            <th colspan="2">Instances de pilotage du projet : </th>
        </tr>
        <tr>
            <td colspan="2"><inText v-model="projet.instances" :editable="editable"></inText></td>
        </tr>
        <tr>
            <th colspan="2">Partenaires externes impliqués :</th>
        </tr>
        <tr>
            <td colspan="2"><inLongText v-model="projet.partenaires" placeholder="Description du projet" :editable="editable"></inLongText></td>
        </tr>

        <tr>
            <th>Budget prévisionnel :  </th>
            <th>Durée prévisionnelle : </th>
        </tr>
        <tr>
            <td><inNumber v-model="projet.budgetPrev" :maxLength="6" suffix="k€" :editable="editable"></inNumber></td>
            <td><inNumber v-model="projet.dureePrev" :maxLength="2" suffix="mois" :editable="editable" :minValue="1" :defaultValue="1"></inNumber></td>
        </tr>
    </table>
</template>

<script>
  /**
  * @prop {Object}  [projet={}]       Projet concerné
  * @prop {Boolean} [editable=false]  Contenu éditable ?
  */

  import inLongText from './inLongText'
  import inNumber from './inNumber'
  import inText from './inText'
  import inChoixMultiple from './inChoixMultiple'
  import inAc from './inAc'

  export default {
    components: { inLongText, inNumber, inText, inChoixMultiple, inAc },
    props: { projet: { type: Object, default: {} }, editable: { type: Boolean, default: false }},
    methods: { }
  }
</script>

<style scoped lang="scss">
</style>
