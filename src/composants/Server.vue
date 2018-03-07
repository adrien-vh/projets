<script>
    let rq = 0,
        processData = function (data, path, processFunction) {
          var root = path.shift(),
              i
          
          if (root.slice(-1) === "%") {
            root = root.slice(0, -1)
            if (path.length === 0) {
              data[root] = processFunction(data[root])
              return data
            } else {
              for (i = 0; i < data[root].length; i += 1) {
                data[root][i] = processData(data[root][i], path.slice(), processFunction)
              }
              return data
            }
          } else {
            if (path.length === 0) {
              data[root] = processFunction(data[root])
              return data
            } else {
              data[root] = processData(data, path.slice(), processFunction)
              return data
            }
          }
        }

    export default {
      data () {
        var toMoment = v => moment(v, "YYYY-MM-DD"),
            toDurationMonth = v => moment.duration(parseFloat(v), "months"),
            toFloat = v => parseFloat(v)
        return {
          runningQueries: 0,
          transformTables: {
            projet: {
              objet : C.PROJET,
              transformations : [
                { path: C.ETAPES + "%,debut", processFunction : toMoment},
                { path: C.ETAPES + "%,debutInitial", processFunction : toMoment},
                { path: C.ETAPES + "%,duree", processFunction : toDurationMonth},
                { path: C.ETAPES + "%,dureeInitial", processFunction : toDurationMonth},
                { path: C.ETAPES + "%,transactions%,date", processFunction : toMoment},
                { path: C.ETAPES + "%,transactions%,montant", processFunction : toFloat},
                { path: C.FINANCEMENT + "%,date", processFunction : toMoment},
                { path: C.FINANCEMENT + "%,montant", processFunction : toFloat}
              ]
            }
          }
        }
      },
      methods: {
        $$ServerCall (action, callback = null, datas = {}, ttr = [], tts = []) {
          var me = this
          this.runningQueries += 1
          $.post(C.SERVER_URL + action, datas, function (data) {
            var i, j
            me.runningQueries -= 1
            for (i = 0; i < data[C.MESSAGES].length; i += 1) {
              me.$store.dispatch('displayMessage', data[C.MESSAGES][i])
            }
    
            for (i = 0; i < data[C.DEBUG].length; i += 1) {
              me.$store.dispatch(
                'displayMessage', 
                {
                  text: "Erreur SQL : " + data[C.DEBUG][i][2],
                  type: "error"
                }
              )
            }

            if (typeof data[C.INFOS_FICHIERS] !== 'undefined') {
              for (i = 0; i < data[C.INFOS_FICHIERS].length; i += 1) {
                me.$store.commit('addInfoFichier', data[C.INFOS_FICHIERS][i])
              }
            }

            for (i = 0; i < ttr.length; i += 1) {
              for (j = 0; j < ttr[i].transformations.length; j += 1) {
              //for (j = 0; j < 1; j += 1) {
                data[ttr[i].objet] = processData (
                  data[ttr[i].objet],
                  ttr[i].transformations[j].path.split(","),
                  ttr[i].transformations[j].processFunction
                )
              }
            }

            if (action === C.CHARGE_PROJET) {
            }

            callback(data)
          })
        },
        $$runningQueries () {
          return rq
        },
        $$loadAxes () {
          var me = this
          this.$$ServerCall (C.AXES, function (data) {
            me.$store.commit('setAxes', data[C.AXES])
            me.$store.commit('setSousAxes', data[C.SOUS_AXES])
          })
        },
        $$loadTypesEtapes () {
          var me = this
          this.$$ServerCall (C.TYPES_ETAPES, function (data) {
            me.$store.commit('setTypesEtapes', data[C.TYPES_ETAPES])
          })
        },
        $$loadDirections () {
          var me = this
          this.$$ServerCall (C.DIRECTIONS, function (data) {
            me.$store.commit('setDirections', data[C.DIRECTIONS])
          })
        },
        $$connectUserFromIp () {
          var me = this
          this.$$ServerCall (C.IPUSER, function (data) {
            me.$store.commit('setUser', data[C.USER])
            me.$store.commit('setIpUser', data[C.IPUSER])
          })
        },
        $$logout () {
          var me = this
          this.$$ServerCall (C.LOGOUT, function (data) {
            me.$store.commit('logout')
          })
        },
        $$connectWithLoginPassword (loginDatas) {
          var me = this
          this.$$ServerCall (C.LOGIN, function (data) {
            me.$store.commit('setUser', data[C.USER])
            /*if (data[C.USER].login) {
              me.$store.dispatch('displayMessage', { type: 'success', text: 'Connexion réussie !' })
            } else {
              me.$store.dispatch('displayMessage', { type: 'error', text: "Erreur d'authentification" })
            }*/
          }, loginDatas)
        },
        $$loadUtilisateurs () {
          var me = this
          this.$$ServerCall (C.UTILISATEURS, function (data) {
            me.$store.commit('setUtilisateurs', data[C.UTILISATEURS])
          })
          
        }
      }
    }
</script>