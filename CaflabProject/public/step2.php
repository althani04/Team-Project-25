<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Coffee Subscription - Select Product</title>
    <link rel="stylesheet" href="css/subscription.css" />
  </head>
  <body>

  <?php 
  include 'session_check.php';
  include 'navbar.php'; ?>


    <!-- Step Navigation -->
    <div class="step-navigation">
      <div class="step active">1. <span>Select Product</span></div>
      <div class="step">2. <span>Set Quantity</span></div>
      <div class="step">3. <span>Set Frequency</span></div>
      <div class="step">4. <span>Choose Your Plan</span></div>
    </div>

    <!-- Main Content -->
    <main>
      <section class="filter-section">
        <h2>Coffee Category</h2>
        <div class="filters">
          <label for="coffee-type">Coffee Type:</label>
          <select id="coffee-type">
            <option value="all">All</option>
            <option value="whole-bean">Whole Bean</option>
            <option value="ground">Ground</option>
          </select>

          <label for="sort">Sort By:</label>
          <select id="sort">
            <option value="all">All</option>
            <option value="price-low">Price: Low to High</option>
            <option value="price-high">Price: High to Low</option>
          </select>
        </div>
      </section>

      <section class="product-list">
        <div class="section-header">
          <h2>All Coffees</h2>
        </div>
        <div class="product-grid">

        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer>
      <p>© 2024 CAF LAB Coffee Company. All Rights Reserved. Ecommerce software by Team Expert 25</p>
    </footer>

    <div class="modal" id="confirmation-modal">
      <div class="modal-content">
        <span class="close" id="close-confirmation-modal">&times;</span>
        <h3>Confirm Your Selection</h3>
        <p id="confirmation-message"></p>
        <button id="confirm-button" class="modal-button">Confirm</button>
        <button id="cancel-button" class="modal-button">Cancel</button>
      </div>
    </div>

    <div class="modal" id="admin-edit-modal">
      <div class="modal-content admin-edit-modal">
        <span class="close" id="close-admin-modal">&times;</span>
        <h3>Edit Coffee Plan</h3>
        <form id="admin-edit-form">
          <div class="form-grid">
            <label for="edit-name">Name:</label>
            <input type="text" id="edit-name" required>
            
            <label for="edit-type">Type:</label>
            <select id="edit-type" required>
              <option value="whole-bean">Whole Bean</option>
              <option value="ground">Ground</option>
            </select>
            
            <label for="edit-features">Features:</label>
            <textarea id="edit-features" rows="3" required></textarea>
            
            <label for="edit-price">Price (£):</label>
            <input type="number" id="edit-price" step="0.01" min="0" required>
            
            <label for="edit-image">Upload Image:</label>
            <div class="custom-file-upload">
              <input type="file" id="edit-image" accept="image/*">
              <label for="edit-image" class="upload-button">Choose File</label>
              <span class="file-name">No file chosen</span>
            </div>
          </div>
          <div class="form-buttons">
            <button type="button" id="save-changes">Save Changes</button>
            <button type="button" id="delete-plan" class="danger">DELETE</button>
            <button type="button" id="cancel-edit">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal" id="add-plan-modal">
      <div class="modal-content admin-edit-modal">
        <span class="close" id="close-add-modal">&times;</span>
        <h3>Create New Plan</h3>
        <form id="add-plan-form">
          <div class="form-grid">
            <label for="new-name">Name:</label>
            <input type="text" id="new-name" required>
            
            <label for="new-type">Type:</label>
            <select id="new-type" required>
              <option value="whole-bean">Whole Bean</option>
              <option value="ground">Ground</option>
            </select>
            
            <label for="new-features">Features:</label>
            <textarea id="new-features" rows="3" required></textarea>
            
            <label for="new-price">Price (£):</label>
            <input type="number" id="new-price" step="0.01" min="0" required>
            
            <label for="new-image">Upload Image:</label>
            <div class="custom-file-upload">
              <input type="file" id="new-image" accept="image/*" required>
              <label for="new-image" class="upload-button">Choose File</label>
              <span class="file-name">No file chosen</span>
            </div>
          </div>
          <div class="form-buttons">
            <button type="button" id="confirm-add">Create Plan</button>
            <button type="button" id="cancel-add">Cancel</button>
          </div>
        </form>
      </div>
    </div>

    <script>
      let selectedProductName = "";
      let selectedProductPrice = 0;
      let selectedProductId = -1;
      let isAdmin = false;

      // Coffee product data
      let coffeeProducts = [];

      // Gets the user ID from the PHP session
      const userId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;

      // Filter and sort functions
      function filterAndSortProducts() {
        const selectedType = document.getElementById("coffee-type").value;
        const sortBy = document.getElementById("sort").value;

        // Screening product
        let filteredProducts = coffeeProducts.filter((product) => {
          return (
            (selectedType === "all" || product.type === selectedType)
          );
        });

        // Sort product
        filteredProducts.sort((a, b) => {
          switch (sortBy) {
            case "price-low":
              return a.price - b.price;
            case "price-high":
              return b.price - a.price;
            default:
              return 0; // Keep the original sequence
          }
        });

        // Update display
        updateProductDisplay(filteredProducts);
      }

      // Update product display
      function updateProductDisplay(products) {
        const productGrid = document.querySelector(".product-grid");
        productGrid.innerHTML = "";

        products.forEach((product) => {
          const productCard = `
            <div class="product-card">
              <div class="product-image">
${product.image ? `<img src="${product.image}" alt="${product.name} product image">` : 'Coffee Image'}
              </div>
              <h3 data-id="${product.id}">${product.name}</h3>
              <p>${product.features}</p>
              <p class="price">£${product.price.toFixed(2)}</p>
              <div class="button-container">
                <button class="add-to-basket" onclick="confirmAndSave(this)">
                  Select
                </button>
                ${isAdmin ? `<button class="edit-btn" data-id="${product.id}">Edit</button>` : ''}
              </div>
            </div>
          `;
          productGrid.innerHTML += productCard;
        });

        if (isAdmin) {
          productGrid.innerHTML += 
          `<div class="add-product-card">
          <button class="add-new-plan-card">
            <div class="add-icon">+</div>
            <div class="add-text">Add New Plan</div>
          </button>
          </div>`;
        }
      }

      // Add an event listener
      document.addEventListener("DOMContentLoaded", function () {
        // Add change event listening for all filters
        const filters = ["coffee-type", "sort"];
        filters.forEach((filterId) => {
          document
            .getElementById(filterId)
            .addEventListener("change", filterAndSortProducts);
        });

        // Displays all products initially
        filterAndSortProducts();
      });

      // Existing other functions
      function confirmAndSave(button) {
        const card = button.closest(".product-card");
        selectedProductName = card.querySelector("h3").textContent;
        selectedProductPrice = parseFloat(
          card.querySelector(".price").textContent.replace("£", "")
        );
        selectedProductId = card.querySelector("h3").dataset.id;

        document.getElementById(
          "confirmation-message"
        ).textContent = `Are you sure you want to select ${selectedProductName} for £${selectedProductPrice.toFixed(
          2
        )}?`;

        document.getElementById("confirmation-modal").style.display = "block";
      }

      document
        .getElementById("close-confirmation-modal")
        .addEventListener("click", () => {
          document.getElementById("confirmation-modal").style.display = "none";
        });

      document
        .getElementById("confirm-button")
        .addEventListener("click", () => {
          localStorage.setItem("planId", selectedProductId);
          localStorage.setItem("productName", selectedProductName);
          localStorage.setItem("productPrice", selectedProductPrice);
          window.location.href = "./step3.php";
        });

      document.getElementById("cancel-button").addEventListener("click", () => {
        document.getElementById("confirmation-modal").style.display = "none";
      });

      // add get user permission function
      async function checkAdminPermission(userId) {
        try {
          console.log(userId);
          const response = await fetch(`../api/get_user_role.php?user_id=${userId}`);
          const data = await response.json();
          return data.role === 'admin';
        } catch (error) {
          console.error('get user permission failed:', error);
          return false;
        }
      }

      // modify DOMContentLoaded event
      document.addEventListener('DOMContentLoaded', async function() {
        
        try {
          // get user permission first
          isAdmin = await checkAdminPermission(userId);
          console.log(`current user permission: ${isAdmin ? 'admin' : 'user'}`);

          // get product data first
          const plansResponse = await fetch('../api/get_plans.php');
          const plans = await plansResponse.json();
          console.log(plans);
          
          coffeeProducts = plans.data.map(plan => ({
            id: plan.plan_id,
            name: plan.name,
            type: plan.type,
            price: parseFloat(plan.price),
            features: plan.description,
            image: plan.image_url || null,
          }));

          // refresh product list finally
          filterAndSortProducts();

          localStorage.setItem("userId", userId);

        } catch (error) {
          console.error('initialize failed:', error);
        }
      });

      // add event listener
      document.addEventListener('click', async (e) => {
        if (e.target.classList.contains('edit-btn')) {
          const productId = e.target.dataset.id;
          const product = coffeeProducts.find(p => p.id == productId);
          showEditModal(product);
        }
      });

      let currentEditingProduct = null;

      function showEditModal(product) {
        currentEditingProduct = product;
        document.getElementById('edit-name').value = product.name;
        document.getElementById('edit-type').value = product.type;
        document.getElementById('edit-features').value = product.features;
        document.getElementById('edit-price').value = product.price;
        document.getElementById('admin-edit-modal').style.display = 'block';
      }

      // modal close logic
      document.getElementById('close-admin-modal').addEventListener('click', closeEditModal);
      document.getElementById('cancel-edit').addEventListener('click', closeEditModal);

      function closeEditModal() {
        document.getElementById('admin-edit-modal').style.display = 'none';
        currentEditingProduct = null;
      }

      // save changes logic
      document.getElementById('save-changes').addEventListener('click', async () => {
        const formData = new FormData();
        const imageFile = document.getElementById('edit-image').files[0];
        
        formData.append('name', document.getElementById('edit-name').value);
        formData.append('type', document.getElementById('edit-type').value);
        formData.append('description', document.getElementById('edit-features').value);
        formData.append('price', document.getElementById('edit-price').value);
        if(imageFile) formData.append('image', imageFile);

        try {
          const response = await fetch(`../api/update_plan.php?id=${currentEditingProduct.id}`, {
            method: 'POST',
            body: formData
          });

          if (response.ok) {
            const coffeePd = await response.json();
            const index = coffeeProducts.findIndex(p => p.id === currentEditingProduct.id);
            coffeeProducts[index] = {
              ...coffeeProducts[index],
              name: document.getElementById('edit-name').value,
              type: document.getElementById('edit-type').value,
              features: document.getElementById('edit-features').value,
              price: parseFloat(document.getElementById('edit-price').value),
              image: imageFile ? 
                `${coffeePd.image_url}?t=${Date.now()}` : 
                coffeeProducts[index].image
            };
            filterAndSortProducts();
            closeEditModal();
          }
        } catch (error) {
          console.error('update failed:', error);
        }
      });

      // delete logic
      document.getElementById('delete-plan').addEventListener('click', async () => {
        if (confirm('Are you sure you want to delete this product?')) {
          try {
            const response = await fetch(`../api/delete_plan.php?plan_id=${currentEditingProduct.id}`);

            if (response.ok) {
              coffeeProducts = coffeeProducts.filter(p => p.id !== currentEditingProduct.id);
              filterAndSortProducts();
              closeEditModal();
            }
          } catch (error) {
            console.error('delete failed:', error);
          }
        }
      });

      // create new plan
      document.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-new-plan-card')) {
          document.getElementById('add-plan-modal').style.display = 'block';
        }
      });

      // close new plan modal
      document.getElementById('close-add-modal').addEventListener('click', () => {
        document.getElementById('add-plan-modal').style.display = 'none';
      });

      document.getElementById('cancel-add').addEventListener('click', () => {
        document.getElementById('add-plan-modal').style.display = 'none';
      });

      // confirm new plan
      document.getElementById('confirm-add').addEventListener('click', async () => {
        const formData = new FormData();
        const imageFile = document.getElementById('new-image').files[0];
        
        formData.append('name', document.getElementById('new-name').value);
        formData.append('type', document.getElementById('new-type').value);
        formData.append('description', document.getElementById('new-features').value);
        formData.append('price', document.getElementById('new-price').value);
        formData.append('image', imageFile);

        try {
          const response = await fetch('../api/create_plan.php', {
            method: 'POST',
            body:formData
          });

          if (response.ok) {
            const createdPlan = await response.json();
            console.log(createdPlan);
            coffeeProducts.push({
              id: createdPlan.plan_id,
              name: createdPlan.name,
              type: createdPlan.type,
              price: parseFloat(createdPlan.price),
              features: createdPlan.description,
              image: createdPlan.image_url ? 
                `${createdPlan.image_url}?t=${Date.now()}` 
                : 'https://via.placeholder.com/150'
            });
            filterAndSortProducts();
            document.getElementById('add-plan-modal').style.display = 'none';
            document.getElementById('add-plan-form').reset();
          }
        } catch (error) {
          console.error('error:', error);
        }
      });

      // Added file selection event handling
      document.querySelectorAll('input[type="file"]').forEach(input => {
        input.addEventListener('change', function() {
          const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
          this.parentNode.querySelector('.file-name').textContent = fileName;
        });
      });
    </script>

    <style>
      /* unify the button style */
      .edit-btn, #save-changes, #delete-plan, #cancel-edit, 
      .add-new-plan-btn, #confirm-add, #cancel-add {
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
        transition: background-color 0.3s ease;
      }

      .edit-btn, #save-changes, .add-new-plan-btn, #confirm-add {
        background-color: var(--accent);
      }

      #delete-plan {
        background-color: rgb(221, 86, 100);
      }

      #cancel-edit, #cancel-add {
        background-color: #6c757d;
      }

      /* unify the hover effect */
      .edit-btn:hover, #save-changes:hover, 
      .add-new-plan-btn:hover, #confirm-add:hover {
        background-color: var(--text);
      }

      #delete-plan:hover {
        background: #dc3545;
      }

      #cancel-edit:hover, #cancel-add:hover {
        background-color: var(--text);
      }

      /* optimize the grid layout */
      .product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
        padding: 1rem 0;
      }

      /* unify the card style */
      .product-card, .add-product-card {
        background: #fff;
        border-radius: 8px;
        padding: 1.5rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
      }

      .edit-btn {
        background-color: var(--accent);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }

      .edit-btn:hover {
        background-color: var(--text);
      }

      .button-container {
        justify-content: center;
        margin-top: 20px;
        padding: 10px 0;
      }

      .button-container button {
        margin: 0 5px;
      }

      @media (max-width: 480px) {
        .button-container {
          flex-direction: column;
          gap: 12px;
        }
        .button-container button {
          width: 100%;
          margin: 0;
        }
      }

      /* optimize the modal container */
      .admin-edit-modal {
        max-width: 600px;
        padding: 2rem;
        background: #FAF6F1;
        border-radius: 16px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
      }

      /* title style */
      .admin-edit-modal h3 {
        color: #432818;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid #D4A373;
        padding-bottom: 0.5rem;
      }

      /* optimize the form grid */
      .form-grid {
        display: grid;
        grid-template-columns: 120px 1fr;
        gap: 1.2rem;
        margin: 2rem 0;
      }

      .form-grid label {
        color: #6c584c;
        font-weight: 500;
        align-self: center;
      }

      /* input style */
      input, textarea, select {
        padding: 0.8rem;
        border: 2px solid #D4A373;
        border-radius: 6px;
        font-size: 1rem;
        transition: all 0.3s ease;
      }

      input:focus, textarea:focus, select:focus {
        outline: none;
        border-color: #B08D69;
        box-shadow: 0 0 8px rgba(180, 141, 105, 0.2);
      }

      /* optimize the button container */
      .form-buttons {
        display: grid;
        grid-template-columns: repeat(3, auto);
        gap: 1.5rem;
        justify-content: end;
        margin-top: 2rem;
      }

      /* unify the button style */
      .modal-button {
        padding: 1rem 2rem;
        font-size: 1.1rem;
        min-width: 120px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        transition: 
          background-color 0.3s ease,
          color 0.3s ease,
          border-color 0.3s ease;
        font-weight: 500;
      }

      #save-changes {
        background-color: var(--accent);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }
      

      #save-changes:hover {
        background-color: var(--text);
      }

      #delete-plan {
        background-color:rgb(221, 86, 100);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }

      #delete-plan:hover {
        background: #dc3545;
      }

      #cancel-edit {
        background-color: #6c757d;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }

      #cancel-edit:hover {
        background-color: var(--text);
      }

      /* close button style */
      #close-admin-modal {
        color: #6c584c;
        font-size: 1.8rem;
        transition: color 0.3s ease;
      }

      #close-admin-modal:hover {
        color: #B08D69;
      }

      .add-new-plan-btn {
        background-color: var(--accent);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }

      .add-new-plan-btn:hover {
        background-color: var(--text);
      }

      #confirm-add {
        background-color: var(--accent);
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }

      #confirm-add:hover {
        background-color: var(--text);
      }

      #cancel-add {
        background-color: #6c757d;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 1rem;
      }

      #cancel-add:hover {
        background-color: var(--text);
      }

      /* unify the button spacing */
      #add-plan-modal .form-buttons {
        gap: 1.5rem;
        justify-content: flex-end;
      }

      /* add new card style */
      .add-product-card {
        width: 100%;
        min-height: 300px; /* match the height of the product card */
        border: 2px dashed #D4A373;
        border-radius: 8px; /* match the product card radius */
        transition: all 0.3s ease;
        background: rgba(212, 163, 115, 0.05);
      }

      .add-new-plan-card {
        width: 100%;
        height: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: none;
        cursor: pointer;
        padding: 20px;
      }

      .add-icon {
        font-size: 3rem;
        color: #D4A373;
        line-height: 1;
        margin-bottom: 1rem;
        transition: transform 0.3s ease;
      }

      .add-text {
        color: #6c584c;
        font-weight: 500;
        text-align: center;
        font-size: 1.1rem;
      }

      /* hover effect */
      .add-product-card:hover {
        background: rgba(212, 163, 115, 0.1);
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(212, 163, 115, 0.2);
      }

      .add-product-card:hover .add-icon {
        transform: scale(1.1);
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .add-product-card {
          min-height: 250px;
        }
        
        .add-icon {
          font-size: 2.5rem;
        }
        
        .add-text {
          font-size: 1rem;
        }
      }

      .product-image {
        width: 100%;
        height: 200px; 
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        aspect-ratio: 5/4; 
      }

      .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        border-radius: 0px;
        min-width: 100%; 
        min-height: 100%; 
      }

      /* Added file upload style */
      .custom-file-upload {
        position: relative;
        display: flex;
        align-items: center;
        gap: 10px;
      }

      .upload-button {
        background-color: var(--accent);
        color: white;
        padding: 8px 20px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s;
        border: 2px solid var(--accent);
      }

      .upload-button:hover {
        background-color: var(--text);
        border-color: var(--text);
      }

      input[type="file"] {
        position: absolute;
        left: 0;
        opacity: 0;
        width: 1px;
        height: 1px;
      }

      .file-name {
        color: #6c584c;
        font-size: 0.9rem;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
      }

      /* Adjust an existing file input style */
      input[type="file"] {
        padding: 0;
        border: none;
        background: transparent;
      }
    </style>
    <?php include 'Chatbot.php'; ?>
  </body>
</html>
