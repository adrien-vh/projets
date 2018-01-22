<script>
    export default {
        methods: {
            $test (val) { return "Retour de la methode" },
            $formatDate (date) { return date.format("MMM YYYY ") }
        }
    }
</script>