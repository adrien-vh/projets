<template>
  <div id="pageAccueil">
    <!--<div id="contBouton">
      <a href="#" class="fs-18" @click.prevent="newProject"><i class="fa fa-plus" aria-hidden="true"></i> Nouveau projet</a>
    </div>-->
    <!--<h2>Synthèses</h2>-->
    <div id="listeSyntheses">
      <div class="card pa10">
        <i class="fa fa-money fa-2x" aria-hidden="true"></i><br>
        Récapitulatif financier
      </div><div class="card pa10">
        <i class="fa fa-wpforms fa-2x" aria-hidden="true"></i><br>
        Planning global
      </div>
      <!--<router-link to="login"><i class="fa fa-wpforms" aria-hidden="true"></i> Récapitulatif financier</router-link>
      <router-link to="login"><i class="fa fa-wpforms" aria-hidden="true"></i> Planning global</router-link>-->
    </div>
    <!--<h2>Projets en cours</h2>-->
    <div class="cards-container">
      <div class="project-card pointer card" @click="newProject">
        <div class="fs-12 mt10 txt-grey">&nbsp;</div>
        <div class="nom"><div>NOUVEAU PROJET</div></div>
        <img src="../../assets/images/plus_116.png">
        <div class="fin">&nbsp;</div>
        <div>&nbsp;</div>
      </div>
      <div v-for="projet in projets" :key="projet.num_projet" class="project-card pointer card" @click="openProject(projet.num_projet)">
        <div class="fs-12 mt10 txt-grey">{{ projet.chefProjet }}&nbsp;</div>
        <div class="nom"><div>{{ projet.nom }}</div></div>
        <img v-if="delta(projet) < 2" src="../../assets/images/sun_116.png">
        <img v-else-if="delta(projet) < 3" src="../../assets/images/cloud_116.png">
        <img v-else-if="delta(projet) < 6" src="../../assets/images/rain_116.png">
        <img v-else src="../../assets/images/storm_116.png">
        <div class="fin">{{ $formatDate(projet.fin) }}</div>
        <div>{{ projet.budgetPrev }}<span v-if="projet.budgetPrev > 0"> 000</span> €</div>
      </div>
    </div>
   <!-- <table class="table table-hover table-sm table-striped">
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
    </table>-->
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
      return projet.fin.diff(projet.finPrev, 'months') - 1
    },
    openProject (num_projet) {
      this.$router.push("/projet/" + num_projet)
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

    .card {
      background: #fff;
      border-radius: 2px;
      display: inline-block;
      width: 200px;
      box-sizing: border-box;
      /*position: relative;*/
      
      color: #333;
      box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24);
      transition: all 0.3s cubic-bezier(.25,.8,.25,1);
      text-align: center;
      margin: 15px;

      cursor: pointer;

      &:hover {
        box-shadow: 0 14px 28px rgba(0,0,0,0.25), 0 10px 10px rgba(0,0,0,0.22);
      }

    }

    .cards-container {

      .project-card {
        height: 330px;
        

        .nom {
          display: table-cell;
          vertical-align: middle;
          text-align: center;
          padding-top: 10px;
          font-size: 15px;
          /*border: 1px solid #333;*/
          height: 100px;
          font-weight: bold;
          width: 200px;
          div {
            width: 100%;
            padding: 0 10px;
          }
        }
      } 
    }

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
