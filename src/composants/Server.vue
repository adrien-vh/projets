<!--
  MIXIN DE COMMUNICATION AVEC LE SERVEUR
-->
<script>
    let rq = 0,
        /** 
        * Traitement des données en réception
        * 
        * @param  {Object}    data            Données à traiter
        * @param  {String}    path            Chemin dans les données des données à traiter
        * @param  {Function}  processFunction Fonction à utiliser pour le traitement des données
        */
        processData = function (data, path, processFunction) {
          var root = path.shift(), i
          
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
        /* Fonctions de transformation des données reçues */
        var toMoment = v => moment(v, "YYYY-MM-DD"),
            toDurationMonth = v => moment.duration(parseFloat(v), "months"),
            toFloat = v => parseFloat(v)
        return {
          runningQueries: 0, // Requêtes en cours
          
          /* Table de transformation des données reçues */
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
        /** 
        * Appel Serveur
        * 
        * @param  {String}    action            Action demandée au serveur
        * @param  {Function}  [callback=null]   Fonction appelée en retour de l'appel serveur, prend les données reçues en paramètre
        * @param  {Object}    [datas={}]        Données envoyées au serveur
        * @param  {Array}     [ttr=[]]]         Table de transformation des données recues
        */
        $$ServerCall (action, callback = null, datas = {}, ttr = []) {
          var me = this
          this.runningQueries += 1  // Une requête en plus

          /* Lancement de la requête */
          $.post(C.SERVER_URL + action, datas, function (data) {
            /* Retour de la requête */
            var i, j
            me.runningQueries -= 1  // Un requête en moins

            /* Affichage des messages retournés par le serveur */
            for (i = 0; i < data[C.MESSAGES].length; i += 1) {
              me.$store.dispatch('displayMessage', data[C.MESSAGES][i])
            }
    
            /* Affichage des erreurs SQL éventuelles */
            for (i = 0; i < data[C.DEBUG].length; i += 1) {
              me.$store.dispatch(
                'displayMessage', 
                {
                  text: "Erreur SQL : " + data[C.DEBUG][i][2],
                  type: "error"
                }
              )
            }

            /* Enregistrement des infos fichiers s'il y a lieu */
            if (typeof data[C.INFOS_FICHIERS] !== 'undefined') {
              for (i = 0; i < data[C.INFOS_FICHIERS].length; i += 1) {
                me.$store.commit('addInfoFichier', data[C.INFOS_FICHIERS][i])
              }
            }

            /* Application de la table de transformations */
            for (i = 0; i < ttr.length; i += 1) {
              for (j = 0; j < ttr[i].transformations.length; j += 1) {
                data[ttr[i].objet] = processData (
                  data[ttr[i].objet],
                  ttr[i].transformations[j].path.split(","),
                  ttr[i].transformations[j].processFunction
                )
              }
            }

            callback(data)
          })
        },

        /** 
        * Requêtes en cours
        * 
        * @return {int} Nombre de requêtes en cours
        */
        $$runningQueries () {
          return rq
        },

        /* Chargement des axes */
        $$loadAxes () {
          var me = this
          this.$$ServerCall (C.AXES, function (data) {
            me.$store.commit('setAxes', data[C.AXES])
            me.$store.commit('setSousAxes', data[C.SOUS_AXES])
          })
        },

        /* Chargement des types d'étape */
        $$loadTypesEtapes () {
          var me = this
          this.$$ServerCall (C.TYPES_ETAPES, function (data) {
            me.$store.commit('setTypesEtapes', data[C.TYPES_ETAPES])
          })
        },

        /* Chargement des directions */
        $$loadDirections () {
          var me = this
          this.$$ServerCall (C.DIRECTIONS, function (data) {
            me.$store.commit('setDirections', data[C.DIRECTIONS])
          })
        },

        /* Connexion automatique de l'utilisateur avec son IP */
        $$connectUserFromIp () {
          var me = this
          this.$$ServerCall (C.IPUSER, function (data) {
            me.$store.commit('setUser', data[C.USER])
            me.$store.commit('setIpUser', data[C.IPUSER])
          })
        },

        /* Déconnexion de l'utilisateur */
        $$logout () {
          var me = this
          this.$$ServerCall (C.LOGOUT, function (data) {
            me.$store.commit('logout')
          })
        },

        /** 
        * Connexion de l'utilisateur avec login et mot de passe
        * 
        * @param  {Object}    loginDatas  Informations de connexions { C.LOGIN: _login_, C.PASSWORD: _motdepasse_ }
        */
        $$connectWithLoginPassword (loginDatas) {
          var me = this
          this.$$ServerCall (C.LOGIN, function (data) {
            me.$store.commit('setUser', data[C.USER])
          }, loginDatas)
        },

        /* Chargement de la liste des utilisateurs */
        $$loadUtilisateurs () {
          var me = this
          this.$$ServerCall (C.UTILISATEURS, function (data) {
            me.$store.commit('setUtilisateurs', data[C.UTILISATEURS])
          })
          
        }
      }
    }
</script>