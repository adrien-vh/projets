<template>
  <div class="gantt-container">
    <div class="gantt">
      <div class="inner-gantt" :style="{ width: (nbMonths * cellWidth) + 'px'}" ref="innerGantt">
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
        <div v-for="m in steps.length" class="gantt-row" :key="m">
          <div v-for="mo in months" class="gantt-cell" :style="{ width: cellWidth + 'px'}" :key="mo.text"></div>
        </div>
        <div v-for="m in steps.length" :key="m" >
          <div v-if="!steps[m-1].new" class="step-visual" :style="stepVisualStyleInitial(m-1)" :data-num-step="m-1"></div>
          <div class="step-visual changeable" :style="stepVisualStyle(m-1)" :data-num-step="m-1">{{ steps[m-1].nom }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import interact from '../../vendor/interact.min.js'
  import smallInText from './smallInText'
  import smallInDuration from './smallInDuration'
    
  export default {
    components: { smallInText, smallInDuration },
    props: ['value', 'editable'],
    watch: {
      value () {
        this.steps = this.value
      },
      steps () {
        setTimeout(this.setInteract, 500)
      },
      editable () {
        this.setInteract()
      }
    },
    data () {
      return {
        scales: [30, 40, 50, 70, 80],
        size: '50',
        titre: '',
        dateDebut: moment(),
        date: moment().format('L'),
        txt: {time: moment().format("DD/MM/YYYY")},
        cellWidth: 20,
        displayDuration: true,
        steps: this.value,
        interval: null
      }
    },
    computed: {
      nbMonths () { return this.ganttEndDate.diff(this.ganttStartDate, "months") },
      ganttStartDate () {
        var momentStart = this.steps.length > 0 ? this.steps[0].debut : moment()
        for (let step of this.steps) {
          if (step.debut.isBefore(momentStart)) {
            momentStart = step.debut
          }
          if (step.debutInitial.isBefore(momentStart)) {
            momentStart = step.debutInitial
          }
        }
        console.log(momentStart.format())
        return moment(momentStart).subtract(1, 'months')
      },
      ganttEndDate () {
        var momentEnd = this.steps.length > 0 ? this.endDate(this.steps[0]) : moment()
        for (let step of this.steps) {
          if (this.endDate(step).isAfter(momentEnd)) {
            momentEnd = this.endDate(step)
          }
      
          if (this.endDateInitial(step).isAfter(momentEnd)) {
            momentEnd = this.endDateInitial(step)
          }
        }
        return moment(momentEnd).add(2, 'months')
      },
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
      endDate (step) {
        return moment(step.debut).add(step.duree).subtract(1, "d")
      },
      endDateInitial (step) {
        return moment(step.debutInitial).add(step.dureeInitial).subtract(1, "d")
      },
      autoCellWidth () {
        var me = this
        setTimeout(
          function (){ me.cellWidth = Math.floor(10 * $(me.$el).find(".gantt").width() / me.nbMonths) / 10; },
          250
        )
      },
      stepVisualStyle (m) {
        var bgColor = "#999", i

        for (i = 0; i < this.$store.state.typesEtapes.length; i += 1) {
          if (this.$store.state.typesEtapes[i].num_typeEtape === this.steps[m].num_typeEtape) {
            bgColor = "#" + this.$store.state.typesEtapes[i].couleur
          }
        }

      console.log(this.cellWidth)

        return {
          top : ((this.cellWidth < 25 ? 0 : 30) + (this.cellWidth < 40 ? 30 : 0) + 6 + (30 * m)) + 'px',
          left : (this.steps[m].debut.diff(this.ganttStartDate, 'months') * this.cellWidth) + 'px',
          width : (this.steps[m].duree.asMonths() * this.cellWidth) + 'px',
          'background-color': bgColor,
          height: '16px',
          'padding-top': '1px'
        }
      },
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
      dayName (day) {
        var dayNames = ["lu", "ma", "me", "je", "ve", "sa", "di"]
        return dayNames[(this.firstDay + day - 2) % 7]
      },
      styleDiv (size) {
        return {
          height: size + 'px',
          width: size + 'px'
        }
      },
      setInteract () {
        var me = this
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
        } else {
          $(this.$el).find(".step-visual.changeable").each(
            function () {
              console.log("ici")
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
      this.interval = setInterval (function () {
        me.autoCellWidth()
      }, 1000)
    },
    beforeDestroy () {
      if (this.interval) {
        clearInterval(this.interval)
      }
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
