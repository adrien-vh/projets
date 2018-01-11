<template>
  <div class="gantt">
    <!--Durée : <smallInDuration v-model="steps[0].duration"></smallInDuration><br>-->
   <!-- <button v-for="scale in scales" class="btn" @click="cellWidth = scale">{{scale}}</button>
    <button class="btn" @click="autoCellWidth">auto</button>-->
    <div class="gantt">
      <div class="inner-gantt" :style="{ width: (nbMonths * cellWidth) + 'px'}">
        <div class="gantt-scale" v-if="cellWidth < 40">
          <div v-for="y in years" :style="{ width: (y.monthsIn * cellWidth) + 'px'}" :key="y.text">
            {{ y.text }}
          </div>
        </div>
        <div class="gantt-scale">
          <div v-for="m in months" :style="{ width: cellWidth + 'px'}" :key="m.text">
            <span v-if="cellWidth < 40">{{ m.month }}</span>
            <span v-else>{{ m.text }}</span>
          </div>
        </div>
        <div v-for="m in steps.length" class="gantt-row" :key="m">
          <div v-for="mo in months" class="gantt-cell" :style="{ width: cellWidth + 'px'}" :key="mo.text"></div>
        </div>
        <div v-for="m in steps.length" class="step-visual" :style="stepVisualStyle(m-1)" :data-num-step="m-1" :key="m">
          {{ steps[m-1].title }}
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  //require("../../vendor/moment.js")
  //require("../../vendor/locale/fr.js")
  import interact from '../../vendor/interact.min.js'
  import smallInText from './smallInText'
  import smallInDate from './smallInDate'
  import smallInDuration from './smallInDuration'
    
  export default {
    components: { smallInText, smallInDate, smallInDuration },
    props: ['value'],
    watch: {
      value () {
        this.steps = this.value
      },
      steps () {
        setTimeout(this.setInteract, 500)
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
        steps: this.value
      }
    },
    computed: {
      nbMonths () { return this.ganttEndDate.diff(this.ganttStartDate, "months") },
      ganttStartDate () {
        var momentStart = this.steps.length > 0 ? this.steps[0].startDate : moment()
        for (let step of this.steps) {
          if (step.startDate.isBefore(momentStart)) {
            momentStart = step.startDate
          }
        }
        console.log(momentStart.format())
        return moment(momentStart).subtract(1, 'months')
      },
      ganttEndDate () {
        var momentEnd = this.steps.length > 0 ? this.steps[0].endDate : moment()
        for (let step of this.steps) {
          if (step.endDate.isAfter(momentEnd)) {
            momentEnd = step.endDate
          }
        }
        return moment(momentEnd).add(1, 'months')
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
      updateEndDate (step) {
        step.endDate = moment(step.startDate).add(step.duration).subtract(1, "d")
        this.autoCellWidth()
      },
      validateDates (step) {
        if (step.endDate.isBefore(step.startDate)) {
          step.endDate = moment(step.startDate).add(1, "months")
        }
        step.duration = moment.duration(Math.round(moment.duration(step.endDate.diff(step.startDate)).asMonths()), "months")
        this.autoCellWidth()
      },
      autoCellWidth () {
        this.cellWidth = Math.floor(10 * $(this.$el).find(".gantt").width() / this.nbMonths) / 10
      },
      addStep () {
        this.steps.push({
          startDate: moment(this.ganttEndDate).subtract(1, "months").add(1,"d"),
          endDate: moment(this.ganttEndDate),
          text: "Nouvelle étape",
          duration: moment.duration(1, "months")
        })
        this.autoCellWidth()
      },
      stepStart (step) {
        return step.startDate.format("D MMM YY")
      },
      stepDuration (step) {
        return moment.duration(step.endDate.diff(step.startDate)).add(1, "d").humanize().replace(" ", "&nbsp;")
      },
      stepVisualStyle (m) {
        
        return {
          top : (30 + (this.cellWidth < 40 ? 30 : 0) + 4 + (30 * m)) + 'px',
          left : (this.steps[m].startDate.diff(this.ganttStartDate, 'months') * this.cellWidth) + 'px',
          width : (this.steps[m].duration.asMonths() * this.cellWidth) + 'px'
        }
      },
      dayName (day) {
        var dayNames = ["lu", "ma", "me", "je", "ve", "sa", "di"]
        return dayNames[(this.firstDay + day - 2) % 7]
        //return moment(this.ganttStartDate,"DDMMYY").add(day-1, "day").format('dd')
      },
      styleDiv (size) {
        return {
          height: size + 'px',
          width: size + 'px'
        }
      },

      setInteract () {
        var me = this
        $(this.$el).find(".step-visual").each(
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
                
                  step.startDate = moment(step.startDate).add(deltaMonths, "months")
                  step.endDate = moment(step.endDate).add(deltaMonths, "months")
                
                  target.style.webkitTransform = target.style.transform = 'translate(0px, 0px)'
                  //target.style.left = (parseFloat(target.style.left.replace(/\D/g,'')) + roundedX) + 'px'
                  target.setAttribute('data-x', '0')               
                  me.autoCellWidth()
                }).on('resizeend', function (event){
                  var target = event.target,
                      duration = Math.max(1, Math.round(parseFloat(target.style.width.replace(/[^\d\.]/g,'') / me.cellWidth))),
                      //roundedWidth = me.cellWidth * duration  - 1,
                      step = me.steps[parseFloat(target.getAttribute('data-num-step'))]
                  
                  //target.style.width  = roundedWidth + 'px'
                  step.endDate = moment(step.startDate).add(duration, "months")
                  me.validateDates(step)
                  me.autoCellWidth()
                })
            }
          }
        )
      },
    },    
    mounted () {
      this.setInteract()
      this.autoCellWidth()
    }
  }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  $lineHeight: 30px;
  $headerWidth: 300px;
  
  .gantt {
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
      .inner-gantt {
        
        div.step-visual {
          position: absolute;
          height: 20px;
          border-radius: 3px;
          background-color: $CY35;
          -webkit-transform: translate(0, 0);
          transform: translate(0, 0);
          padding-left: 10px;
          font-size: 11px;
          padding-top: 2px;
            -webkit-touch-callout: none; /* iOS Safari */
    -webkit-user-select: none; /* Safari */
     -khtml-user-select: none; /* Konqueror HTML */
       -moz-user-select: none; /* Firefox */
        -ms-user-select: none; /* Internet Explorer/Edge */
            user-select: none; 
           white-space: nowrap;
          overflow: hidden;
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
