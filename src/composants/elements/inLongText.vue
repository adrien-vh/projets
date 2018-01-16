<template>
    <div class="parent">
       
        <div class="fs-13 txt-justify content" contenteditable="true">
            {{ value }}
        </div>
    </div>
</template>

<script>
export default {
  data () {
    return {
        editorInstance: null
    }
  },
  props: {
    value: String,
    placeholder: String
  },
  methods: {},
  mounted () {
    var me = this
    this.editorInstance = CKEDITOR.inline(this.$el.firstChild)
    this.editorInstance.on( 'change', function( evt ) {
        // getData() returns CKEditor's HTML content.
        me.$emit('input', evt.editor.getData())
    });
  }
}
</script>

<style scoped lang="scss">
    @import "../../styles/copic";
    div.parent {
        position: relative;
        border-bottom: 1px solid #dedede;

        div.content {
            outline: none;
            p[style] { margin-bottom: 0 !important; }
        }
    }
</style>
