<template>
  <div id="pageAccueil">
    <div id="contBouton">
      <button class="btn btn-lg btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Nouveau projet</button>
    </div>
    <h2>Synthèses</h2>
    <div id="listeSyntheses">
      <router-link to="login"><i class="fa fa-file-o" aria-hidden="true"></i> Récapitulatif financier</router-link>
      <router-link to="login"><i class="fa fa-file-o" aria-hidden="true"></i> Planning global</router-link>
    </div>
    <h2>Projets en cours</h2>
    <table class="table table-hover table-sm table-striped">
      <thead class="thead-dark">
        <tr>
          <th>Titre</th>
          <th>Chef de projet</th>
          <th>Date de fin</th>
          <th>Budget prévisionnel</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><b>Plan lumière</b></td>
          <td>François Houot</td>
          <td>juillet 2018</td>
          <td>50 000 €</td>
          <td><img src="../../assets/images/sun.png" alt=""></td>
        </tr>
        <tr>
          <td><b>Valorisation et préservation des espaces naturels remarquables</b></td>
          <td>Christophe Armand</td>
          <td>mars 2017</td>
          <td>100 000 €</td>
          <td><img src="../../assets/images/cloud.png" alt=""></td>
        </tr>
      </tbody>
    </table>
    <h2>Projets archivés</h2>
    <table class="table table-hover table-sm table-striped">
      <thead class="thead-dark">
        <tr>
          <th>Titre</th>
          <th>Chef de projet</th>
          <th>Date de fin</th>
          <th>Budget prévisionnel</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td><b>Plan lumière</b></td>
          <td>François Houot</td>
          <td>juillet 2018</td>
          <td>50 000 €</td>
          <td><img src="../../assets/images/rain.png" alt=""></td>
        </tr>
        <tr>
          <td><b>Valorisation et préservation des espaces naturels remarquables</b></td>
          <td>Christophe Armand</td>
          <td>mars 2017</td>
          <td>100 000 €</td>
          <td><img src="../../assets/images/storm.png" alt=""></td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {  }
</script>

<style scoped lang="scss">
  @import "../../styles/copic";

  #pageAccueil {
    padding: 0 50px;
    #listeSyntheses {
      padding-bottom: 20px;
      a {
        display: inline-block;
        margin-right: 15px;
      }
    }
    #contBouton {
      text-align: center;
      padding-bottom: 20px;
      
      button {
        cursor: pointer;
      }
    }
    table {
      font-size: 14px;
      td {
        vertical-align: middle;
      }
    }
  }
  tr {
    cursor: pointer;
  }
</style>
