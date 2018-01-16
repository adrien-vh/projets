<template>
  <div id="projet">
    <nav>
      <ul>
        <li>
          <a href="#" :class="{ active: activePart == 'fiche' }" @click.prevent="goTo('fiche')">Fiche projet</a>
        </li>
        <li>
          <a href="#" :class="{ active: activePart == 'analyse' }" @click.prevent="goTo('analyse')">Présentation détaillée du projet</a>
        </li>
        <li>
          <a href="#" :class="{ active: activePart == 'phases' }" @click.prevent="goTo('phases')">Phases</a>
        </li>
        <li>
          <a href="#" :class="{ active: activePart == 'fiches' }" @click.prevent="goTo('fiches')">Fiches étape</a>
        </li>
      </ul>
      <button class="btn btn-primary btn-sm" @click="sauveProjet"><i class="fa fa-floppy-o" aria-hidden="true"></i> Enregistrer</button>
    </nav>
    <div class="contenu">
      <!--<h2>Fiche projet</h2>-->

      <h2 v-show="!editing">{{ projet.nom }}</h2>
      
      <table class="form">
        <tr v-show="editing">
          <th colspan="2">Nom du projet :</th>
        </tr>
        <tr v-show="editing">
          <td colspan="2">
            <input v-model="projet.nom" type="text">
          </td>
        </tr>
      </table>
      <h5 id="fiche" class="part">Présentation du projet</h5>
      <table class="form">
        <tr>
          <th colspan="2">Descriptif sommaire :</th>
        </tr>
         <tr>
          <td colspan="2">
            <inLongText v-model="projet.description" placeholder="Description du projet" v-show="editing"></inLongText>
            <div class="fs-13 txt-justify" v-html="projet.description" v-show="!editing"></div>
          </td>
        </tr>
        <tr>
          <th>Pilote politique du projet : </th>
          <th>Chef de projet : </th>
        </tr>
        <tr>
          <td><input v-model="projet.pilotePolitique" type="text" class="w100"></td>
          <td><input v-model="projet.chefProjet" type="text" class="w100"></td>
        </tr>
        <tr>
          <th>Equipe projet interne :  </th>
          <th>Instances de pilotage du projet : </th>
        </tr>
        <tr>
          <td><input v-model="projet.equipeProjet" type="text" class="w100"></td>
          <td><input v-model="projet.instances" type="text" class="w100"></td>
        </tr>
        <tr>
          <th colspan="2">Partenaires externes impliqués :</th>
        </tr>
        <tr>
          <td colspan="2"><inLongText v-model="projet.partenaires" placeholder="Description du projet" v-show="editing"></inLongText></td>
        </tr>
        
        <tr>
          <th>Budget prévisionnel :  </th>
          <th>Durée prévisionnelle : </th>
        </tr>
        <tr>
          <!--<td><input v-model="projet.budgetPrev" type="text" maxlength="6" class="w100p"> €</td>-->
          <td><inNumber v-model="projet.budgetPrev" :maxLength="6" suffix="k€"></inNumber></td>
          <td><inNumber v-model="projet.dureePrev" :maxLength="2" suffix="mois"></inNumber></td>
        </tr>
      </table>
      <h5 id="analyse" class="part">Présentation détaillée du projet</h5>
      <table class="form">
        <tr>
           <th colspan="2">Objectifs et enjeux du projet :</th>
        </tr>
        <tr>
          <td colspan="2">
            <inLongText v-model="projet.description" placeholder="Description du projet" v-show="editing"></inLongText>
          </td>
        </tr>
        <tr>
           <th colspan="2">Planning détaillé du projet :</th>
        </tr>
      </table>      
      <table class="table table-hover table-sm table-striped infos">
        <thead>
          <tr>
            <th>Étapes / instance</th>
            <th>Dates</th>
            <th>Commentaires</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
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
      </table>
      
      
      <h5 id="phases" class="part">Calendrier du projet :</h5>
      <table class="table table-hover table-sm table-striped infos">
        <thead class="">
          <tr>
            <th>Étape</th>
            <th>Commentaires</th>
            <th>Début</th>
            <!--<th>Fin</th>-->
            <th>Durée</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="step in steps" :key="step.num_step">
            <td>{{ step.title }}</td>
            <td>{{ step.description }}</td>
            <td>{{ formatDate(step.startDate) }}</td>
            <!--<td>{{ formatDate(step.endDate) }}</td>-->
            <td><inDuration v-model="step.duration"></inDuration></td>
            <td><button class="btn btn-danger btn-sm pointer mb10">Suppr.</button></td>
          </tr>
          <tr>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td><input type="text"></td>
            <td>
              <button class="btn btn-primary btn-sm pointer mb10">Valider</button>
            </td>
          </tr>
        </tbody>
      </table>
      <!--<button class="btn btn-primary btn-sm pointer mb10">Ajouter une étape</button>-->
      <gantt v-model="steps"></gantt>
      <br>
      <h5 id="fiches" class="part">Fiches étapes :</h5>
      <div class="serif fs-13 mt10 bold">Objectifs :</div>
      <inLongText v-model="steps[0].description" placeholder="Description du projet" v-show="editing"></inLongText>
      <div class="serif fs-13 mt10 bold">Dépenses de fonctionnement (prestation, animation, expertise ...) :</div>
      <table class="table table-hover table-sm table-striped infos">
        <thead>
          <tr>
            <th>Date de réalisation <span>(paiement)</span></th>
            <th>Montant</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr>
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
      <!--<button class="btn btn-primary btn-sm pointer mb10">Ajouter une dépense</button>-->
      <div class="serif fs-13 mt10 bold">Dépenses d'investissement :</div>
      <div class="serif fs-13 mt10 bold">Recettes de fonctionnement :</div>
      <div class="serif fs-13 mt10 bold">Recettes d'investissement :</div>
      
    </div>
    
  </div>
