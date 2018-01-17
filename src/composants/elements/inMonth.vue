<template>
  <div>
      <a href="#" @click.prevent="monthChoserOpen = true" v-show="editable">{{ currentValueMonth }} {{ currentValueYear }}</a>
      <span v-show="!editable">{{ currentValueMonth }} {{ currentValueYear }}</span>
      <div class="monthChoser" v-show="monthChoserOpen">
          <a href="#" @click.prevent="setMonth(1)">jan.</a>
          <a href="#" @click.prevent="setMonth(2)">fév.</a>
          <a href="#" @click.prevent="setMonth(3)">mars</a>
          <a href="#" @click.prevent="setMonth(4)">avr.</a>
          <a href="#" @click.prevent="setMonth(5)">mai</a>
          <a href="#" @click.prevent="setMonth(6)">juin</a>
          <a href="#" @click.prevent="setMonth(7)">jui.</a>
          <a href="#" @click.prevent="setMonth(8)">aoû.</a>
          <a href="#" @click.prevent="setMonth(9)">sep.</a>
          <a href="#" @click.prevent="setMonth(10)">oct.</a>
          <a href="#" @click.prevent="setMonth(11)">nov.</a>
          <a href="#" @click.prevent="setMonth(12)">déc.</a>
      </div>
      <div class="yearChoser" v-show="yearChoserOpen">
          <a v-for="year in years" :key="year" href="#" @click.prevent="setYear(year)">{{ year }}</a>
      </div>
  </div>
</template>

<script>
export default {
  data () {
    return {
      currentValueMonth: this.value.format("MMM"),
      currentMonthNumber:  this.value.format("M"),
      currentValueYear: this.value.format("YYYY"),
      monthChoserOpen: false,
      yearChoserOpen: false
    }
  },
  watch: {
    value (value) {
      this.currentValueMonth = this.value.format("MMM")
      this.currentMonthNumber = this.value.format("M")
      this.currentValueYear = this.value.format("YYYY")
    }
  },
  props: {
    value: Object, editable: Boolean
  },
  computed: {
        years () {
            var start = parseFloat(moment().format("YYYY")),
            retour = [], i

            for (i = start - 2; i < start + 10; i += 1) {
                retour.push(i)
            }

            return retour
        }
  },
  methods: {
      setMonth (month) {
          this.currentMonthNumber = month
          this.currentValueMonth = moment("01 " + month + " 2010", "DD M YYYY").format("MMM")
          this.monthChoserOpen = false
          this.yearChoserOpen = true
          this.emitValue()
      },
      setYear (year) {
        this.currentValueYear = year
        this.yearChoserOpen = false
        this.emitValue()
      },
      emitValue () {
          this.$emit("input", moment("01 " + this.currentMonthNumber + " " + this.currentValueYear, "DD M YYYY"))
      }
  }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  div {
    display: inline-block;
    position: relative;
    a {
        width: auto;
        outline: none;
    }
    .monthChoser, .yearChoser {
        width: 120px;
        text-align: center;
        position: absolute;
        top: 0;
        left: 0;
        z-index: 200;
        background-color: #fff;
        padding: 5px;
        border: 1px solid #ddd;

      a {
          display: inline-block;
          width: 50px;
          outline: none;
      }
    }
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
