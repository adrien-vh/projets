<template>
  <div id="projet">
    <nav>
      <ul>
        <li>
          <a href="#" :class="{ active: activePart == 'fiche' }" @click.prevent="goTo('fiche')">Fiche projet</a>
        </li>
        <li>
          <a href="#" :class="{ active: activePart == 'analyse' }" @click.prevent="goTo('analyse')">Analyse préalable</a>
        </li>
        <li>
          <a href="#" :class="{ active: activePart == 'phases' }" @click.prevent="goTo('phases')">Phases</a>
        </li>
        <li>
          <a href="#" :class="{ active: activePart == 'fiches' }" @click.prevent="goTo('fiches')">Fiches étape</a>
        </li>
      </ul>
    </nav>
    <div class="contenu">
      <!--<h2>Fiche projet</h2>-->

      <h2 id="fiche" v-show="!editing">{{ projet.nom }}</h2>
      <input type="text" v-model="projet.nom" placeholder="Nom du projet" class="h2-size serif" v-show="editing">
      <table class="infos">
        
        <tr>
          <th colspan="4">Descriptif sommaire :</th>
        </tr>
         <tr>
          <td colspan="4">
            <inLongText v-model="projet.description" placeholder="Description du projet" v-show="editing"></inLongText>
            <div class="fs-13 txt-justify" v-html="projet.description" v-show="!editing"></div>
          </td>
        </tr>
        <tr>
          <th colspan="2">Pilote politique du projet : </th>
          <th colspan="2">Chef de projet : </th>
        </tr>
        <tr>
          <td colspan="2"><input v-model="projet.pilotePolitique" type="text"></td>
          <td colspan="2"><input v-model="projet.chefProjet" type="text"></td>
        </tr>
        <tr>
          <th colspan="2">Equipe projet interne :  </th>
          <th colspan="2">Instances de pilotage du projet : </th>
        </tr>
        <tr>
          <td colspan="2"><input v-model="projet.pilotePolitique" type="text"></td>
          <td colspan="2"><input v-model="projet.pilotePolitique" type="text"></td>
        </tr>
        <tr>
          <th colspan="4">Partenaires externes impliqués :</th>
        </tr>
        <tr>
          <td colspan="4">RFF (Antony Larrondo) puis Stéphane FONTAINE (SNCF réseaux)<br>
SNCF ( Hélene Massias)<br>
CRLorraine (Marc Giraud) – Dominique LEBESSON<br>
EPFL (Sophie BUGADA)
        </td>
  </tr>
        
        <tr>
          <th colspan="2">Budget prévisionnel :  </th>
          <th colspan="2">Durée prévisionnelle : </th>
        </tr>
        <tr>
          <td colspan="2">100 000 €</td>
          <td colspan="2">10 mois</td>
        </tr>
      </table>      
      
      <h5 id="analyse" class="part">Analyse préalable</h5>
      <div class="fs-13 txt-justify" id="texteAnalyse" contenteditable="true">
Objectifs du projet (que va-t-on faire) ?
Réaliser l’étude technique pré-opérationnelle 3 volets :
Infrastructure ferroviaire
Stationnement
Pôle BUS
Créer un Pôle d’Echange Multimodal au cœur du Bassin de Pompey
Déplacement/Aménagement de la halte ferroviaire de Pompey et création d’une véritable gare accompagnée de ses services
Aménagement de l’interconnexion BUS/CAR
Création d’un parking en ouvrage de type silo (P+R)
Finalités du projet :
Amélioration significative de la desserte du territoire et du Parc Eiffel Energie par les transports collectifs pour un report modal des déplacements automobiles pendulaires Amélioration du bilan environnemental lié aux déplacements et de la qualité de l’air.

