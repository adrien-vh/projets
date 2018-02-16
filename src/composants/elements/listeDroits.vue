<template>
    <div>
      {{ nouveauDroit.login }}
      <table class="table table-sm">
        <thead>
          <tr>
            <th>Agent</th>
            <th>Droits d'accès</th>
            <th v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <inAc
                    :source="$store.state.utilisateurs"
                    v-model="projet.chefProjet"
                    labelField="nom"
                    valueField="login"
                    :editable="false"
                >
              </inAc>
            </td>
            <td>
              lecture / écriture / validation
            </td>
            <td v-show="editable"></td>
          </tr>
          <tr>
            <td>
              <inAc
                    :source="$store.state.utilisateurs"
                    v-model="nouveauDroit.login"
                    labelField="nom"
                    valueField="login"
                    :editable="true"
                >
              </inAc>
            </td>
            <td>
              <inChoixMultiple
                    v-model="nouveauDroit.niveau"
                    :listeElements="$store.state.niveauxDroits"
                    :champValeur="'niveau'"
                    :champLabel="'label'"
                    :editable="true"
                ></inChoixMultiple>
            </td>
            <td v-show="editable">
               <button v-show="nouveauDroit.login != ''" class="btn btn-primary btn-sm pointer" @click="ajouteDroit">Ajouter</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
</template>

<script>
    import inAc from './inAc'
    import inChoixMultiple from './inChoixMultiple'

    export default {
        components: { inAc, inChoixMultiple },
        data () {
          return { nouveauDroit: { login: '', niveau: 0 } }
        },
        props: { 
          projet: { type: Object, default: {} },
          editable: { type: Boolean, default: false }
        },
        methods: {
          ajouteDroit () {}
        }
    }
</script>

<style scoped lang="scss">
</style>
