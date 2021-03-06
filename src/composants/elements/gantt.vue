<!--
  DIAGRAMME DE GANTT
-->
<template>
  <div class="gantt-container">
    <div class="gantt">
      <div class="inner-gantt" :style="{ width: (nbMonths * cellWidth) + 'px'}" ref="innerGantt">

        <!-- Échelle en haut du digramme :
              - Largeur de cellule < 40 : on affiche les années
              - 25 <= Largeur de cellule < 40 : on affiche les n° des mois
              - Largeur de cellule >= 40 : on affiche le nom des mois
        -->
        <div class="gantt-scale" v-if="cellWidth < 40">
          <div v-for="y in years" :style="{ width: (y.monthsIn * cellWidth) + 'px'}" :key="y.text">
            {{ y.text }}
          </div>
        </div>
        <div class="gantt-scale" v-if="cellWidth > 25">
          <div v-for="m in months" :style="{ width: cellWidth + 'px'}" :key="m.text">
            <span v-if="cellWidth < 40">{{ m.month }}</span>
            <span v-else>{{ m.text }}</span>
          </div>
        </div>
        <!-- FIN Échelle en haut du digramme -->

        <!-- Lignes vides qui contiendront les phases visuelles -->
        <div v-for="m in steps.length" class="gantt-row" :key="m">
          <div v-for="mo in months" class="gantt-cell" :style="{ width: cellWidth + 'px'}" :key="mo.text"></div>
        </div>
        <!-- FIN Lignes vides qui contiendront les phases visuelles -->

        <!-- Phase visuelles -->
        <div v-for="m in steps.length" :key="m" >
          <div v-if="!steps[m-1].new" class="step-visual" :style="stepVisualStyleInitial(m-1)" :data-num-step="m-1"></div>
          <div class="step-visual changeable" :style="stepVisualStyle(m-1)" :data-num-step="m-1">{{ steps[m-1].nom }}</div>
        </div>
        <!-- FIN Phase visuelles -->
      </div>
    </div>
  </div>
</template>

