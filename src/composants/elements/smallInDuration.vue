<template>
  <div>
    <input 
           type="text"
           v-model="currentValue"
           @keyup="onKeyUp"
           maxlength="4"
    >
    <span v-show="editing">
      <a href="#" class="btn-unit" :class="{ active: currentUnit == 'd' }" @click.prevent="stopEdit('d')">J</a><a href="#" class="btn-unit" :class="{ active: currentUnit == 'w' }" @click.prevent="stopEdit('w')">S</a><a href="#" class="btn-unit" :class="{ active: currentUnit == 'M' }" @click.prevent="stopEdit('M')">M</a><a href="#" class="btn-unit" :class="{ active: currentUnit == 'y' }" @click.prevent="stopEdit('y')">A</a>
    </span>
    <a href="#" @click.prevent="startEdit" class="value" v-html="displayedValue"></a>
  </div>
</template>

<script>
export default {
  data () {
    return {
      editing: false,
      currentValue: this.durationInfos().number,
      currentUnit: this.durationInfos().unit
    }
  },
  watch: {
    value (value) {
      this.currentValue = this.durationInfos().number
      this.currentUnit = this.durationInfos().unit
    }
  },
  props: {
    value: Object,
  },
  computed: {
    displayedValue () { 
      var unit, infos = this.durationInfos()
      switch (infos.unit) {
        case "y":
          unit = infos.number === 1 ? "an" : "ans"
          break
        case "M":
          unit = "mois"
          break
        case "w":
          unit = infos.number === 1 ? "semaine" : "semaines"
          break
        default :
          unit = infos.number === 1 ? "jour" : "jours"
      }
      return infos.number + "&nbsp;" + unit 
    }
  },
  methods: {
    durationInfos () {
      if (this.value.asYears() % 1 === 0) {
        return { number : this.value.asYears(), unit : "y" }
      }
      if (this.value.asMonths() % 1 === 0) {
        return { number : this.value.asMonths(), unit : "M" }
      }
      if (this.value.asWeeks() % 1 === 0) {
        return { number : this.value.asWeeks(), unit : "w" }
      }
      return { number : Math.round(this.value.asDays()), unit : "d" }
    },
    startEdit () {
      this.editing = true
      $(this.$el).find('a.value').hide()
      $(this.$el).find('input').show()
      $(this.$el).find('input').select().focus()
      this.$emit('startEdit')
    },
    stopEdit (unit) {
      var currentValue = parseFloat(this.currentValue)
      this.editing = false
      $(this.$el).find('input').hide()
      $(this.$el).find('a.value').show()
      this.$emit('input', moment.duration(currentValue, unit))
    },
    onKeyUp (e) {
      var currentValue = $(this.$el).find('input').val().replace(/\D/g, '')        
      $(this.$el).find('input').val(currentValue)
    }
  }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  div {
    display: inline-block;
  }
  
  a.value {
    cursor: pointer;
    padding-bottom: 1px;
    display: inline-block;
    width: 80px;
    font-size: 12px;
  }
  
  a.btn-unit {
    border-radius: 2px;
    border: 1px solid #555;
    padding: 1px 3px 0;
    font-size: 11px;
    color: #000;
    outline: none;
    
    &:hover {
      text-decoration: none;
      background-color: $CB00;
    }
    
    &.active {
      background-color: $CB02;
    }
  }
  
  input {
    border-width: 0 0 1px 0;
    border-bottom: 1px solid #555;
    padding: 0;
    width: 40px;
    display: none;
    text-align: center;
    font-size: 12px;
  }

</style>
