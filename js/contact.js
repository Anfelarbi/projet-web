/**
 * Aura Deco - Contact Page JavaScript
 * 
 * Contains the functionality specific to the contact page
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize contact form validation
  initContactForm();
  
  // Check URL parameters for form submission status
  checkFormStatus();
});

/**
 * Contact Form Validation and Handling
 */
function initContactForm() {
  const contactForm = document.getElementById('contactForm');
  
  if (!contactForm) return;
  
  contactForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Reset previous validation errors
    clearValidationErrors();
    
    // Get form fields
    const nameField = document.getElementById('name');
    const emailField = document.getElementById('email');
    const messageField = document.getElementById('message');
    
    // Validate fields
    let isValid = true;
    
    if (!nameField.value.trim()) {
      showValidationError(nameField, 'Veuillez entrer votre nom');
      isValid = false;
    }
    
    if (!emailField.value.trim()) {
      showValidationError(emailField, 'Veuillez entrer votre email');
      isValid = false;
    } else if (!isValidEmail(emailField.value.trim())) {
      showValidationError(emailField, 'Veuillez entrer un email valide');
      isValid = false;
    }
    
    if (!messageField.value.trim()) {
      showValidationError(messageField, 'Veuillez entrer votre message');
      isValid = false;
    }
    
    if (isValid) {
      // Form is valid, prepare for submission
      const formData = {
        name: nameField.value.trim(),
        email: emailField.value.trim(),
        message: messageField.value.trim()
      };
      
      // Simulate form submission
      submitForm(formData);
    }
  });
}

/**
 * Submit contact form data
 */
function submitForm(formData) {
  // Show loading state
  const submitBtn = document.querySelector('.btn-submit');
  const originalText = submitBtn.textContent;
  submitBtn.textContent = 'Envoi en cours...';
  submitBtn.disabled = true;
  
  // In a real application, you would send this data to a server
  // Here we'll simulate a successful submission after a delay
  setTimeout(() => {
    console.log('Form data submitted:', formData);
    
    // Reset form
    document.getElementById('contactForm').reset();
    
    // Show success message
    showAlert('success', 'Votre message a été envoyé avec succès. Nous vous répondrons sous 24h.');
    
    // Reset button
    submitBtn.textContent = originalText;
    submitBtn.disabled = false;
  }, 1500);
}

/**
 * Show validation error for a field
 */
function showValidationError(field, message) {
  // Add error class to the field
  field.classList.add('error');
  
  // Create error message element
  const errorMessage = document.createElement('div');
  errorMessage.className = 'error-message';
  errorMessage.textContent = message;
  
  // Insert error message after the field
  field.parentNode.insertBefore(errorMessage, field.nextSibling);
  
  // Add focus event to clear error when user starts typing
  field.addEventListener('focus', function() {
    this.classList.remove('error');
    const errorMsg = this.parentNode.querySelector('.error-message');
    if (errorMsg) {
      errorMsg.remove();
    }
  }, { once: true });
}

/**
 * Clear all validation errors
 */
function clearValidationErrors() {
  // Remove error classes
  document.querySelectorAll('.error').forEach(el => {
    el.classList.remove('error');
  });
  
  // Remove error messages
  document.querySelectorAll('.error-message').forEach(el => {
    el.remove();
  });
}

/**
 * Validate email format
 */
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return emailRegex.test(email);
}

/**
 * Show alert message
 */
function showAlert(type, message) {
  // Get or create alert container
  let alertElement = document.querySelector(`.alert-${type}`);
  
  if (!alertElement) {
    alertElement = document.createElement('div');
    alertElement.className = `alert alert-${type}`;
    
    // Insert at the top of the form
    const contactForm = document.getElementById('contactForm');
    contactForm.parentNode.insertBefore(alertElement, contactForm);
  }
  
  // Set message and show
  alertElement.textContent = message;
  alertElement.classList.add('show');
  
  // Auto-hide after 5 seconds
  setTimeout(() => {
    alertElement.classList.remove('show');
  }, 5000);
}

/**
 * Check URL parameters for form submission status
 */
function checkFormStatus() {
  const urlParams = new URLSearchParams(window.location.search);
  const status = urlParams.get('status');
  
  if (status === 'success') {
    showAlert('success', 'Votre message a été envoyé avec succès. Nous vous répondrons sous 24h.');
  } else if (status === 'error') {
    showAlert('error', 'Une erreur est survenue lors de l\'envoi de votre message. Veuillez réessayer.');
  }
}

/**
 * Initialize Google Map
 */
function initMap() {
  // This function would be called by the Google Maps API
  // In a real application, you would add your Google Maps API key and setup
  
  // Example implementation (commented out as we don't have actual API access):
  /*
  const mapElement = document.getElementById('map');
  
  if (mapElement) {
    const map = new google.maps.Map(mapElement, {
      center: { lat: YOUR_LATITUDE, lng: YOUR_LONGITUDE },
      zoom: 15
    });
    
    const marker = new google.maps.Marker({
      position: { lat: YOUR_LATITUDE, lng: YOUR_LONGITUDE },
      map: map,
      title: 'Aura Decoration'
    });
  }
  */
}