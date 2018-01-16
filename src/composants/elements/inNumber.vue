<template>
  <div>
    <a href="#" @click.prevent="minus" tabindex="-1"><i class="fa fa-minus-square" aria-hidden="true"></i></a>
    <input 
           type="text"
           v-model="currentValue"
           @keypress="isNumber($event)"
           @input="updateValue($event.target.value)"
           :maxlength="maxLength"
           :style="inputStyle()"
    >
    <span>{{ suffix }}</span>
    <a href="#" @click.prevent="plus" tabindex="-1"><i class="fa fa-plus-square" aria-hidden="true"></i></a>
  </div>
</template>

<script>
export default {
    data () { return { currentValue : this.value }},

    watch: { value (value) { this.currentValue = this.value } },

    props: {
        suffix: { default: "", type: String },
        maxLength: { default: 10, type: Number },
        value: Number,
        increment: { default: 1, type: Number }
    },
    methods: {
        isNumber: function(evt) {
            evt = (evt) ? evt : window.event;
            var charCode = (evt.which) ? evt.which : evt.keyCode;
            if ((charCode > 31 && (charCode < 48 || charCode > 57)) && charCode !== 46) {
                evt.preventDefault();;
            } else {
                return true;
            }
        },

        updateValue (value) {this.$emit('input', value) },
        inputStyle () { return { width : (this.maxLength * 10) + 'px' } },
        minus () { 
            this.currentValue = (this.currentValue || 0) - this.increment
            this.$emit('input', this.currentValue)
        },
        plus () { 
            this.currentValue = (this.currentValue || 0) + this.increment
            this.$emit('input', this.currentValue)
        }
    }
}
</script>

<style scoped lang="scss">
  @import "../../styles/copic";
  
  input {
    border-width: 0 0 1px 0;
    border-color: rgb(175, 175, 175);
    color: #212529;
    text-align: center;
  }

  a {
      outline: none;
  }

</style>
