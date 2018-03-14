<!--
 PAGE D'ACCUEIL
 -->
<template>
  <div id="pageAccueil">

    <!-- Titre de la page -->
    <div class="titre-page">Accueil</div>
    <!-- FIN Titre de la page -->

    <!-- Liens vers les synthèses -->
    <div id="listeSyntheses">
      <div class="small-card pa10">
        <i class="fa fa-money fa-2x" aria-hidden="true"></i><br>
        Récapitulatif financier
      </div><div class="small-card pa10">
        <i class="fa fa-wpforms fa-2x" aria-hidden="true"></i><br>
        Planning global
      </div>
    </div>
    <!-- FIN Liens-->

    <!-- Liste des projets accessibles à l'utilisateur -->
    <div class="cards-container">

      <!-- Carte "Nouveau Projet" -->
      <div class="project-card pointer card hvr-pop" @click="newProject" v-show="$niveauAcces(0) > 0">
        <div class="nom"><div>NOUVEAU PROJET</div></div>
        <img src="../../assets/images/plus_116.png">
        <div class="fin">&nbsp;</div>
        <div class="fs-12 mt10 txt-grey h45p oh pr15 pl15">&nbsp;</div>
        <div class="fs-12 txt-grey">&nbsp;</div>
        <div>&nbsp;</div>
      </div>
      <!-- FIN Carte "Nouveau Projet" -->

      <!-- Cartes des projets -->
      <div v-for="projet in projets" :key="projet.num_projet" class="project-card pointer card hvr-grow" @click="openProject(projet.num_projet)">
        <div v-for="axe in $store.state.axes" :key="axe.num_axe" class="bandeau-axe" :style="{ backgroundColor: '#' + axe.couleur }" v-if="axe.num_axe == projet.num_axe"></div>
        <div class="nom"><div>{{ projet.nom }}</div></div>
        <img v-if="delta(projet) < 2" src="../../assets/images/sun_116.png">
        <img v-else-if="delta(projet) < 3" src="../../assets/images/cloud_116.png">
        <img v-else-if="delta(projet) < 6" src="../../assets/images/rain_116.png">
        <img v-else src="../../assets/images/storm_116.png">
        <div class="fin">{{ $formatDate(projet.fin) }}</div>
        <div>{{ projet.budgetPrev }}<span v-if="projet.budgetPrev > 0"> 000</span> €</div>
        <div class="fs-12 mt10 txt-grey h45p oh pr15 pl15" v-for="direction in $store.state.directions" :key="direction.num_direction" v-if="direction.num_direction == projet.num_direction">{{ direction.nom }}</div>
        <div class="fs-12">{{ $userName(projet.chefProjet) }}&nbsp;</div>
      </div>
      <!-- FIN Cartes des projets -->

    </div>
    <!-- FIN Liste des projets accessibles à l'utilisateur -->
  </div>
</template>

<script>
export default { 
  data () {
    return {
      projets: [] // Liste des projets
    }
  },
  methods : {
    newProject () { this.$router.push('/projet') }, // Navigation vers la page nouveau projet

    /** 
    * Décalage temporel d'un projet
    * 
    * @param  {Object}  projet  Objet projet tel que retourné par le serveur au chargement de la liste des projets
    */
    delta (projet) {
      return projet.fin.diff(projet.finPrev, 'months') - 1
    },

    /** 
    * Ouverture d'une fiche projet (=navigation vers la page idoine)
    * 
    * @param  {String}  num_projet  Index du projet
    */
    openProject (num_projet) { this.$router.push("/projet/" + num_projet) }
  },
  mounted () {
    var me = this, i

    /* Chargement de la liste des projets */
    this.$$ServerCall (C.LISTE_PROJETS, function (data) {

      /* Transformation en objet moment des date de fin et de fin prévisionnelle */
      for (i = 0; i < data[C.LISTE_PROJETS].length; i += 1) {
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
    text-align: center;

    .card, .small-card {
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

      
    }

    .card {
      &:hover {
        box-shadow: 0 10px 18px rgba(0,0,0,0.25), 0 5px 5px rgba(0,0,0,0.12);
      }
    }

    .small-card {
      &:hover {
        box-shadow: 0 3px 8px rgba(0,0,0,0.25), 0 5px 5px rgba(0,0,0,0.12);
      }
    }

    .cards-container {

      .project-card {
        height: 350px;
        position: relative;

        .bandeau-axe {
          position: absolute;
          height: 5px;
          top: 0;
          left: 0;
          width: 100%;
        }
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
/*
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
    }*/
  }
  tr {
    cursor: pointer;
  }
</style>