</template>

<script>
  import gantt from '../elements/gantt'
  import inLongText from '../elements/inLongText'
  import inNumber from '../elements/inNumber'
  import inDuration from '../elements/inDuration'
    
  export default {
    components: { inLongText, gantt, inNumber, inDuration },
    data () {
      return {
        editing: true,
        activePart: "fiche",
        num_projet: -1,
        projet: { },
        steps: [
          { 
            num_step: 1,
            startDate: moment("010118", "DDMMYY"),
            //endDate: moment(moment("010118", "DDMMYY")).add(5, "months"),
            title: "Première étape avec un nom un peu long",
            description: "Première étape avec un nom un peu long",
            duration: moment.duration(5, "months")
          },
          { 
            num_step: 2,
            startDate: moment("010217", "DDMMYY"),
            // endDate: moment(moment("010217", "DDMMYY")).add(2, "months"),
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
      },
      formattedSteps () {
        var retour = []
        for (let step of this.steps) {
          retour.push(
            {
              num_step: step.num_step,
              startDate: step.startDate.format("YYYY-MM-DD"),
              title: step.title,
              description: step.description,
              duration: step.duration.asMonths(),
              num_step: this.num_projet
            }
          )
        }
        return retour
      },
      sauveProjet () {
        var donnees = {}
        donnees[C.PROJET] = this.projet
        donnees[C.ETAPES] = this.formattedSteps()
        this.$store.state.server.call (C.SAUVE_PROJET, function (data) {
          console.log(data)
        }, donnees )
      }
    },    
    mounted () { 
      var me = this
      //CKEDITOR.inlineAll()
      //console.log(CKEDITOR)
      window.onscroll = function() {
        var scrollPosition = (document.documentElement.scrollTop || document.body.scrollTop) + 100, 
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
      border-color: rgb(175, 175, 175);
      color: #212529;

      &.h2-size {
        font-size: 2rem;
      }
    }

    h5 {
      background-color: $CB97;
      color: #fff;
      margin-top: 25px;
      padding: 2px 5px;
      border-radius: 3px;
    }
    
    nav {
      position: fixed;
      top: 80px;
      width: 200px;
      margin-left: 0;
      ul {
        list-style-type: none;
        margin-left: 0;
        padding-left: 0;
        li {
          a {
            padding: 0 20px 0 0;
            margin: 0;
            font-size: 13px;
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
        margin-top: 20px;

        button {
          padding: 2px;
          font-size: 12px;
          width: 100%;
          margin: 0;
        }

        input {
          background-color: transparent;
          width: 100%;
        }
        td {
          font-size: 11px;
          vertical-align: middle;
        }
        th {
          background-color: $CB60;
          padding: 2px;
          font-size: 12px;
          font-weight: bold;
          padding-left: 3px;
          span {
            font-size: 10px;
          }
        }
      }

      table.form {
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
