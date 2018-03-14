<!--
  INPUT AVEC AUTOCOMPLETION
-->
<template>
  <div :class="`autocomplete-wrapper`">
    <input
      ref="input"
      type="text"
      class="autocomplete-input "
      :placeholder="placeholder"
      v-model="type"
      @input="handleInput"
      @dblclick="handleDoubleClick"
      @blur="handleBlur"
      @keydown="handleKeyDown"
      @focus="handleFocus"
      autocomplete="off"
      v-show="editable"
    />
    <i class="fa fa-times txt-red fa-lg" aria-hidden="true" v-show="!valid && editable"></i>
    <i class="fa fa-check txt-green fa-lg" aria-hidden="true" v-show="valid && editable"></i>
    
    <div
      :class="`autocomplete autocomplete-list`"
      v-show="showList && json.length"
    >
      <ul>
        <li
          v-for="(data, i) in json"
          :class="activeClass(i)"
          :key="i"
        >
          <a
            href="#"
            @click.prevent="selectList(data)"
            @mousemove="mousemove(i)"
            v-if="i < 11"
          >
            <div>
              <b class="autocomplete-anchor-text">{{ deepValue(data, labelField) }}</b>
              <!--<span class="autocomplete-anchor-label">{{ deepValue(data, label) }}</span>-->
            </div>
          </a>
        </li>
      </ul>

    </div>
    <span v-show="!editable">{{ getLabel() }}</span>
  </div>
</template>


<script>

  export default {

    props: {
      placeholder: String,
      value: String,
      editable: { type: Boolean, default: true },
      source: { type: Array, required: true },
      labelField: { type: String, required: true },
      valueField: { type: String, required: true },

      // minimum length
      min: { type: Number, default: 0 },

      // Callback
      onInput: Function,
      onShow: Function,
      onBlur: Function,
      onHide: Function,
      onFocus: Function,
      onSelect: Function,
    },

    data() {
      return {
        showList: false,
        type: "",
        json: [],
        focusList: "",
        valid: false
      };
    },

    watch: {
      value (v) {  
        this.type = this.getLabel()
        this.valid = this.type !== ''
      },
      source () {
        this.type = this.getLabel()
        this.valid = this.type !== ''
      }
    },

    methods: {
      // Netralize Autocomplete
      clearInput() {
        this.showList = false
        this.type = ""
        this.json = []
        this.focusList = ""
      },

      // Get the original data
      cleanUp(data){
        return JSON.parse(JSON.stringify(data));
      },

      /*==============================
        INPUT EVENTS
      =============================*/
      handleInput(e){
        const { value } = e.target
        this.showList = true;
        this.valid = false;
        // Callback Event
        if(this.onInput) this.onInput(value)
        return this.getData(value)
      },


      handleKeyDown(e){
        let key = e.keyCode;

        // Disable when list isn't showing up
        if(!this.showList) return;

        // Key List
        const DOWN = 40
        const UP = 38
        const ENTER = 13
        const ESC = 27


        // Prevent Default for Prevent Cursor Move & Form Submit
        switch (key) {
          case DOWN:
            e.preventDefault()
            this.focusList++;
          break;
          case UP:
            e.preventDefault()
            this.focusList--;
          break;
          case ENTER:
            e.preventDefault()
            this.selectList(this.json[this.focusList])
            this.showList = false;
          break;
          case ESC:
            this.showList = false;
          break;
        }

        const listLength = this.json.length - 1;
        const outOfRangeBottom = this.focusList > listLength
        const outOfRangeTop = this.focusList < 0
        const topItemIndex = 0
        const bottomItemIndex = listLength

        let nextFocusList = this.focusList
        if (outOfRangeBottom) nextFocusList = topItemIndex
        if (outOfRangeTop) nextFocusList = bottomItemIndex
        this.focusList = nextFocusList
      },

      setValue(val) {
        this.type = val
      },

      getLabel () {
        var i
        for (i = 0; i < this.source.length; i += 1) {
          if (this.source[i][this.valueField] === this.value) {
            return this.source[i][this.labelField]
          }
        }
        return ''
      },


      /*==============================
        LIST EVENTS
      =============================*/

      handleDoubleClick(){
        this.json = [];
        this.getData("")
        // Callback Event
        this.onShow ? this.onShow() : null
        this.showList = true;
      },

      handleBlur(e){
        // Callback Event
        this.onBlur ? this.onBlur(e) : null
        setTimeout(() => {
          // Callback Event
          this.onHide ? this.onHide() : null
          this.showList = false;
        },250);
      },

      handleFocus(e){
        this.focusList = 0;
        // Callback Event
        this.onFocus ? this.onFocus(e) : null
      },

      mousemove(i){
        this.focusList = i;
      },

      activeClass(i){
        const focusClass = i === this.focusList ? 'focus-list' : ''
        return `${focusClass}`
      },

      selectList(data){
        // Deep clone of the original object
        const clean = this.cleanUp(data);
        // Put the selected data to type (model)
        this.type = clean[this.labelField];
        // Hide List
        this.showList = false;
        this.valid = true
        this.$emit('input', clean[this.valueField])
        // Callback Event
        this.onSelect ? this.onSelect(clean) : null
      },

      deepValue(obj, path) {
        const arrayPath = path.split('.')
        for (var i = 0; i < arrayPath.length; i++) {
          obj = obj[arrayPath[i]];
        }
        return obj;
      },

      getData(val){
        val = val.toLowerCase()
        this.json = this.source.filter(element => element[this.labelField].toLowerCase().indexOf(val) !== -1)
      },
    },
    created(){
      // Sync parent model with initValue Props
      this.type = this.getLabel()
      this.valid = this.type !== ''
    },
    mounted() { }

  }
</script>
<style scoped lang="scss">
  @import "../../styles/copic";

  div {
    position: relative;

    input {
      width: calc(100% - 40px);
      background-color: transparent;
    }

    .autocomplete-list {
      position: absolute;
      top: 20px;
      left: 0;
      padding: 0;
      border: 1px solid #555;
      background-color: #fff;
      width: 100%;
      ul {
        margin: 0;
        padding: 0;
        list-style-type: none;
        width: 100%;

        li {
          padding: 0 5px;
        }

        li.focus-list {
          background-color: $CB14;
          a {
            
            color: #fff;
          }
        }
      }
    }
  }

  
</style>
