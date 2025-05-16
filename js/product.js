/**
 * Aura Deco - Product Page JavaScript
 * 
 * Contains the functionality specific to the product page
 */

document.addEventListener('DOMContentLoaded', function() {
  // Initialize product tab functionality
  initProductTabs();
  
  // Initialize product filtering
  initProductFilter();
  
  // Initialize purchase form
  initPurchaseForm();
  
  // Add to cart functionality
  initAddToCart();
});

/**
 * Product Tab Functionality
 */
function initProductTabs() {
  const tabs = document.querySelectorAll('.filter-btn');
  const sections = document.querySelectorAll('.product-section');
  
  if (tabs.length === 0 || sections.length === 0) return;
  
  // Function to show a specific section
  function showSection(sectionId) {
    // Hide all sections
    sections.forEach(section => {
      section.classList.remove('active');
    });
    
    // Show the selected section
    const targetSection = document.getElementById(sectionId);
    if (targetSection) {
      targetSection.classList.add('active');
    }
    
    // Update active tab
    tabs.forEach(tab => {
      if (tab.getAttribute('data-target') === sectionId) {
        tab.classList.add('active');
      } else {
        tab.classList.remove('active');
      }
    });
  }
  
  // Add click event to tabs
  tabs.forEach(tab => {
    tab.addEventListener('click', function() {
      const target = this.getAttribute('data-target');
      showSection(target);
      
      // Update URL hash
      window.location.hash = target;
    });
  });
  
  // Check URL hash on page load
  if (window.location.hash) {
    const targetId = window.location.hash.substring(1);
    showSection(targetId);
  } else {
    // Show first tab by default
    const firstTab = tabs[0];
    if (firstTab) {
      const firstTarget = firstTab.getAttribute('data-target');
      showSection(firstTarget);
    }
  }
}

/**
 * Product Filtering
 */
function initProductFilter() {
  const filterButtons = document.querySelectorAll('.filter-btn');
  const productCards = document.querySelectorAll('.product-card');
  
  if (filterButtons.length === 0 || productCards.length === 0) return;
  
  filterButtons.forEach(button => {
    button.addEventListener('click', function() {
      // Get the category from data attribute
      const category = this.getAttribute('data-category');
      
      // Update active button
      filterButtons.forEach(btn => btn.classList.remove('active'));
      this.classList.add('active');
      
      // Filter products
      if (category === 'all') {
        // Show all products
        productCards.forEach(card => {
          card.style.display = 'block';
          setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
          }, 10);
        });
      } else {
        // Show only products matching the category
        productCards.forEach(card => {
          if (card.getAttribute('data-category') === category) {
            card.style.display = 'block';
            setTimeout(() => {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, 10);
          } else {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
              card.style.display = 'none';
            }, 300);
          }
        });
      }
    });
  });
}

/**
 * Purchase Form Handling
 */
function initPurchaseForm() {
  const buyButtons = document.querySelectorAll('.btn-buy');
  const purchaseForm = document.getElementById('purchaseForm');
  const closeBtn = document.querySelector('.btn-close');
  const orderForm = document.getElementById('orderForm');
  
  if (!purchaseForm) return;
  
  // Show purchase form when Buy button is clicked
  buyButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Get product information from the card
      const productCard = this.closest('.product-card');
      const productName = productCard.querySelector('h3').textContent;
      const productPrice = productCard.querySelector('.price').textContent;
      const productImage = productCard.querySelector('img').src;
      
      // Store product data in form
      purchaseForm.setAttribute('data-product', productName);
      purchaseForm.setAttribute('data-price', productPrice);
      purchaseForm.setAttribute('data-image', productImage);
      
      // Show form
      purchaseForm.classList.add('active');
    });
  });
  
  // Close form when close button is clicked
  if (closeBtn) {
    closeBtn.addEventListener('click', function() {
      purchaseForm.classList.remove('active');
    });
  }
  
  // Close form when clicking outside
  purchaseForm.addEventListener('click', function(e) {
    if (e.target === purchaseForm) {
      purchaseForm.classList.remove('active');
    }
  });
  
  // Handle form submission
  if (orderForm) {
    orderForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Collect form data
      const formData = {
        name: document.getElementById('name').value,
        address: document.getElementById('address').value,
        phone: document.getElementById('phone').value,
        payment: document.getElementById('payment').value,
        product: purchaseForm.getAttribute('data-product'),
        price: purchaseForm.getAttribute('data-price')
      };
      
      // Here you would typically send this data to a server
      console.log('Order submitted:', formData);
      
      // Show success message
      alert('Votre commande a été effectuée avec succès! Nous vous contacterons bientôt.');
      
      // Reset form and close
      orderForm.reset();
      purchaseForm.classList.remove('active');
    });
  }
}

