<template>
    <table class="form">
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
            <td><inText v-model="projet.chefProjet" :editable="editable"></inText></td>
        </tr>
        <tr>
            <th>Equipe projet interne :  </th>
            <th>Instances de pilotage du projet : </th>
        </tr>
        <tr>
            <td><inText v-model="projet.equipeProjet" :editable="editable"></inText></td>
            <td><inText v-model="projet.instances" :editable="editable"></inText></td>
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
    import inLongText from '../elements/inLongText'
    import inNumber from '../elements/inNumber'
    import inText from '../elements/inText'

    export default {
        components: { inLongText, inNumber, inText },
        props: { projet: { type: Object, default: {} }, editable: { type: Boolean, default: false }}
    }
</script>

<style scoped lang="scss">
</style>
