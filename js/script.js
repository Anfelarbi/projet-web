/**
 * Aura Deco - Main JavaScript File
 * 
 * Contains all the interactive functionality for the website
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize all components
  initNavigation();
  initLanguageSwitcher();
  initScrollEffects();
  initProductTabs();
  initPurchaseForm();
  initBackToTop();
  initTestimonialsSlider();
});

/**
 * Mobile Navigation Toggle
 */
function initNavigation() {
  const toggleBtn = document.getElementById('toggleMenu');
  const menubar = document.getElementById('menubar');
  
  if (toggleBtn && menubar) {
    toggleBtn.addEventListener('click', function() {
      menubar.classList.toggle('active');
    });
    
    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
      if (!menubar.contains(event.target) && !toggleBtn.contains(event.target)) {
        menubar.classList.remove('active');
      }
    });
  }
  
  // Add scroll effect to header
  const header = document.querySelector('header');
  if (header) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 50) {
        header.classList.add('scrolled');
      } else {
        header.classList.remove('scrolled');
      }
    });
  }
}

/**
 * Language Switcher

function initLanguageSwitcher() {
  const langBtns = document.querySelectorAll('.lang-btn');
  
  if (langBtns.length) {
    langBtns.forEach(btn => {
      btn.addEventListener('click', function() {
        const lang = this.getAttribute('onclick').match(/switchLang\('([^']+)'\)/)[1];
        
        // Remove active class from all buttons
        langBtns.forEach(b => b.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Call the switchLang function
        switchLang(lang);
      });
    });
  }
}

/**
 * Switch language function

function switchLang(lang) {
  // Handle text elements with lang attribute
  document.querySelectorAll('[lang="fr"]').forEach(el => {
    el.style.display = lang === 'fr' ? 'block' : 'none';
  });
  
  document.querySelectorAll('[lang="ar"]').forEach(el => {
    el.style.display = lang === 'ar' ? 'block' : 'none';
  });
  
  // Handle RTL for Arabic
  document.body.style.direction = lang === 'ar' ? 'rtl' : 'ltr';
  
  // Save language preference
  localStorage.setItem('preferredLanguage', lang);
  
  // Add a class to the body for additional styling if needed
  document.body.classList.remove('lang-fr', 'lang-ar');
  document.body.classList.add(`lang-${lang}`);
} */

/**
 * Apply scroll animations
 */
function initScrollEffects() {
  // Detect elements in viewport and add animation classes
  const elements = document.querySelectorAll('.fade-in, .slide-in-left, .slide-in-right');
  
  const isInViewport = function(element) {
    const rect = element.getBoundingClientRect();
    return (
      rect.top <= (window.innerHeight || document.documentElement.clientHeight) &&
      rect.bottom >= 0
    );
  };
  
  const animateElements = function() {
    elements.forEach(element => {
      if (isInViewport(element) && !element.classList.contains('animated')) {
        element.classList.add('animated');
      }
    });
  };
  
  // Run once on load
  animateElements();
  
  // Listen for scroll events
  window.addEventListener('scroll', animateElements);
}

/**
 * Product Page Tabs
 */
function initProductTabs() {
  const tabs = document.querySelectorAll('.tab');
  
  if (tabs.length) {
    tabs.forEach(tab => {
      tab.addEventListener('click', function() {
        const target = this.getAttribute('data-target');
        
        // Remove active class from all tabs
        tabs.forEach(t => t.classList.remove('active'));
        
        // Add active class to clicked tab
        this.classList.add('active');
        
        // Hide all sections
        document.querySelectorAll('.product-section').forEach(section => {
          section.classList.remove('active');
        });
        
        // Show selected section
        const targetSection = document.getElementById(target);
        if (targetSection) {
          targetSection.classList.add('active');
        }
      });
    });
  }
}

/**
 * Purchase Form Handling
 */
