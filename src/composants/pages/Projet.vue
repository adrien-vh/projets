<!--
 PAGE PROJET
 -->
<template>
  <div id="projet">
    <!-- Titre de la page -->
    <div class="titre-page">{{ projet.nom }}</div>
    <!-- FIN Titre de la page -->

    <!-- Alerte défaut autorisation -->
    <div class="alert alert-danger txt-center fs-18" role="alert"  v-show="niveauAcces < 0">
      <i aria-hidden="true" class="fa fa-warning fa-5x"></i><br>
      Vous n'avez pas accès à ce projet !
    </div>
    <!-- FIN Alerte défaut autorisation -->

    <!-- Barre latérale -->
    <nav v-show="niveauAcces >= 0">
      
      <!-- Menu premier niveau -->
      <ul class="menu-haut">
        <li><a href="#" :class="{ active: activePage == 'fiche' }" @click.prevent="activePage = 'fiche'; goTo('fiche')">FICHE</a></li>
        <li><a href="#" :class="{ active: activePage == 'phases' }" @click.prevent="activePage = 'phases'; goTo('phases')">PHASES</a></li>
      </ul>
      <!-- FIN Menu premier niveau -->

      <!-- Alerte obsolète -->
      <span v-show="!isLastVersion" class="fs-14 txt-white w100 txt-center ib bg-red pt20 pb20 br10 mb10">
        <i class="fa fa-exclamation-triangle fa-2x" aria-hidden="true"></i><br>
        Version obsolète !
      </span>
      <!-- FIN Alerte obsolète -->

      <!-- Météo du projet -->
      <span class="txt-center w100 ib" v-show="isLastVersion">
        <img v-if="delta < 2" src="../../assets/images/sun_116.png">
        <img v-else-if="delta < 3" src="../../assets/images/cloud_116.png">
        <img v-else-if="delta < 6" src="../../assets/images/rain_116.png">
        <img v-else src="../../assets/images/storm_116.png">
      </span>
      <!-- FIN Météo du projet -->

      <!-- Menu -->
      <ul v-show="activePage == 'fiche'">
        <li><a href="#" :class="{ active: activePart == 'fiche' }" @click.prevent="goTo('fiche')">Présentation</a></li>
        <li><a href="#" :class="{ active: activePart == 'analyse' }" @click.prevent="goTo('analyse')">Présentation détaillée</a></li>
        <li><a href="#" :class="{ active: activePart == 'financement' }" @click.prevent="goTo('financement')">Financement</a></li>
        <li><a href="#" :class="{ active: activePart == 'droits' }" @click.prevent="goTo('droits')">Droits d'accès</a></li>
      </ul>
      <ul v-show="activePage == 'phases'">
        <li><a href="#" :class="{ active: activePart == 'phases' }" @click.prevent="goTo('phases')">Calendrier</a></li>
        <li><a href="#" :class="{ active: activePart == 'fiches' }" @click.prevent="goTo('fiches')">Fiche(s) phase(s)</a></li>
      </ul>
      <!-- FIN Menu -->

      <!-- Infos et navigation versions -->
      <span class="fs-12 txt-center w100 ib">
        <router-link :to="'/projet/' + num_projetPrec" id="menuLogo" :class="{inactive : !num_projetPrec}"><i class="fa fa-chevron-left fa-lg" aria-hidden="true"></i></router-link>&nbsp;
        v. <span class="fs-18">{{ projet.version }}</span>
        &nbsp;<router-link :to="'/projet/' + num_projetSuiv" id="menuLogo" :class="{inactive : !num_projetSuiv}"><i class="fa fa-chevron-right fa-lg" aria-hidden="true"></i></router-link>
      </span> <br>
      <span v-show="projet.brouillon == '1'" class="fs-12 txt-grey w100 txt-center ib">(version provisoire)<br></span>
      <span v-show="!isLastVersion" class="fs-12 txt-red w100 txt-center ib">Version obsolète !</span>
      <!-- FIN Infos et navigation versions -->

      <!-- Boutons d'action -->
      <div class="txt-center mt20">

        <a href="#" class="round-button blue" @click.prevent="sauveProjet(false)" v-show="editing" :class="{ inactive: !unsaved }">
          <i class="fa fa-floppy-o" aria-hidden="true"></i> Enregistrer
        </a>

        <a href="#" class="round-button blue" @click.prevent="editing = true; unsaved = false" v-show="!editing && projet.brouillon == '1' && niveauAcces > 0">
          <i class="fa fa-pencil" aria-hidden="true"></i> Éditer
        </a>

        <a href="#" class="round-button blue" @click.prevent="newVersion()"  v-show="projet.brouillon == '0' && isLastVersion">
          <i class="fa fa-star" aria-hidden="true"></i> Nouvelle version
        </a>

        <a href="#" class="round-button green" v-show="projet.brouillon == '1' && niveauAcces > 1" @click.prevent="valideProjet">
          <i class="fa fa-check" aria-hidden="true"></i> Valider
        </a>
        
      </div>
      <!-- FIN Boutons d'action -->

      <!-- Alerte non enregistré -->
      <span v-show="editing && unsaved" class="fs-12 txt-white w100 txt-center ib bg-red pt10 pb10 br10 mt20">
        <i class="fa fa-exclamation-triangle fa-lg" aria-hidden="true"></i><br>
        Non enregistré !
      </span>
      <!-- FIN Alerte non enregistré -->
    </nav>
    <!-- FIN Barre latérale -->

    <!-- Fiche projet -->
    <div class="contenu" v-show="niveauAcces >= 0 && activePage=='fiche'">
      
      <table class="form" v-show="editing">
        <tr><th>Nom du projet :</th></tr>
        <tr><td><inText v-model="projet.nom" :editable="editing"></inText></td></tr>
      </table>

      <h5 id="fiche" class="part">Présentation du projet</h5>
      <formPresentation :projet="projet" :editable="editing"></formPresentation>

      <h5 id="analyse" class="part">Présentation détaillée du projet</h5>
      <formPresentationD :projet="projet" :editable="editing"></formPresentationD>      
      
      <h5 id="financement" class="part">Financement :</h5>
      <listeFinancements :projet="projet" :editable="editing"></listeFinancements>
            
      <h5 id="droits" class="part">Droits d'accès</h5>
      <listeDroits :projet="projet" :editable="editing"></listeDroits>


      
    </div>  

    <!-- Fiches phase -->
    <div class="contenu" v-show="niveauAcces >= 0 && activePage=='phases'">
      <h5 id="phases" class="part">Calendrier du projet :</h5>
      <formCalendrier :numProjet="num_projet" :etapes="etapes" :editable="editing" @select="selectEtape($event)" @update="setDelta($event)"></formCalendrier>
      
      <!-- Navigation phases -->
      <div id="fiches" class="part"></div>
      <div class="fs-18 txt-center mt20" v-show="etapeCourante !== null">
        <a href="#" :class="{ inactive: indexEtapeCourante == 0 }" @click.prevent="etapeCourante = etapes[indexEtapeCourante - 1]">
          <i aria-hidden="true" class="fa fa-chevron-left fa-lg"></i>
        </a>
        Phase {{ indexEtapeCourante + 1 }} / {{ etapes.length }}
        <a href="#" :class="{ inactive: indexEtapeCourante === etapes.length - 1}" @click.prevent="etapeCourante = etapes[indexEtapeCourante + 1]">
          <i aria-hidden="true" class="fa fa-chevron-right fa-lg"></i>
        </a>
      </div>
      <!-- FIN Navigation phases -->
      <h5 v-if="etapeCourante !== null">Phase : {{ etapeCourante.nom }}</h5>
      <ficheEtape :projet="projet" :etape="etapeCourante" :editable="editing" v-if="etapeCourante != null"></ficheEtape>
      <!-- FIN Fiches phase -->

    </div>
    <!-- FIN Fiche projet -->

  </div>
