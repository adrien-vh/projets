<template>
  <div id="projet">
    {{titre}}<br>
    Étape : <smallInText v-model="titre" placeholder="Titre de l'étape"></smallInText>
    <br>
    Date de début : <smallInDate v-model="dateDebut" placeholder="Date de début"></smallInDate>
    <br>
    <br>
    Durée : <smallInDuration v-model="steps[0].duration"></smallInDuration><br>
    <!--{{ganttStartDate}}&nbsp;{{ganttEndDate}}-->&nbsp;{{nbDays}}&nbsp;{{cellWidth}}<br>
    <button v-for="scale in scales" class="btn" @click="cellWidth = scale">{{scale}}</button>
    <button class="btn" @click="autoCellWidth">auto</button>
    <br>
    <button class="btn" @click="displayDuration = true">Durée</button>
    <button class="btn" @click="displayDuration = false">Date de fin</button>
    <br>
    <div class="steps-list" >
      <div class="steps-spacer" v-show="cellWidth > 9 || cellWidth < 2"></div>
      <div class="steps-spacer" v-show="cellWidth > 18"></div>
      <table>
        <thead>
          <tr>
            <th>Phase</th>
            <th>Début</th>
            <th v-show="!displayDuration"><a href="#" @click.prevent="displayDuration = true">Fin</a></th>
            <th v-show="displayDuration"><a href="#" @click.prevent="displayDuration = false">Durée</a></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="step in steps">
            <td>
              <a href="#" class="but-develop"><i class="fa fa-caret-square-o-down" aria-hidden="true"></i></a>
              <span>{{ step.text }}</span>
            </td>
            <td><smallInDate v-model="step.startDate" @input="validateDates(step)"></smallInDate></td>
            <td v-show="!displayDuration"><smallInDate v-model="step.endDate" @input="validateDates(step)"></smallInDate></td>
            <td v-show="displayDuration" class="duration"><smallInDuration v-model="step.duration" @input="updateEndDate(step)"></smallInDuration></td>
          </tr>
          <tr>
            <td colspan="3"><button class="btn btn-sm btn-primary" @click="addStep">Nouvelle étape</button></td>
          </tr>
        </tbody>
      </table>
    </div><div class="gantt">
      <div class="inner-gantt" :style="{ width: (nbDays * cellWidth) + 'px'}">
        <div class="gantt-scale" v-if="cellWidth < 2">
          <div v-for="y in years" :style="{ width: (y.daysIn * cellWidth) + 'px'}">
            {{ y.text }}
          </div>
        </div>
        <div class="gantt-scale">
          <div v-for="m in months" :style="{ width: (m.daysIn * cellWidth) + 'px'}">
            <span v-if="cellWidth < 2">{{ m.month }}</span>
            <span v-else>{{ m.text }}</span>
          </div>
        </div>
        <div class="gantt-scale" v-if="cellWidth > 9">
          <div v-for="m in weeks" :style="{ width: (m.daysIn * cellWidth) + 'px'}">
            {{ m.text }}
          </div>
        </div>
        <div class="gantt-scale" v-if="cellWidth > 18">
          <div v-for="m in nbDays" :style="{ width: cellWidth + 'px'}">
            {{ dayName(m) }}
          </div>
        </div>
        <div v-for="m in steps.length + 1" class="gantt-row">
          <div v-if="cellWidth >= 10" v-for="n in nbDays" class="gantt-cell" :style="{ width: cellWidth + 'px'}"></div>
          <div v-if="cellWidth < 10" v-for="m in weeks" class="gantt-cell" :style="{ width: (m.daysIn * cellWidth) + 'px'}"></div>
        </div>
        <div v-for="m in steps.length" class="step-visual" :style="stepVisualStyle(m-1)" :data-num-step="m-1">
          
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  //require("../../vendor/moment.js")
  //require("../../vendor/locale/fr.js")
  import interact from '../../vendor/interact.min.js'
  import myDatepicker from '../elements/vue-datepicker'
  import smallInText from '../elements/smallInText'
  import smallInDate from '../elements/smallInDate'
  import smallInDuration from '../elements/smallInDuration'
    
  export default {
    components: { myDatepicker, smallInText, smallInDate, smallInDuration },
    watch: { 
      steps () {
        setTimeout(this.setInteract, 500)
      }
    },
    data () {
      return {
        scales: [1, 1.5, 2, 3, 5, 7, 10, 15, 20, 25],
        size: '50',
        titre: '',
        dateDebut: moment(),
        date: moment().format('L'),
        txt: {time: moment().format("DD/MM/YYYY")},
        cellWidth: 5,
        displayDuration: true,
        steps: [
          { 
            startDate: moment("050118", "DDMMYY"),
            endDate: moment("310718", "DDMMYY"),
            text: "Première étape avec un nom un peu long",
            duration: moment.duration(moment("310718", "DDMMYY").diff(moment("050118", "DDMMYY"))).add(1, "d")
          },
          { 
            startDate: moment("050217", "DDMMYY"),
            endDate: moment("280218", "DDMMYY"),
            text: "Seconde étape",
            duration: moment.duration(moment("280218", "DDMMYY").diff(moment("050217", "DDMMYY"))).add(1, "d")
          }
        ]
      }
    },
    computed: {
      firstDay () { return parseFloat(this.ganttStartDate.format("d"))  }, 
      nbDays () { return this.ganttEndDate.diff(this.ganttStartDate, "days") },
      ganttStartDate () {
        var momentStart = this.steps[0].startDate
        for (let step of this.steps) {
          if (step.startDate.isBefore(momentStart)) {
            momentStart = step.startDate
          }
        }
        return moment(momentStart).subtract(1, 'months')
      },
      ganttEndDate () {
        var momentEnd = this.steps[0].endDate
        for (let step of this.steps) {
          if (step.endDate.isAfter(momentEnd)) {
            momentEnd = step.endDate
          }
        }
        return moment(momentEnd).add(1, 'months')
      },
      years () {
        var i, months = this.months, currentYear = months[0].year, daysIn = 0, retour = []
        for (i = 0; i < months.length; i += 1) {
          if (months[i].year !== currentYear) {
            retour.push({
              text : currentYear,
              daysIn : daysIn
            })
            daysIn = 0
            currentYear = months[i].year
          }
          daysIn += months[i].daysIn
        }
        retour.push({
          text : currentYear,
          daysIn : daysIn + 1
        })
        
        return retour
      },
      months () {
        var endMoment = moment(this.ganttEndDate).add(1, 'months'),
            retour = [],
            currentDate = moment(this.ganttStartDate),
            isFirst = true,
            daysIn
        do {
          daysIn = currentDate.daysInMonth() - (isFirst ? (parseFloat(currentDate.format('D')) - 1) : 0)
          retour.push({
            text : currentDate.format("MMM YY"),
            month : currentDate.format("MMM"),
            year : currentDate.format("YYYY"),
            daysIn : Math.min(daysIn, this.ganttEndDate.diff(currentDate.clone().startOf('month'), "days"))
          })
          currentDate.add(1, 'months')
          isFirst = false
        } while (currentDate.isBefore(endMoment))
        return retour
      },
      
      weeks () {
        var endMoment = moment(this.ganttEndDate).add(1, 'weeks'),
            retour = [],
            currentDate = moment(this.ganttStartDate),
            deltaFirst = parseFloat(currentDate.format('e'))
                
        do {
          retour.push({
            text : currentDate.format("DD") + " - " + currentDate.clone().add(6 - deltaFirst, "days").format("DD/MM"),
            daysIn : Math.min( 7 - deltaFirst, this.ganttEndDate.diff(currentDate.clone().startOf('week'), "days"))
           
          })
          currentDate.add(7 - deltaFirst, 'days')
          deltaFirst = 0
        } while (currentDate.isBefore(endMoment))
        return retour
      },
    },
    methods: {
      updateEndDate (step) {
        step.endDate = moment(step.startDate).add(step.duration).subtract(1, "d")
        this.autoCellWidth()
      },
      validateDates (step) {
        if (step.endDate.isBefore(step.startDate)) {
          step.endDate = moment(step.startDate).add(2, "days")
        }
        step.duration = moment.duration(step.endDate.diff(step.startDate)).add(1, "d")
        this.autoCellWidth()
      },
      autoCellWidth () {
        this.cellWidth = Math.floor(100 * $(this.$el).find(".gantt").width() / this.nbDays) / 100
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
          top : (30 + (this.cellWidth > 9 ? 30 : 0) + (this.cellWidth > 18 ? 30 : 0) + (this.cellWidth < 2 ? 30 : 0) + 4 + (30 * m)) + 'px',
          left : (this.steps[m].startDate.diff(this.ganttStartDate, 'days') * this.cellWidth) + 'px',
          width : (this.steps[m].duration.asDays() * this.cellWidth) + 'px'
        }
      },
      dayName (day) {
        var dayNames = ["lu", "ma", "me", "je", "ve", "sa", "di"]
        return dayNames[(this.firstDay + day - 2) % 7]
        //return moment(this.ganttStartDate,"DDMMYY").add(day-1, "day").format('dd')
      },
      styleDiv (size) {
        console.log(moment(this.txt.time, "DD/MM/YYYY").format('LL'));
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
                  event.target.style.width  = Math.max(event.rect.width, 2 * me.cellWidth) + 'px'
                })
                .on('dragend', function (event) {
                  var target = event.target,
                      step = me.steps[parseFloat(target.getAttribute('data-num-step'))],
                      x = (parseFloat(target.getAttribute('data-x')) || 0),
                      roundedX = me.cellWidth * Math.round(x / me.cellWidth),
                      deltaDays = Math.round(roundedX / me.cellWidth)
                
                  step.startDate = moment(step.startDate).add(deltaDays, "days")
                  step.endDate = moment(step.endDate).add(deltaDays, "days")
                
                  target.style.webkitTransform = target.style.transform = 'translate(0px, 0px)'
                  //target.style.left = (parseFloat(target.style.left.replace(/\D/g,'')) + roundedX) + 'px'
                  target.setAttribute('data-x', '0')               
                  me.autoCellWidth()
                  //console.log(parseFloat(target.style.left.replace(/\D/g,'')))

                  //console.log(event.target.getAttribute('data-x'))
                }).on('resizeend', function (event){
                  var target = event.target,
                      duration = Math.max(2, Math.round(parseFloat(target.style.width.replace(/[^\d\.]/g,'') / me.cellWidth))),
                      //roundedWidth = me.cellWidth * duration  - 1,
                      step = me.steps[parseFloat(target.getAttribute('data-num-step'))]
                  
                  console.log(duration)
                  
                  //target.style.width  = roundedWidth + 'px'
                  step.endDate = moment(step.startDate).add(duration - 1, "days")
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
  
  #projet {
    font-size: 14px;
    position: relative;
    .steps-list {
      width: $headerWidth;
      border: 1px solid #ddd;
      display: inline-block;
      vertical-align: top;
      font-size: 12px;
      .steps-spacer {
        height: $lineHeight;
      }
      table {
        border-collapse: collapse;
        width: $headerWidth;
        tr {
          height: 30px;
          th a {
            padding-top: 4px;
          }
          td, th {
            * {
              display: inline-block;
              max-height: 20px !important;
              overflow: hidden;
            }
            position: relative;
            height: 30px;
            border: 1px solid #ddd;
            border-width: 0 0 1px 0;
            text-align: center;
            padding: 0;
            
            .but-develop {
              position: absolute;
              top: 4px;
              left: 2px;
            }
            
            &.duration {
              width: 115px;
            }
            
            &:last-child {
              padding-right: 5px;
            }
            
            &:first-child {
              padding-left: 15px;
            }
          }
          
          &:last-child {
            td, th {
              border-width: 0;
            }
          }
        }
        button {
          font-size: 11px;
          cursor: pointer;
          padding: 2px 5px;
        }
      }
    }
    
    .gantt {
      position: relative;
      width: calc(100% - #{$headerWidth});
      display: inline-block;
      overflow-x: scroll;
      .inner-gantt {
        border-top : 1px solid #ddd;
        div.step-visual {
          position: absolute;
          height: 20px;
          border-radius: 3px;
          background-color: $CR14;
          -webkit-transform: translate(0, 0);
          transform: translate(0, 0);
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
