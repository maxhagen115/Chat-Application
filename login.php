<?php include "header.php"; ?>
<?php include "light_dark_switch.php"; ?>
<?php include "language_switch.php"; ?>
  <body>
    <div class="wrapper">
      <section class="form login">
        <header data-i18n="header.title">Chat Applicatie</header>
        <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
          <div class="error-text"></div>
          <div class="field input">
            <label data-i18n="login.email">Emailadres</label>
            <input type="text" name="email" data-i18n-placeholder="login.email.placeholder" placeholder="Voer uw Emailadres in" required>
          </div>
          <div class="field input">
            <label data-i18n="login.password">Wachtwoord</label>
            <input type="password" name="password" data-i18n-placeholder="login.password.placeholder" placeholder="Voer uw wachtwoord in" required>
            <i class="fas fa-eye"></i>
          </div>
          <div class="field button">
            <input type="submit" name="submit" data-i18n-value="login.submit" value="Inloggen">
          </div>
        </form>
        <div class="link"><span data-i18n="login.noaccount">Nog geen account?</span> <a href="index.php" data-i18n="login.register">Registreer</a></div>
      </section>
    </div>
  
    <script src="js/translations.js"></script>
    <script src="js/pass-show-hide.js"></script>
    <script src="js/login.js"></script>
  
  </body>
</html>
