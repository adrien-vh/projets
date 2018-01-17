<template>
  <div>
    <input 
           type="text"
           v-model="currentValue"
           @keyup="onKeyUp"
           @blur="onBlur"
           maxlength="10"
    >
    <a href="#" @click.prevent="startEdit">{{ displayedValue }}</a>
  </div>
</template>

<script>
export default {
  data () {
    return {
      editing: false,
      currentValue: this.value.format("DD/MM/YY")
    }
  },
  watch: {
    value (value) {
      this.currentValue = this.value.format("DD/MM/YY")
    }
  },
  props: {
    value: Object
  },
  computed: {
    displayedValue () { return this.value.format("D MMM YY") }
  },
  methods: {
    startEdit () {
      this.editing = true
      $(this.$el).find('a').hide()
      $(this.$el).find('input').show()
      $(this.$el).find('input').select().focus()
    },
    stopEdit () {
      var currentValue = $(this.$el).find('input').val(),
          currentMoment = null,
          validDayFormats = ["DD", "D"],
          validMonthFormats = ["MM", "M"],
          validYearFormats = ["YYYY", "YY"],
          validSeparators = ["", "/", "-"]
      
      for (let d of validDayFormats) for (let m of validMonthFormats) for (let y of validYearFormats) for (let s of validSeparators) {
        if (moment(currentValue, d + s + m + s + y, true).isValid()) {
          currentMoment = moment(currentValue, d + s + m + s + y)
        }
      }
      
      if (currentMoment === null) {
        return
      }

      this.editing = false
      $(this.$el).find('input').hide()
      $(this.$el).find('a').show()
      this.currentValue = currentMoment.format("DD/MM/YY")
      this.$emit('input', currentMoment)
    },
    onKeyUp (e) {
      if (e.keyCode === 13) {
        this.stopEdit()
      }
      if (e.keyCode !== 8) {
        var currentValue = $(this.$el).find('input').val().replace(/[^\/\d-]/g, '')
        if (currentValue.length === 10) {
          this.stopEdit()
        }
        
        $(this.$el).find('input').val(currentValue)
      }
    },
    onBlur (e) {
      this.stopEdit()
    }
  }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  div {
    display: inline-block;
  }
  
  a {
    cursor: pointer;
    padding-bottom: 1px;
    display: inline-block;
    width: 80px;
  }
  
  input {
    border-width: 0 0 1px 0;
    border-bottom: 1px solid #555;
    padding: 0;
    width: 80px;
    display: none;
    text-align: center;
  }

</style>
