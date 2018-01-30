<template>
  <div id="pageAccueil">
    <div id="contBouton">
      <button class="btn btn-lg btn-primary" @click="newProject"><i class="fa fa-plus" aria-hidden="true"></i> Nouveau projet</button>
    </div>
    <h2>Synthèses</h2>
    <div id="listeSyntheses">
      <router-link to="login"><i class="fa fa-wpforms" aria-hidden="true"></i> Récapitulatif financier</router-link>
      <router-link to="login"><i class="fa fa-wpforms" aria-hidden="true"></i> Planning global</router-link>
    </div>
    <h2>Projets en cours</h2>
    <table class="table table-hover table-sm table-striped">
      <thead class="thead-dark">
        <tr>
          <th>Titre</th>
          <th>Chef de projet</th>
          <th>Date de fin</th>
          <th>Budget prévisionnel</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="projet in projets" :key="projet.num_projet">
          <td><b><router-link :to="'/projet/' + projet.num_projet">{{ projet.nom }}</router-link></b></td>
          <td>{{ projet.chefProjet }}</td>
          <td>{{ $formatDate(projet.fin) }}</td>
          <td>{{ projet.budgetPrev }} 000 €</td>
          <td>
            <img v-if="delta(projet) < 2" src="../../assets/images/sun.png">
            <img v-else-if="delta(projet) < 3" src="../../assets/images/cloud.png">
            <img v-else-if="delta(projet) < 6" src="../../assets/images/rain.png">
            <img v-else src="../../assets/images/storm.png">
          </td>
        </tr>
        <tr>
          <td><b>Plan lumière</b></td>
          <td>François Houot</td>
          <td>juillet 2018</td>
          <td>50 000 €</td>
          <td><img src="../../assets/images/sun.png" alt=""></td>
        </tr>
        <tr>
          <td><b>Valorisation et préservation des espaces naturels remarquables</b></td>
          <td>Christophe Armand</td>
          <td>mars 2017</td>
          <td>100 000 €</td>
          <td><img src="../../assets/images/cloud.png" alt=""></td>
        </tr>
      </tbody>
    </table>
    <h2>Projets archivés</h2>
    <table class="table table-hover table-sm table-striped">
      <thead class="thead-dark">
        <tr>
          <th>Titre</th>
          <th>Chef de projet</th>
          <th>Date de fin</th>
          <th>Budget prévisionnel</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><b>Plan lumière</b></td>
          <td>François Houot</td>
          <td>juillet 2018</td>
          <td>50 000 €</td>
          <td><img src="../../assets/images/rain.png" alt=""></td>
        </tr>
        <tr>
          <td><b>Valorisation et préservation des espaces naturels remarquables</b></td>
          <td>Christophe Armand</td>
          <td>mars 2017</td>
          <td>100 000 €</td>
          <td><img src="../../assets/images/storm.png" alt=""></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default { 
  data () {
    return {
      projets: []
    }
  },
  methods : {
    newProject () {
      this.$router.push('/projet')
    },
    delta (projet) {
      return projet.fin.diff(this.finPrev, 'months')
    }
  },
  mounted () {
    var me = this, i
    this.$store.state.server.call (C.LISTE_PROJETS, function (data) {
      for (i = 0; i < data[C.LISTE_PROJETS].length; i += 1) {
        //console.log(projet)
        data[C.LISTE_PROJETS][i].fin = data[C.LISTE_PROJETS][i].fin ? moment(data[C.LISTE_PROJETS][i].fin, "YYYY-MM-DD") : moment()
        data[C.LISTE_PROJETS][i].finPrev = data[C.LISTE_PROJETS][i].finPrev ? moment(data[C.LISTE_PROJETS][i].finPrev, "YYYY-MM-DD") : moment()
      }
      me.projets = data[C.LISTE_PROJETS]
    })
  }
 }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";

  #pageAccueil {
    padding: 0 50px;
    #listeSyntheses {
      padding-bottom: 20px;
      a {
        display: inline-block;
        margin-right: 15px;
      }
    }
    #contBouton {
      text-align: center;
      padding-bottom: 20px;
      
      button {
        cursor: pointer;
      }
    }
    table {
      font-size: 14px;
      td {
        vertical-align: middle;
      }
    }
  }
  tr {
    cursor: pointer;
  }
</style>
