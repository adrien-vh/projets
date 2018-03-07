/* STORE */

import Vue from 'vue'

Vue.use(Vuex)

export default new Vuex.Store ({
  state : {

    // Utilisateur en cours
    user : {
      login : null,
      connecte : false,
      fullName : null,
      droitsProjets: []
    },

    // Utilisateur correspondant à l'IP du poste client
    ipUser : {
      login: null,
      fullName: null,
      droitsProjets: []
    },

    // Contenu de la fenêtre modale
    modale: {
      titre: "",
      contenu: "",
      grande: true,
      confirmation: false,
      onConfirm: function () {}
    },

    // Contenus des listes à choix multiple
    typesEtapes: [],
    axes: [],
    sousAxes: [],
    directions: [],
    utilisateurs: [],
    niveauxDroits: [
      { niveau: 0, label: "lecture seule" },
      { niveau: 1, label: "lecture / écriture" },
      { niveau: 2, label: "lecture / écriture / validation" }
    ],

    // Messages affichés au retour serveur
    messages : [],
    
    // Informations sur les fichiers chargés
    infosFichiers: []
    
  },
  mutations: {
    logout (state) {
      state.user.connecte = false
    },
    setUser (state, user) {
      $.extend(state.user, user)
      state.user.connecte = state.user.connecte
    },
    setIpUser (state, user) {
      $.extend(state.ipUser, user)
    },
    ipLogin (state) {
      if (state.ipUser.login) {
        $.extend(state.user, state.ipUser)
        state.user.connecte = true
      }
    },
    setTypesEtapes (state, typesEtapes) {
      state.typesEtapes = typesEtapes
    },
    setAxes (state, axes) {
      state.axes = axes
    },
    setSousAxes (state, sousAxes) {
      state.sousAxes = sousAxes
    },
    setDirections (state, directions) {
      state.directions = directions
    },
    setUtilisateurs (state, utilisateurs) {
      state.utilisateurs = utilisateurs
    },
    addInfoFichier (state, infoFichier) {
      state.infosFichiers[parseInt(infoFichier.num_fichier)] = infoFichier
    },
    ajouteDroit (state, droit) {
      state.user.droitsProjets.push(droit)
    }
  },
  actions: {
    // Affichage d'un message au retour serveur
    displayMessage (context, message) {
      context.state.messages.push(message)
      setTimeout(
        function () { context.state.messages.shift() },
        3000
      )
    }
  }
})