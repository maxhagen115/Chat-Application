<?php
session_start();
if (isset($_SESSION['unique_id'])) {
  header("location: users.php");
}
?>

<?php include "header.php"; ?>
<?php include "light_dark_switch.php"; ?>
<?php include "language_switch.php"; ?>

<body>
  <div class="wrapper">
    <section class="form signup">
      <header data-i18n="header.title">Chat Applicatie</header>
      <form action="#" method="POST" enctype="multipart/form-data" autocomplete="off">
        <div class="error-text"></div>
        <div class="field image">
          <label data-i18n="signup.selectphoto">Selecteer een profiel foto</label>
          <div class="profile-picture-container">
            <label for="image-upload" class="profile-picture-wrapper">
              <img id="profile-preview" src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 150 150'%3E%3Ccircle cx='75' cy='75' r='75' fill='%23e0e0e0'/%3E%3Ccircle cx='75' cy='60' r='20' fill='%23999'/%3E%3Cpath d='M75 85 C55 85 40 95 40 110 L40 120 L110 120 L110 110 C110 95 95 85 75 85 Z' fill='%23999'/%3E%3C/svg%3E" alt="Profile Preview">
              <div class="profile-picture-overlay">
                <i class="fas fa-camera"></i>
                <span data-i18n="signup.clickphoto">Klik om foto te selecteren</span>
              </div>
            </label>
            <input type="file" id="image-upload" name="image" accept="image/x-png,image/gif,image/jpeg,image/jpg" required>
          </div>
        </div>
        <div class="name-details">
          <div class="field input">
            <label data-i18n="signup.firstname">Voornaam</label>
            <input type="text" name="fname" data-i18n-placeholder="signup.firstname.placeholder" placeholder="Voornaam" required>
          </div>
          <div class="field input">
            <label data-i18n="signup.lastname">Achternaam</label>
            <input type="text" name="lname" data-i18n-placeholder="signup.lastname.placeholder" placeholder="Achternaam" required>
          </div>
        </div>
        <div class="field input">
          <label data-i18n="signup.email">Emailadres</label>
          <input type="text" name="email" data-i18n-placeholder="signup.email.placeholder" placeholder="Voer uw Emailadres in" required>
        </div>
        <div class="field input">
          <label data-i18n="signup.password">Wachtwoord</label>
          <input type="password" name="password" data-i18n-placeholder="signup.password.placeholder" placeholder="Voer een wachtwoord in" required>
          <i class="fas fa-eye"></i>
        </div>
        <div class="field button">
          <input type="submit" name="submit" data-i18n-value="signup.submit" value="Registreer en door naar chat">
        </div>
      </form>
      <div class="field button demo-button">
        <a href="php/demo_login.php" class="demo-link" data-i18n="signup.demo">Ga zonder account door naar chat</a>
      </div>
      <div class="link"><span data-i18n="signup.hasaccount">Al een account?</span> <a href="login.php" data-i18n="signup.login">Login</a></div>
    </section>
  </div>

  <script src="js/translations.js"></script>
  <script src="js/pass-show-hide.js"></script>
  <script src="js/signup.js"></script>

  <script>
    const actualFileInput = document.getElementById('image-upload');
    const profilePreview = document.getElementById('profile-preview');

    actualFileInput.addEventListener('change', function() {
      const file = this.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          profilePreview.src = e.target.result;
          profilePreview.style.opacity = '1';
        };
        reader.readAsDataURL(file);
      }
    });
  </script>

</body>

</html>