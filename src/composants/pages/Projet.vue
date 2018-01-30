<template>
  <div id="projet">
    <nav>
      <!-- MENU -->
      <ul>
        <li><a href="#" :class="{ active: activePart == 'fiche' }" @click.prevent="goTo('fiche')">Présentation</a></li>
        <li><a href="#" :class="{ active: activePart == 'analyse' }" @click.prevent="goTo('analyse')">Présentation détaillée</a></li>
        <li><a href="#" :class="{ active: activePart == 'phases' }" @click.prevent="goTo('phases')">Calendrier</a></li>
        <li><a href="#" :class="{ active: activePart == 'fiches' }" @click.prevent="goTo('fiches')">Fiche étape</a></li>
      </ul>
      <!--fin MENU -->

      <!-- METAS -->
      <span class="fs-12 serif">N° de version : <b>{{ projet.version }}</b></span> <br>
      <span v-show="projet.brouillon == '1'" class="fs-12 serif">(version provisoire)<br></span>
      <span v-show="!isLastVersion" class="fs-12 serif txt-red">version obsolète !</span>

      <button class="btn btn-primary btn-sm" @click="sauveProjet(false)" v-show="editing">
        <i class="fa fa-floppy-o" aria-hidden="true"></i> Enregistrer
      </button>

      <button class="btn btn-primary btn-sm" @click="editing = true" v-show="!editing && projet.brouillon == '1'">
        <i class="fa fa-pencil" aria-hidden="true"></i> Éditer
      </button>

      <button class="btn btn-primary btn-sm" @click="newVersion()"  v-show="projet.brouillon == '0' && isLastVersion">
        <i class="fa fa-star" aria-hidden="true"></i> Nouvelle version
      </button>

      <button class="btn btn-primary btn-sm" v-show="projet.brouillon == '1'" @click="valideProjet">
        <i class="fa fa-check" aria-hidden="true"></i> Valider la fiche
      </button><br>
      <!--{{ etapes.length }}-->
      <!--fin METAS-->
    </nav>
    <div class="contenu">

      <h2 v-show="!editing">{{ projet.nom }}</h2>
      
      <table class="form" v-show="editing">
        <tr><th>Nom du projet :</th></tr>
        <tr><td><inText v-model="projet.nom" :editable="editing"></inText></td></tr>
      </table>

      <h5 id="fiche" class="part">Présentation du projet</h5>
      <formPresentation :projet="projet" :editable="editing"></formPresentation>

      <h5 id="analyse" class="part">Présentation détaillée du projet</h5>
      <formPresentationD :projet="projet" :instances="instances" :editable="editing" :numProjet="num_projet"></formPresentationD>      
      
      <h5 id="phases" class="part">Calendrier du projet :</h5>
      <formCalendrier :numProjet="num_projet" :etapes="etapes" :editable="editing" @select="selectEtape($event)"></formCalendrier>
      

      <h5 id="fiches" class="part">Fiche étape :</h5>
      <ficheEtape :projet="projet" :etape="etapeCourante" :editable="editing" v-if="etapeCourante != null"></ficheEtape>

    </div>
    
  </div>
</template>

