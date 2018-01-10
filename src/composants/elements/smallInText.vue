<template>
  <div>
    <input 
           type="text"
           :value="value"
           :placeholder="placeholder"
           @focusout="stopEdit"
           @keyup="onKeyUp"
           @input="updateValue($event.target.value)"
    >
    <a href="#" @click.prevent="startEdit">{{ currentValue === '' ? placeholder : currentValue }}</a>
  </div>
</template>

<script>
export default {
  data () {
    return {
      editing: false,
      currentValue: this.value
    }
  },
  props: {
    value: String,
    placeholder: String
  },
  methods: {
    startEdit () {
      this.editing = true
      $(this.$el).find('a').hide()
      $(this.$el).find('input').show()
      $(this.$el).find('input').select()
      $(this.$el).find('input').focus()
    },
    stopEdit () {
      this.editing = false
      $(this.$el).find('input').hide()
      $(this.$el).find('a').show()
    },
    onKeyUp (e) {
      if (e.keyCode === 13) {
        this.stopEdit()
      }
    },
    updateValue (value) {
      this.currentValue = value
      this.$emit('input', value)
      
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
  }
  
  input {
    border-width: 0 0 1px 0;
    border-bottom: 1px solid #555;
    padding: 0;
    width: 450px;
    display: none;
  }

</style>