</template>

<script>
  
  /**
  * @prop {String} [num_projet="0"] Index du projet à charger
  */

  import gantt from '../elements/gantt'
  import inLongText from '../elements/inLongText'
  import inNumber from '../elements/inNumber'
  import inDuration from '../elements/inDuration'
  import inText from '../elements/inText'
  import inMonth from '../elements/inMonth'
  import formPresentation from '../elements/formPresentation'
  import formPresentationD from '../elements/formPresentationD'
  import ficheEtape from '../elements/ficheEtape'
  import formCalendrier from '../elements/formCalendrier'
  import listeFinancements from '../elements/listeFinancements'
  import listeDroits from '../elements/listeDroits'  

  export default {
    components: { inLongText, gantt, inNumber, inDuration, inText, inMonth, formPresentation, formPresentationD, ficheEtape, formCalendrier, listeFinancements, listeDroits },
    props: {
      num_projet: { type: String, default: "0" }
    },
    watch: {
      /** 
      * Chargement de la fiche projet au changement d'index
      * 
      * @param  {String}  value    Nouvel index du projet
      */
      num_projet (value) {
        if (this.num_projet !== '0') { this.editing = false }
        this.chargeProjet()
      },

      /* Détection des changements sur la fiche projet pour affichage de l'alerte non-sauvegardé */
      projet: {
        handler () { this.unsaved = true },
        deep: true
      }
    },
    data () {
      return {
        editing: true,        // Édition en cours ?
        activePart: "fiche",  // Partie affichée pour mise en surbrillance du menu
        activePage: "fiche",  // Page active (fiche ou phases)
        projet: {             // Projet par défaut
          etapes: [],
          droits: [],
          num_projetInitial: 0 
        },
        isLastVersion: true,  // Dernière version ?
        etapeCourante: null,  // Phase actuellement affichée
        num_projetPrec: null, // Index de la version précédente du projet
        num_projetSuiv: null, // Index de la version suivante du prohet
        delta: -1,            // Décalage temporel du projet (pour la météo)
        unsaved: false        // Modifications non sauvegardées ?
      }
    },
    computed: {
      /** @return  {Array}  Tableau des phases du projet */
      etapes () { return this.projet[C.ETAPES] },       
          
      /** @return  {int}    Index de la phase affichée */                      
      indexEtapeCourante () { return this.etapes.indexOf(this.etapeCourante) },

      /** @return  {int}    Niveau d'accès de l'utilisateur au projet */       
      niveauAcces () { return this.$niveauAcces(this.projet.num_projetInitial) }
    },
    methods: {
      setDelta (delta) { this.delta = delta },  // Mise à jour du décalage temporel du projet à la modification du calendrier

      /** 
      * Scroll jusqu'à la partie de fiche
      * 
      * @param  {String}  id    Id de l'élément HTML jusqu'au quel il faut scroller
      */
      goTo (id) {
        $('html, body').animate({
            scrollTop: $('#' + id).offset().top - 50
        }, 200);
      },

      /** 
      * Changement de la phase affichée lors d'un click dans le calendrier
      * 
      * @param  {Object}  etape    Phase à afficher
      */
      selectEtape (etape) {
        this.etapeCourante = etape
        this.goTo('fiches')
      },

      /* Création d'une nouvelle version du projet */
      newVersion () {
        var donnees = {}, me = this
        donnees[C.NUM_PROJET] = this.num_projet
        this.$$ServerCall (C.CREER_VERSION, function (data) {
          me.$router.push("/projet/" + data[C.NUM_PROJET])
        }, donnees)
          
      },

      /** 
      * Sauvegarde du projet
      * 
      * @param  {Boolean}  validate    Validation du projet ?
      */
      sauveProjet (validate) {
        var donnees = {}, // Données qui seront envoyées au serveur
            me = this

        donnees[C.PROJET]         = this.$stringify($.extend(true, {}, this.projet))
        donnees[C.NUM_PROJET]     = this.num_projet
        donnees[C.VALIDE_PROJET]  = validate ? '1' : '0'

        this.$$ServerCall (C.SAUVE_PROJET, function (data) {
          me.unsaved = false
          me.editing = !validate

          /* On donne les droits dans le frontend à l'utilisateur courant sur le projet
           car lors du premier enregistrement ils ne sont pas remontés */
          me.$store.commit('ajouteDroit', {
            num_projet: data[C.NUM_PROJET],
            niveau: 2
          })

          // S'il s'agit du premier enregistrement d'un nouveau projet, on le charge avec son index
          if (parseFloat(me.num_projet) === 0) { me.$router.push("/projet/" + data[C.NUM_PROJET]) }

          // Si le projet a été validé on le recharge
          if (validate) { me.chargeProjet() }
        }, donnees )
      },

      /* Chargement de la fiche projet */
      chargeProjet () {
        var donnees = {}, me = this, i, j
        donnees[C.NUM_PROJET] = this.num_projet

        this.$$ServerCall (C.CHARGE_PROJET, function (data) {
          delete data.projet.num_projet
          data.projet.budgetPrev = parseFloat(data.projet.budgetPrev)
          data.projet.dureePrev = parseFloat(data.projet.dureePrev)
          
          me.isLastVersion  = data[C.IS_LAST_VERSION]
          me.num_projetPrec = data[C.PRECEDENT]
          me.num_projetSuiv = data[C.SUIVANT]
          me.projet         = data[C.PROJET]

          // S'il y a plus d'une phase on affiche la première
          if (data.projet[C.ETAPES].length > 0) {
            me.etapeCourante = data.projet[C.ETAPES][0]
          }

          // S'il s'agit d'un nouveau projet, l'utilisateur courant est créateur
          if (parseInt(me.num_projet) === 0) {
            me.projet.createur = me.$store.state.user.login
          }

        }, donnees, [this.transformTables.projet] )
      },

      /* Validation du projet (=sauvegarde avec validation) */
      valideProjet () { 
        var me = this
        this.$showModal(
          "Validation du projet",
          'Êtes vous sûr de vouloir valider la version n°' + this.projet.version + ' du projet "' + this.projet.nom + '" ?',
          false,
          true,
          function () {
            me.sauveProjet(true)
          }
        )
      },

      /* Détection de la position du scroll pour la surbrillance du projet */
      onScroll () {
        var scrollPosition = (document.documentElement.scrollTop || document.body.scrollTop) + 200, 
            parts = $(".part:visible"),
            i,
            activePart = parts[0].id
        
        console.log(scrollPosition - 200)

        for (i = 0; i < parts.length; i += 1) {
          if (parts[i].offsetTop <= scrollPosition) {
            activePart = parts[i].id
          }
        }

        console.log(activePart)

        this.activePart = activePart
      }
    },    
    mounted () {
      if (this.num_projet !== '0') { this.editing = false } // S'il ne s'agit pas d'un nouveau projet on désactive l'édition
      this.chargeProjet()                                   // Chargement de la fiche projet
      window.addEventListener("scroll", this.onScroll)      // Ajout de l'évènement onScroll pour la surbrillance du menu
    },
    beforeDestroy () {
      window.removeEventListener("scroll", this.onScroll)   // Destruction du listener sur onScroll
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
      border-color: rgb(230, 230, 230);
      border-style: solid;
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

        &.menu-haut {
          margin-bottom: 0;
          li {
            &:first-child a {
              border-radius: 5px 5px 0 0;
              border-width: 1px 1px 0 1px;
              border-color: $CN5;
              border-style: solid;
            }
            &:last-child a {
              border-radius: 0 0 5px 5px;
              border-width: 0 1px 1px 1px;
              border-color: $CN5;
              border-style: solid;
            }

            a {
              background-color: $CN0;
              text-align: center;
              padding: 3px;
              border: none;

              &.active {
                background-color: $CB06;
              }
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