/**
 * Add to Cart Functionality
 */
function initAddToCart() {
  const addToCartButtons = document.querySelectorAll('.btn-buy');
  
  if (addToCartButtons.length === 0) return;
  
  addToCartButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      // Prevent the default action (opening purchase form)
      e.preventDefault();
      
      // Get product information
      const productCard = this.closest('.product-card');
      const productId = productCard.getAttribute('data-id') || generateProductId();
      const productName = productCard.querySelector('h3').textContent;
      const productPrice = productCard.querySelector('.price').textContent.replace(/[^0-9.]/g, '');
      const productImage = productCard.querySelector('img').src;
      
      // Add to cart
      addToCart(productId, productName, productPrice, productImage);
    });
  });
}

/**
 * Helper function to generate a unique product ID if none exists
 */
function generateProductId() {
  return 'product_' + Math.random().toString(36).substring(2, 15);
}

/**
 * Add product to cart
 */
function addToCart(productId, productName, productPrice, productImage) {
  // Get current cart from localStorage
  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  // Check if product already exists in cart
  const existingProductIndex = cart.findIndex(item => item.id === productId);
  
  if (existingProductIndex !== -1) {
    // Increment quantity if product already in cart
    cart[existingProductIndex].quantity += 1;
  } else {
    // Add new item to cart
    cart.push({
      id: productId,
      name: productName,
      price: productPrice,
      image: productImage,
      quantity: 1
    });
  }
  
  // Save updated cart to localStorage
  localStorage.setItem('cart', JSON.stringify(cart));
  
  // Update cart count
  updateCartCount();
  
  // Show success notification
  showNotification('Produit ajouté au panier');
}

/**
 * Update cart count in the navigation
 */
function updateCartCount() {
  const cartCountElement = document.querySelector('.cart-count');
  if (!cartCountElement) return;
  
  // Get cart from localStorage
  const cart = JSON.parse(localStorage.getItem('cart')) || [];
  
  // Calculate total quantity
  const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
  
  // Update display
  cartCountElement.textContent = totalItems;
  
  // Add animation effect
  cartCountElement.classList.add('update-animation');
  setTimeout(() => {
    cartCountElement.classList.remove('update-animation');
  }, 300);
}

/**
 * Show notification message
 */
function showNotification(message) {
  // Create notification element if it doesn't exist
  let notification = document.querySelector('.notification');
  
  if (!notification) {
    notification = document.createElement('div');
    notification.className = 'notification';
    document.body.appendChild(notification);
    
    // Add styles if not already in CSS
    notification.style.position = 'fixed';
    notification.style.bottom = '20px';
    notification.style.right = '20px';
    notification.style.backgroundColor = 'var(--color-success)';
    notification.style.color = 'white';
    notification.style.padding = '10px 20px';
    notification.style.borderRadius = '4px';
    notification.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
    notification.style.transform = 'translateY(100px)';
    notification.style.opacity = '0';
    notification.style.transition = 'all 0.3s ease';
    notification.style.zIndex = '1000';
  }
  
  // Set message
  notification.textContent = message;
  
  // Show notification
  setTimeout(() => {
    notification.style.transform = 'translateY(0)';
    notification.style.opacity = '1';
  }, 100);
  
  // Hide after delay
  setTimeout(() => {
    notification.style.transform = 'translateY(100px)';
    notification.style.opacity = '0';
  }, 3000);
}