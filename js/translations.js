// Translation system
const translations = {
  nl: {
    'header.title': 'Chat Applicatie',
    'login.email': 'Emailadres',
    'login.password': 'Wachtwoord',
    'login.email.placeholder': 'Voer uw Emailadres in',
    'login.password.placeholder': 'Voer uw wachtwoord in',
    'login.submit': 'Inloggen',
    'login.noaccount': 'Nog geen account?',
    'login.register': 'Registreer',
    'signup.selectphoto': 'Selecteer een profiel foto',
    'signup.clickphoto': 'Klik om foto te selecteren',
    'signup.firstname': 'Voornaam',
    'signup.firstname.placeholder': 'Voornaam',
    'signup.lastname': 'Achternaam',
    'signup.lastname.placeholder': 'Achternaam',
    'signup.email': 'Emailadres',
    'signup.email.placeholder': 'Voer uw Emailadres in',
    'signup.password': 'Wachtwoord',
    'signup.password.placeholder': 'Voer een wachtwoord in',
    'signup.submit': 'Registreer en door naar chat',
    'signup.demo': 'Ga zonder account door naar chat',
    'signup.hasaccount': 'Al een account?',
    'signup.login': 'Login',
    'users.selectperson': 'Selecteer een persoon om mee te chatten',
    'users.search.placeholder': 'Zoek een persoon',
    'users.logout': 'Loguit',
    'users.hide': 'Verberg',
    'demo.fname': 'Demo',
    'demo.lname': 'Gebruiker'
  },
  en: {
    'header.title': 'Chat Application',
    'login.email': 'Email Address',
    'login.password': 'Password',
    'login.email.placeholder': 'Enter your email address',
    'login.password.placeholder': 'Enter your password',
    'login.submit': 'Login',
    'login.noaccount': 'No account yet?',
    'login.register': 'Register',
    'signup.selectphoto': 'Select a profile photo',
    'signup.clickphoto': 'Click to select photo',
    'signup.firstname': 'First Name',
    'signup.firstname.placeholder': 'First Name',
    'signup.lastname': 'Last Name',
    'signup.lastname.placeholder': 'Last Name',
    'signup.email': 'Email Address',
    'signup.email.placeholder': 'Enter your email address',
    'signup.password': 'Password',
    'signup.password.placeholder': 'Enter a password',
    'signup.submit': 'Register and go to chat',
    'signup.demo': 'Continue to chat without account',
    'signup.hasaccount': 'Already have an account?',
    'signup.login': 'Login',
    'users.selectperson': 'Select a person to chat with',
    'users.search.placeholder': 'Search for a person',
    'users.logout': 'Logout',
    'users.hide': 'Hide',
    'demo.fname': 'Demo',
    'demo.lname': 'User'
  }
};

// Get current language from localStorage or default to Dutch
let currentLang = localStorage.getItem('language') || 'nl';

// Function to translate text
function translate(key) {
  return translations[currentLang][key] || translations.nl[key] || key;
}

// Make translate function and currentLang available globally
window.translate = translate;
window.currentLang = currentLang;

// Function to update all translations on the page
function updateTranslations() {
  // Update all elements with data-i18n attribute
  document.querySelectorAll('[data-i18n]').forEach(element => {
    const key = element.getAttribute('data-i18n');
    element.textContent = translate(key);
  });

  // Update all elements with data-i18n-placeholder attribute
  document.querySelectorAll('[data-i18n-placeholder]').forEach(element => {
    const key = element.getAttribute('data-i18n-placeholder');
    element.placeholder = translate(key);
  });

  // Update all elements with data-i18n-value attribute (for input values)
  document.querySelectorAll('[data-i18n-value]').forEach(element => {
    const key = element.getAttribute('data-i18n-value');
    element.value = translate(key);
  });

  // Update language switcher display
  const langDisplay = document.getElementById('lang-display');
  const langAlt = document.getElementById('lang-alt');
  if (langDisplay && langAlt) {
    if (currentLang === 'nl') {
      langDisplay.textContent = 'NL';
      langAlt.textContent = 'EN';
    } else {
      langDisplay.textContent = 'EN';
      langAlt.textContent = 'NL';
    }
  }

  // Update HTML lang attribute
  document.documentElement.lang = currentLang;
}

// Language switcher functionality
document.addEventListener('DOMContentLoaded', () => {
  const languageSwitch = document.getElementById('language-switch');
  
  if (languageSwitch) {
    languageSwitch.addEventListener('click', () => {
      // Toggle between Dutch and English
      currentLang = currentLang === 'nl' ? 'en' : 'nl';
      window.currentLang = currentLang;
      localStorage.setItem('language', currentLang);
      updateTranslations();
    });
  }

  // Initial translation update
  updateTranslations();
});

