<template>
  <div id="projet">
    <nav>
      <!-- MENU -->
      <span v-show="!isLastVersion" class="fs-14 txt-white w100 txt-center ib bg-red pt20 pb20 br10 mb10">
        <i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i><br>
        Version obsolète !
      </span>
      
      <span class="txt-center w100 ib" v-show="isLastVersion">
        <img v-if="delta < 2" src="../../assets/images/sun_116.png">
        <img v-else-if="delta < 3" src="../../assets/images/cloud_116.png">
        <img v-else-if="delta < 6" src="../../assets/images/rain_116.png">
        <img v-else src="../../assets/images/storm_116.png">
      </span>
      <ul>
        <li><a href="#" :class="{ active: activePart == 'fiche' }" @click.prevent="goTo('fiche')">Présentation</a></li>
        <li><a href="#" :class="{ active: activePart == 'analyse' }" @click.prevent="goTo('analyse')">Présentation détaillée</a></li>
        <li><a href="#" :class="{ active: activePart == 'phases' }" @click.prevent="goTo('phases')">Calendrier</a></li>
        <li><a href="#" :class="{ active: activePart == 'fiches' }" @click.prevent="goTo('fiches')">Fiche étape</a></li>
      </ul>
      <!--fin MENU -->

      <!-- METAS -->
      <span class="fs-12 txt-center w100 ib">
        <router-link :to="'/projet/' + num_projetPrec" id="menuLogo" v-if="num_projetPrec"><i class="fa fa-chevron-left fa-lg" aria-hidden="true"></i></router-link>&nbsp;
        v. <span class="fs-18">{{ projet.version }}</span>
        &nbsp;<router-link :to="'/projet/' + num_projetSuiv" id="menuLogo" v-if="num_projetSuiv"><i class="fa fa-chevron-right fa-lg" aria-hidden="true"></i></router-link>
      </span> <br>
      <span v-show="projet.brouillon == '1'" class="fs-12 txt-grey w100 txt-center ib">(version provisoire)<br></span>
      <span v-show="!isLastVersion" class="fs-12 txt-red w100 txt-center ib">Version obsolète !</span>

      <div class="txt-center mt20">

        <a href="#" class="round-button blue" @click.prevent="sauveProjet(false)" v-show="editing">
          <i class="fa fa-floppy-o" aria-hidden="true"></i> Enregistrer
        </a>

        <a href="#" class="round-button blue" @click.prevent="editing = true" v-show="!editing && projet.brouillon == '1'">
          <i class="fa fa-pencil" aria-hidden="true"></i> Éditer
        </a>

        <a href="#" class="round-button blue" @click.prevent="newVersion()"  v-show="projet.brouillon == '0' && isLastVersion">
          <i class="fa fa-star" aria-hidden="true"></i> Nouvelle version
        </a>

        <a href="#" class="round-button green" v-show="projet.brouillon == '1'" @click.prevent="valideProjet">
          <i class="fa fa-check" aria-hidden="true"></i> Valider
        </a>
        
      </div>
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
      <formCalendrier :numProjet="num_projet" :etapes="etapes" :editable="editing" @select="selectEtape($event)" @update="setDelta($event)"></formCalendrier>
      
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
        etapes: [], 
        num_projetPrec: null,
        num_projetSuiv: null,
        delta: -1
      }
    },
    computed: { },
    methods: {
      formatDuration (duration) { return duration.asMonths() + " mois" },
      setDelta (delta) {
        this.delta = delta
      },
      goTo (id) {
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
        donnees[C.INSTANCES] = this.instances
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
          me.num_projetPrec = data[C.PRECEDENT]
          me.num_projetSuiv = data[C.SUIVANT]

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
        var scrollPosition = (document.documentElement.scrollTop || document.body.scrollTop) + 200, 
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
      background-color: $CN5;
      color: #fff;
      margin-top: 25px;
      padding: 2px 5px;
      border-radius: 3px;
    }
    
    nav {
      position: fixed;
      top: 40px;
      width: 180px;
      margin-left: 0;
      padding: 10px;
      /*text-align: center;*/
      /*background-color: $CW00;*/
      ul {
        list-style-type: none;
        margin-left: 0;
        padding-left: 0;
        li {
          width: 100%;
          a {
            width: 100%;
            padding: 10px;
            margin: 0;
            font-size: 13px;
            font-family: ibm-plex-serif;
            outline: none;
            display: inline-block;
            border-top: 1px solid $CW1;
            color: $CN5;
            transition: all .2s ease-in-out;
            
            &.active {           
              background-color: $CN5;
              color: #fff;
            }

            &:hover {
              text-decoration: none;
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