<script>
  import gantt from '../elements/gantt'
  import inLongText from '../elements/inLongText'
  import inNumber from '../elements/inNumber'
  import inDuration from '../elements/inDuration'
  import inText from '../elements/inText'
  import inDate from '../elements/inDate'
  import inMonth from '../elements/inMonth'
  import formPresentation from '../elements/formPresentation'
  import formPresentationD from '../elements/formPresentationD'
  import ficheEtape from '../elements/ficheEtape'
  import formCalendrier from '../elements/formCalendrier'
    
  export default {
    components: { inLongText, gantt, inNumber, inDuration, inText, inDate, inMonth, formPresentation, formPresentationD, ficheEtape, formCalendrier },
    props: {
      num_projet: { type: String, default: "0" }
    },
    watch: {
      num_projet (value) {
        if (this.num_projet !== '0') {
          this.editing = false
        }
        this.chargeProjet()
      }
    },
    data () {
      return {
        editing: true,
        activePart: "fiche",
        projet: { },
        isLastVersion: true,
        instances: [],
        etapeCourante: null,
        etapes: []
      }
    },
    computed: { },
    methods: {
      formatDuration (duration) { return duration.asMonths() + " mois" },
      goTo (id) {
        this.activePart = id;
        $('html, body').animate({
            scrollTop: $('#' + id).offset().top - 50
        }, 200);
      },
      selectEtape (etape) {
        this.etapeCourante = etape
        this.goTo('fiches')
      },
      newVersion () {
        console.log(this.projet.brouillon)
        var donnees = {}, me = this
        donnees[C.NUM_PROJET] = this.num_projet
        this.$store.state.server.call (C.CREER_VERSION, function (data) {
          me.$router.push("/projet/" + data[C.NUM_PROJET])
        }, donnees)
          
      },
      formattedSteps () {
        var retour = [], tmp, i
        for (let etape of this.etapes) {
          tmp = $.extend({}, etape)
          tmp.debut = tmp.debut.format("YYYY-MM-DD")
          if (tmp.debutInitial) {
            tmp.debutInitial = tmp.debutInitial.format("YYYY-MM-DD")
          }
          tmp.duree = tmp.duree.asMonths()
          if (tmp.dureeInitial) {
            tmp.dureeInitial = tmp.dureeInitial ? "1" : tmp.dureeInitial.asMonths()
          }
          if (tmp.transactions) {
            
            for (i = 0; i < tmp.transactions.length; i += 1) {
              console.log(tmp.transactions[i])
              tmp.transactions[i].date = tmp.transactions[i].date.format("YYYY-MM-DD")
            }
          }
          retour.push(tmp)
        }
        return retour
      },
      sauveProjet (validate) {
        var donnees = {}, me = this
        donnees[C.PROJET] = this.projet
        donnees[C.ETAPES] = this.formattedSteps()
        donnees[C.NUM_PROJET] = this.num_projet
        donnees[C.INSTANCES] = []
        donnees[C.VALIDE_PROJET] = validate ? '1' : '0'

        this.$store.state.server.call (C.SAUVE_PROJET, function (data) {
          me.editing = false
          if (parseFloat(me.num_projet) === 0) {
            me.$router.push("/projet/" + data[C.NUM_PROJET])
          } else {
            me.chargeProjet()
          }
        }, donnees )
      },
      chargeProjet () {
        var donnees = {}, me = this, i
        donnees[C.NUM_PROJET] = this.num_projet
        this.$store.state.server.call (C.CHARGE_PROJET, function (data) {
          
          delete data.projet.num_projet
          data.projet.budgetPrev = parseFloat(data.projet.budgetPrev)
          data.projet.dureePrev = parseFloat(data.projet.dureePrev)
          me.projet = data[C.PROJET]
          me.isLastVersion = data[C.IS_LAST_VERSION]
          me.instances = data[C.INSTANCES]

          for (let etape of data[C.ETAPES]) {
            
            etape['debut'] = moment(etape['debut'], "YYYY-MM-DD")
            etape['debutInitial'] = moment(etape['debutInitial'], "YYYY-MM-DD")
            etape['duree'] = moment.duration(parseFloat(etape['duree']), "months")
            etape['dureeInitial'] = moment.duration(parseFloat(etape['dureeInitial']), "months")
            
            
            for (i = 0; i < etape['transactions'].length; i += 1) {
              etape['transactions'][i]['date'] = moment(etape['transactions'][i]['date'], "YYYY-MM-DD")
              etape['transactions'][i]['montant'] = parseFloat(etape['transactions'][i]['montant'])
            }
          }

          me.etapes = data[C.ETAPES]
          if (me.etapes.length > 0) {
            me.etapeCourante = me.etapes[0]
          }
        }, donnees )
      },
      valideProjet () {
        this.sauveProjet(true)
      },
      onScroll () {
        var scrollPosition = (document.documentElement.scrollTop || document.body.scrollTop) + 100, 
            parts = document.querySelectorAll(".part")
        
        for (let part of parts) {
          if (part.offsetTop <= scrollPosition) {
            this.activePart = part.id
          }
        }
      }
    },    
    mounted () {
      if (this.num_projet !== '0') {
        this.editing = false
      }
      this.chargeProjet()
      window.addEventListener("scroll", this.onScroll)
    },
    beforeDestroy () {
      window.removeEventListener("scroll", this.onScroll)
    }
  }
</script>

<style lang="scss">
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

      button {
        cursor: pointer;
        margin: 3px 0px;
        padding: 2px 6px 2px 22px;
        width: 130px;
        position: relative;
        i {
          position: absolute;
          top: 4px;
          left: 6px;
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

          a {
            outline: none;
          }
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