<script>
  /**
  * @prop {Array}   value     Tableau des phases
  * @prop {Boolean} editable  Contenu éditable ?
  */

  import interact from '../../vendor/interact.min.js'
  import smallInText from './smallInText'
  import smallInDuration from './smallInDuration'
    
  export default {
    components: { smallInText, smallInDuration },
    props: ['value', 'editable'],
    watch: {
      /* Mise à jour du tableau des phases */
      value () { this.steps = this.value },

      /** 
      * Quand le tableau des phases est modifié on remet en place les interaction (déplacer, redimensionner)
      * On attend 500ms pour s'assurer que l'affichage est fait
      */
      steps () { setTimeout(this.setInteract, 500) },

      /* Quand le gantt devient éditable on met en place les interactions */
      editable () { this.setInteract() }

    },
    data () {
      return {
        cellWidth: 20,      // Largeur d'une cellule sur le diagramme (= 1 mois)
        steps: this.value,  // Tableau des phases
        interval: null      // Intervalle mise en place pour mettre à jour le Gantt toutes les secondes (setInterval)
      }
    },
    computed: {
      /** @return  {int}  Nombre de mois à afficher */
      nbMonths () { return this.ganttEndDate.diff(this.ganttStartDate, "months") },

      /** @return  {moment}  Date de début du Gantt */
      ganttStartDate () {
        var momentStart = this.steps.length > 0 ? this.steps[0].debut : moment(), i
        for (i = 0; i < this.steps.length; i += 1) {
          if (this.steps[i].debut.isBefore(momentStart)) {
            momentStart = this.steps[i].debut
          }
          if (this.steps[i].debutInitial.isBefore(momentStart)) {
            momentStart = this.steps[i].debutInitial
          }
        }
        return moment(momentStart).subtract(1, 'months')
      },

      /** @return  {moment}  Date de fin du Gantt */
      ganttEndDate () {
        var momentEnd = this.steps.length > 0 ? this.$stepEndDate(this.steps[0]) : moment(), i
        for (i = 0; i < this.steps.length; i += 1) {
          if (this.$stepEndDate(this.steps[i]).isAfter(momentEnd)) {
            momentEnd = this.$stepEndDate(this.steps[i])
          }
      
          if (this.endDateInitial(this.steps[i]).isAfter(momentEnd)) {
            momentEnd = this.endDateInitial(this.steps[i])
          }
        }
        return moment(momentEnd).add(2, 'months')
      },

      /** @return  {Array}  Liste des années à afficher */
      years () {
        var i, months = this.months, currentYear = months[0].year, retour = [], monthsIn = 0
        for (i = 0; i < months.length; i += 1) {
          if (months[i].year !== currentYear) {
            retour.push({
              text : currentYear,
              monthsIn : monthsIn
            })
            monthsIn = 0
            currentYear = months[i].year
          }
          monthsIn += 1
        }
        retour.push({
          text : currentYear,
          monthsIn : monthsIn + 1
        })
        
        return retour
      },

      /** @return  {Array}  Liste des mois à afficher */
      months () {
        var endMoment = moment(this.ganttEndDate).add(1, 'months'),
            retour = [],
            currentDate = moment(this.ganttStartDate)
        do {
          retour.push({
            text : currentDate.format("MMM YY"),
            month : currentDate.format("MMM"),
            year : currentDate.format("YYYY"),
          })
          currentDate.add(1, 'months')
        } while (currentDate.isBefore(endMoment))
          
          retour.push({
            text : currentDate.format("MMM YY"),
            month : currentDate.format("MMM"),
            year : currentDate.format("YYYY"),
          })
        return retour
      }
    },
    methods: {
      /** 
      * @param    {object}  step  Phase
      * @return   {moment}  Date de fin initialement prévue de la phase
      */
      endDateInitial (step) {
        return moment(step.debutInitial).add(step.dureeInitial).subtract(1, "d")
      },

      /* Calcul de la largeur d'une cellule */
      autoCellWidth () {
        var me = this
        setTimeout(
          function (){ me.cellWidth = Math.floor(10 * $(me.$el).find(".gantt").width() / me.nbMonths) / 10; },
          250
        )
      },

      /** 
      * @param    {object}  m  Index de la phase dans le tableau des phase
      * @return   {object}  Style à appliquer à la phase visuelle
      */
      stepVisualStyle (m) {
        var bgColor = "#999", i

        for (i = 0; i < this.$store.state.typesEtapes.length; i += 1) {
          if (this.$store.state.typesEtapes[i].num_typeEtape === this.steps[m].num_typeEtape) {
            bgColor = "#" + this.$store.state.typesEtapes[i].couleur
          }
        }

        return {
          top : ((this.cellWidth < 25 ? 0 : 30) + (this.cellWidth < 40 ? 30 : 0) + 6 + (30 * m)) + 'px',
          left : (this.steps[m].debut.diff(this.ganttStartDate, 'months') * this.cellWidth) + 'px',
          width : (this.steps[m].duree.asMonths() * this.cellWidth) + 'px',
          'background-color': bgColor,
          height: '16px',
          'padding-top': '1px'
        }
      },

      /** 
      * @param    {object}  m  Index de la phase dans le tableau des phase
      * @return   {object}  Style à appliquer à la partie initiale (semi-transparent) de la phase visuelle
      */
      stepVisualStyleInitial (m) {
        var bgColor = "#999", i

        for (i = 0; i < this.$store.state.typesEtapes.length; i += 1) {
          if (this.$store.state.typesEtapes[i].num_typeEtape === this.steps[m].num_typeEtape) {
            bgColor = "#" + this.$store.state.typesEtapes[i].couleur
          }
        }

        return {
          top : ((this.cellWidth < 25 ? 0 : 30) + (this.cellWidth < 40 ? 30 : 0) + 4 + (30 * m)) + 'px',
          left : (this.steps[m].debutInitial.diff(this.ganttStartDate, 'months') * this.cellWidth) + 'px',
          width : (this.steps[m].dureeInitial.asMonths() * this.cellWidth) + 'px',
          'background-color': bgColor,
          opacity: 0.5,
          height: '20px',
          'padding-top': '2px'
        }
      },

      /* Mise en place des interaction sur les phases visuelles (déplacement, redimensionnement) */
      setInteract () {
        var me = this
        /* Si la fiche est éditable on met en place les interactions */
        if (this.editable) {
          $(this.$el).find(".step-visual.changeable").each(
            function () {
              if (!interact.isSet(this)) {
                interact(this)
                  .draggable({})
                  .resizable({ edges: { left: false, right: true, bottom: false, top: false } })
                  .on('dragmove', function (event) {
                    var target = event.target,
                              x = (parseFloat(target.getAttribute('data-x')) || 0) + event.dx

                      target.style.webkitTransform = target.style.transform = 'translate(' + x + 'px, 0px)'
                      target.setAttribute('data-x', x)
                  })
                  .on('resizemove', function (event) {
                    event.target.style.width  = (me.cellWidth * Math.round(Math.max(event.rect.width, me.cellWidth) / me.cellWidth)) + 'px'
                  })
                  .on('dragend', function (event) {
                    var target = event.target,
                        step = me.steps[parseFloat(target.getAttribute('data-num-step'))],
                        x = (parseFloat(target.getAttribute('data-x')) || 0),
                        roundedX = me.cellWidth * Math.round(x / me.cellWidth),
                        deltaMonths = Math.round(roundedX / me.cellWidth)
                  
                    step.debut = moment(step.debut).add(deltaMonths, "months")
                                      
                    target.style.webkitTransform = target.style.transform = 'translate(0px, 0px)'
                    
                    target.setAttribute('data-x', '0')               
                  }).on('resizeend', function (event){
                    var target = event.target,
                        duree = Math.max(1, Math.round(parseFloat(target.style.width.replace(/[^\d\.]/g,'') / me.cellWidth))),
                        step = me.steps[parseFloat(target.getAttribute('data-num-step'))]
                    
                    step.duree = moment.duration(duree, "months")
                  })
              }
            }
          )
        } 

        /* Si la fiche n'est pas éditable on enlève les interactions */
        else {
          $(this.$el).find(".step-visual.changeable").each(
            function () {
              if (interact.isSet(this)) {
                interact(this).unset()
              }
            }
          )
        }
      },
    },    

    mounted () {
      var me = this
      this.setInteract()

      /* Recalcul toutes les secondes de la largeur des cellules */
      this.interval = setInterval (function () {
        me.autoCellWidth()
      }, 1000)
    },
    
    beforeDestroy () {

      /* Suppression de l'intervalle utilisé pour le recalcul de la largeur des cellules  */
      if (this.interval) { clearInterval(this.interval) }
    }
  }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  $lineHeight: 30px;
  $headerWidth: 300px;
  
  .gantt-container {
    font-size: 14px;
    position: relative;
    text-align: left;

    .gantt {
      position: relative;
      width: 100%;
      display: inline-block;
      overflow-x: scroll;
      border-left : 1px solid #ddd;
      border-top : 1px solid #ddd;
      overflow: hidden;
      .inner-gantt {
        
        div.step-visual {
          position: absolute;
          border-radius: 3px;
          /*background-color: $CY35;*/
          -webkit-transform: translate(0, 0);
          transform: translate(0, 0);
          padding-left: 10px;
          font-size: 11px;
            -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; 
           white-space: nowrap;
         // overflow: hidden;
        }

        .gantt-scale {
          height: $lineHeight;
          overflow: hidden;
          /*border-top: 1px solid #ddd;*/
          border-bottom: 1px solid #ddd;
          div {
            white-space: nowrap;
            overflow: hidden;
            font-size: 11px;
            text-align: center;
            padding-top: 7px;
            height: $lineHeight;
            width: 200px;
            border-right: 1px solid #ddd;
            display: inline-block;
          }
        }

        .gantt-row {
          width: 100%;
          overflow: hidden;
          height: $lineHeight;
          &:nth-child(2n) {
            background-color: #eee;
          }
          .gantt-cell {
            display: inline-block;
            border-right: 1px solid #ddd;
            height: $lineHeight;
          }
        }
      }
    }
    
  }
</style>
