<template>
    <div>
      {{ nouveauDroit.login }}
      <table class="table table-sm table-striped">
        <thead>
          <tr>
            <th>Agent</th>
            <th>Droits d'accès</th>
            <th v-show="editable"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-show="projet.chefProjet !== ''">
            <td>
              <inAc
                    :source="$store.state.utilisateurs"
                    v-model="projet.chefProjet"
                    labelField="nom"
                    valueField="login"
                    :editable="false"
                >
              </inAc> <em>(Chef de projet)</em>
            </td>
            <td>
              lecture / écriture / validation
            </td>
            <td v-show="editable"></td>
          </tr>
          <tr v-show="projet.createur !== projet.chefProjet">
            <td>
              <inAc
                    :source="$store.state.utilisateurs"
                    v-model="projet.createur"
                    labelField="nom"
                    valueField="login"
                    :editable="false"
                >
              </inAc> <em>(Créateur)</em>
            </td>
            <td>
              lecture / écriture / validation
            </td>
            <td v-show="editable"></td>
          </tr>
         <tr v-for="m in projet.droits.length" :key="m">
            <td>
              <inAc
                    :source="$store.state.utilisateurs"
                    v-model="projet.droits[m-1].login"
                    labelField="nom"
                    valueField="login"
                    :editable="editable"
                >
              </inAc>
            </td>
            <td>
              <inChoixMultiple
                    v-model="projet.droits[m-1].niveau"
                    :listeElements="$store.state.niveauxDroits"
                    :champValeur="'niveau'"
                    :champLabel="'label'"
                    :editable="editable"
                ></inChoixMultiple>
            </td>
            <td v-show="editable">
               <button class="btn btn-danger btn-sm pointer" @click="supprimeDroit(m)">Supprimer</button>
            </td>
          </tr>
          <tr v-show="editable">
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
          projet: { type: Object, default: { droits: [] } },
          editable: { type: Boolean, default: false }
        },
        methods: {
          ajouteDroit () {
            this.projet[C.DROITS].push($.extend({}, this.nouveauDroit))
            this.nouveauDroit = { login: '', niveau: 0 }
          }
        }
    }
</script>

<style scoped lang="scss">
  
    button {
      padding: 2px;
      font-size: 12px;
      width: 100%;
    }

</style>