Enjeu du projet :
Articulation des axes de déplacements Nord-Sud et Est-Ouest à l’échelle du SCOT sur le POEM du Bassin de Pompey (Toul-Sillon Lorrain // Luxembourg-Metz-Nancy). Accessibilité du Bassin de Pompey et du Parc Eiffel Energie par les transports collectifs de grande capacité. Positionnement au long terme du Bassin de Pompey sur les axes de performance des transports en Région Lorraine.
  </div>
      <h5 id="phases" class="part">Phases :</h5>
      <table class="table table-hover table-sm table-striped steps-table">
        <thead class="">
          <tr>
            <th>Titre</th>
            <th>Description</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Durée</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="step in steps" :key="step.num_step">
            <td>{{ step.title }}</td>
            <td>{{ step.description }}</td>
            <td>{{ formatDate(step.startDate) }}</td>
            <td>{{ formatDate(step.endDate) }}</td>
            <td>{{ formatDuration(step.duration) }}</td>
          </tr>
        </tbody>
      </table>
      <gantt v-model="steps"></gantt>
      <br>
      <h5 id="fiches" class="part">Fiches étapes :</h5>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
      <br>
    </div>
    
  </div>
</template>

<script>
  import gantt from '../elements/gantt'
  import inLongText from '../elements/inLongText'
    
  export default {
    components: { inLongText, gantt },
    data () {
      return {
        editing: true,
        activePart: "fiche",
        projet: {
          nom: "",
          description: "",
          pilotePolitique: "",
          chefProjet: ""
        },
        steps: [
          { 
            num_step: 1,
            startDate: moment("010118", "DDMMYY"),
            endDate: moment(moment("010118", "DDMMYY")).add(5, "months"),
            title: "Première étape avec un nom un peu long",
            description: "Première étape avec un nom un peu long",
            duration: moment.duration(5, "months")
          },
          { 
            num_step: 2,
            startDate: moment("010217", "DDMMYY"),
            endDate: moment(moment("010217", "DDMMYY")).add(2, "months"),
            title: "Seconde étape",
            description: "Description de la seconde étape",
            duration: moment.duration(2, "months")
          }
        ]
      }
    },
    computed: { },
    methods: {
      formatDate (date) { return date.format("MMM YYYY ") },
      formatDuration (duration) { return duration.asMonths() + " mois" },
      goTo (id) {
        this.activePart = id;
        $('html, body').animate({
            scrollTop: $('#' + id).offset().top - 50
        }, 200);
      }
    },    
    mounted () { 
      var me = this
      //CKEDITOR.inlineAll()
      //console.log(CKEDITOR)
      window.onscroll = function() {
        var scrollPosition = (document.documentElement.scrollTop || document.body.scrollTop) + 300, 
            parts = document.querySelectorAll(".part")
        
        for (let part of parts) {
          if (part.offsetTop <= scrollPosition) {
            me.activePart = part.id
          }
        }
      }
    }
  }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  #projet {


    margin: 0 auto;
    max-width: 1050px;
    

    input {
      border-width: 0 0 1px 0;
      border-color: #000;
      width: 100%;
      color: #212529;
      &.h2-size {
        font-size: 2rem;
      }
    }

    h5 {
      border-top: 1px solid $CW10;
      padding-top: 5px;
      padding-left: 3px;
    }
    
    nav {
      position: fixed;
      top: 80px;
      width: 200px;
      ul {
        list-style-type: none;
        li {
          a {
            padding: 0;
            margin: 0;
            font-size: 15px;
            font-family: ibm-plex-serif;
            outline: none;
            
            &.active {
              text-decoration: underline;
              font-weight: bold;
            }
          }
        }
      }
    }
    .contenu {
      
      margin-left: 200px;
      padding-right: 20px;
      /*max-width: 800px;*/
      table.infos {
        margin-bottom: 15px;
        width: 100%;
      }
      
      th {
        padding-top: 15px;
        font-family: ibm-plex-serif;
        font-size: 13px;
        font-weight: bold;
      }
      td {
        font-size: 13px;
        padding-right: 10px;
      }
      .steps-table {
        font-size: 12px;
      }
    }
  }

</style>
