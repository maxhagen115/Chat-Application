<?php include "header.php"; ?>
  <body>
    <div class="wrapper">
      <section class="form login">
        <header>Chat Applicatie</header>
        <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
          <div class="error-text"></div>
          <div class="field input">
            <label>Emailadres</label>
            <input type="text" name="email" placeholder="Voer uw Emailadres in" required>
          </div>
          <div class="field input">
            <label>Wachtwoord</label>
            <input type="password" name="password" placeholder="Voer uw wachtwoord in" required>
            <i class="fas fa-eye"></i>
          </div>
          <div class="field button">
            <input type="submit" name="submit" value="Ga door naar Chat">
          </div>
        </form>
        <div class="link">Nog geen account? <a href="index.php">Registreer</a></div>
      </section>
    </div>
  
    <script src="js/pass-show-hide.js"></script>
    <script src="js/login.js"></script>
  
  </body>
</html>
