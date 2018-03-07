<script>
    export default {
        methods: {
            $formatDate (date) { return date.format("MMM YYYY ") },
            $stepEndDate (step) {
                return moment(step.debut).add(step.duree).subtract(1, "d")
            },
            $showModal (title, content, grande = false, confirmation = false, onConfirm = function () {}) {
                this.$store.state.modale.titre = title
                this.$store.state.modale.contenu = content
                this.$store.state.modale.grande = grande
                this.$store.state.modale.confirmation = confirmation
                this.$store.state.modale.onConfirm = onConfirm
                $('#modaleGlobale').modal()
            },
            $userName (login) {
                var i
                for (i = 0; i < this.$store.state.utilisateurs.length; i += 1) {
                    if (this.$store.state.utilisateurs[i].login === login) {
                        return this.$store.state.utilisateurs[i].nom
                    }
                }
                return login
            },
            $niveauAcces (num_projet) {
                var i, num_projet = parseInt(num_projet)
                
                if (this.$store.state.user.connecte) {
                    for (i = 0; i < this.$store.state.user.droitsProjets.length; i += 1) {
                        if (parseInt(this.$store.state.user.droitsProjets[i].num_projet) == num_projet) {
                            return parseInt(this.$store.state.user.droitsProjets[i].niveau)
                        }
                    }
                }
                return -1
            },
            $stringify (element) {
                var i, prop

                if (moment.isMoment(element)) {
                    return element.format("YYYY-MM-DD")
                }
                
                if (moment.isDuration(element)) {
                    return element.asMonths()
                }

                if (typeof element === "string" || typeof element === "number" || typeof element === "boolean") {
                    return element
                }

                if (Array.isArray(element)) {
                    for (i = 0; i < element.length; i += 1) {
                        element[i] = this.$stringify(element[i])
                    }
                    return element
                }

                for(prop in element) {
                    if (element.hasOwnProperty(prop)) {
                        element[prop] = this.$stringify(element[prop])
                    }
                }
                return element

            }
        }
    }
</script>