function initPurchaseForm() {
  const buyButtons = document.querySelectorAll('.btn-buy');
  const purchaseForm = document.getElementById('purchaseForm');
  const closeBtn = document.querySelector('.btn-close');
  
  if (buyButtons.length && purchaseForm) {
    buyButtons.forEach(btn => {
      btn.addEventListener('click', function() {
        purchaseForm.classList.add('active');
        
        // Get product details from parent card
        const card = this.closest('.product-card');
        const productName = card.querySelector('h3').textContent;
        const productPrice = card.querySelector('.price').textContent;
        
        // Store product info for form submission
        purchaseForm.setAttribute('data-product', productName);
        purchaseForm.setAttribute('data-price', productPrice);
      });
    });
    
    if (closeBtn) {
      closeBtn.addEventListener('click', function() {
        purchaseForm.classList.remove('active');
      });
    }
    
    // Handle form submission
    const orderForm = document.getElementById('orderForm');
    if (orderForm) {
      orderForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Here you would typically collect form data and send to server
        const formData = {
          name: document.getElementById('name').value,
          address: document.getElementById('address').value,
          phone: document.getElementById('phone').value,
          payment: document.getElementById('payment').value,
          product: purchaseForm.getAttribute('data-product'),
          price: purchaseForm.getAttribute('data-price')
        };
        
        console.log('Order submitted:', formData);
        
        // Show success message and close form
        alert('Commande passée avec succès! Nous vous contacterons bientôt.');
        purchaseForm.classList.remove('active');
        orderForm.reset();
      });
    }
  }
}

/**
 * Back to Top Button
 */
function initBackToTop() {
  const backToTopBtn = document.getElementById('go_to_top');
  
  if (backToTopBtn) {
    window.addEventListener('scroll', function() {
      if (window.scrollY > 300) {
        backToTopBtn.style.opacity = '1';
      } else {
        backToTopBtn.style.opacity = '0';
      }
    });
  }
}

/**
 * Testimonials Slider
 */
function initTestimonialsSlider() {
  const testimonialsSlider = document.querySelector('.testimonials-slider');
  
  if (!testimonialsSlider) return;
  
  // If you want to add automatic sliding, uncomment this code:
  /*
  const testimonials = testimonialsSlider.querySelectorAll('.testimonial-card');
  let currentIndex = 0;
  
  function showTestimonial(index) {
    testimonials.forEach((testimonial, i) => {
      testimonial.style.opacity = i === index ? '1' : '0';
      testimonial.style.transform = i === index ? 'translateX(0)' : 'translateX(-50px)';
    });
  }
  
  function nextTestimonial() {
    currentIndex = (currentIndex + 1) % testimonials.length;
    showTestimonial(currentIndex);
  }
  
  // Initial display
  showTestimonial(currentIndex);
  
  // Auto-rotate every 5 seconds
  setInterval(nextTestimonial, 5000);
  */
}

/**
 * Add items to cart
 */
function addToCart(productId, productName, productPrice, productImage) {
  // Check if cart exists in localStorage
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  // Check if product already exists in cart
  const existingProduct = cart.find(item => item.id === productId);
  
  if (existingProduct) {
    existingProduct.quantity += 1;
  } else {
    cart.push({
      id: productId,
      name: productName,
      price: productPrice,
      image: productImage,
      quantity: 1
    });
  }
  
  // Save cart back to localStorage
  localStorage.setItem('cart', JSON.stringify(cart));
  
  // Update cart count display
  updateCartCount();
  
  // Show notification
  showNotification('Produit ajouté au panier');
}

/**
 * Update cart count display
 */
function updateCartCount() {
  const cartCountElement = document.querySelector('.cart-count');
  if (!cartCountElement) return;
  
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  const itemCount = cart.reduce((total, item) => total + item.quantity, 0);
  
  cartCountElement.textContent = itemCount;
}

/**
 * Show notification popup
 */
function showNotification(message) {
  // Create notification element if it doesn't exist
  let notification = document.querySelector('.notification');
  
  if (!notification) {
    notification = document.createElement('div');
    notification.className = 'notification';
    document.body.appendChild(notification);
  }
  
  // Set message and show
  notification.textContent = message;
  notification.classList.add('show');
  
  // Hide after 3 seconds
  setTimeout(() => {
    notification.classList.remove('show');
  }, 3000);
}

/**
 * Check for saved language preference on page load
 */
document.addEventListener('DOMContentLoaded', function() {
  const savedLang = localStorage.getItem('preferredLanguage');
  if (savedLang) {
    switchLang(savedLang);
    
    // Update active button in language switcher
    const langBtn = document.querySelector(`.lang-btn[onclick*="switchLang('${savedLang}')"]`);
    if (langBtn) {
      document.querySelectorAll('.lang-btn').forEach(btn => btn.classList.remove('active'));
      langBtn.classList.add('active');
    }
  }
  
  // Initialize cart count on page load
  updateCartCount();
});