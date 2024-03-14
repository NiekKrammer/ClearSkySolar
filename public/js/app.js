// Clear local storage after placing order
const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);

if (urlParams.get('clearStorage') === '1') {
    localStorage.clear();
    cart = [];
    updateSidebar();
    displayCart();
}

// Shopping Cart
let cart = getLocalStorage() || [];

const addButton = document.querySelectorAll(".add_btn");
const cartContent = document.getElementById("cart-content");
const cartCounter = document.getElementById("cart-counter");
const totalPrice = document.getElementById("total-price");
const orderList = document.getElementById("order-list");
const empty_cart = document.getElementById('empty_cart');
const shoppingCart = JSON.parse(localStorage.getItem("shopping-cart"));
const totalPriceOrder = document.getElementById("total-price-order");
const orderForm = document.querySelector('.order_form');

updateSidebar();
displayCart();

// Add to cart Button
addButton.forEach(function (button) {
    button.addEventListener("click", function () {
        const productID = this.getAttribute("data-product-id");
        const name = this.getAttribute("data-name");
        const price = parseFloat(this.getAttribute("data-price"));

        const existingItem = cart.find(item => item.productID === productID);

        if (existingItem) {
            existingItem.quantity++;
        } else {
            cart.push({
                productID,
                name,
                price,
                quantity: 1,
            });
        }

        updateLocalStorage();
        updateSidebar();
        displayCart();
    });
});

function updateLocalStorage() {
    localStorage.setItem('shopping-cart', JSON.stringify(cart));
}

function getLocalStorage() {
    const cartJSON = localStorage.getItem('shopping-cart');
    return JSON.parse(cartJSON) || [];
}

if (!shoppingCart || shoppingCart.length === 0) {
    empty_cart.textContent = 'Je winkelwagen is leeg';
}

function updateSidebar() {
    const totalQuantity = cart.reduce((total, item) => total + item.quantity, 0);
    const total = cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
    cartCounter.textContent = totalQuantity;
    totalPrice.textContent = "Totale prijs: €" + total;

    if (totalQuantity === 0) {
        cartCounter.textContent = '';
    }
}

// Show Cart Content
function displayCart() {
    cartContent.innerHTML = cart.map(generateCartItems).join('');

    decreaseButton();
    increaseButton();
}

function generateCartItems(item) {
    return `
    <div class="cart-item mb-3">
        <p class="mb-1" style="color: #111111; font-weight: 450; font-size: 18px;">${item.name} - € ${(item.price * item.quantity).toFixed(2)}</p>
        <div class="d-flex align-items-center gap-2">
            <button class="decrease-item" data-product-id="${item.productID}">
                ${item.quantity === 1 ? '<i class="fa-solid fa-trash-can fa-sm delete_icon"></i>' : '-'}
            </button>
            <p class="item-quantity cart_quantity" style="color: #111111; font-weight: 450;">${item.quantity}</p>
            <button class="increase-item" data-product-id="${item.productID}">+</button>
        </div>
    </div>`;
}

function decreaseButton() {
    const decreaseBtn = document.querySelectorAll(".decrease-item");
    decreaseBtn.forEach(function (button) {
        button.addEventListener("click", function () {
            const productID = this.getAttribute("data-product-id");
            const item = cart.find(item => item.productID === productID);
            if (item && item.quantity > 1) {
                item.quantity--;
            } else {
                cart = cart.filter(item => item.productID !== productID);
            }
            updateLocalStorage();
            updateSidebar();
            displayCart();
        });
    });
}

function increaseButton() {
    const increaseBtn = document.querySelectorAll(".increase-item");
    increaseBtn.forEach(function (button) {
        button.addEventListener("click", function () {
            const productID = this.getAttribute("data-product-id");
            const item = cart.find(item => item.productID === productID);
            if (item) {
                item.quantity++;
            }
            updateLocalStorage();
            updateSidebar();
            displayCart();
        });
    });
}

// Display cart items in the orderList
function displayOrderList() {
    cart.forEach(item => {
        orderList.innerHTML += `
            <div class="order my-3 py-3 m-auto">
                <p style="font-size: 18px;">${item.name}</p>
                <p class="py-2" style="color: #3D3A3AF2;">Aantal: ${item.quantity}</p>
                <p style="color: #3D3A3AF2;">Prijs: €${(item.price * item.quantity).toFixed(2)}</p>
            </div>
        `;
    });
    totalPriceOrder.innerText = "Totale Prijs: €" + cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
}

displayOrderList();

// Send JSON data to OrderItems entity
let orderItemsData = [];

document.addEventListener("DOMContentLoaded", function () {
    const cartDataField = document.getElementById("cartData");
    const cartData = JSON.stringify(cart, null, 2);
    const orderItemsDataField = document.createElement("input");

    orderItemsDataField.type = "hidden";
    orderItemsDataField.name = "orderItemsData";
    orderItemsDataField.value = JSON.stringify(orderItemsData);

    orderForm.appendChild(orderItemsDataField);
    orderForm.addEventListener('click', function () {
        cartDataField.value = cartData;
    });
});

function updateOrderItemsData() {
    orderItemsData = cart.map(item => ({
        item: item.name,
        price: item.price * item.quantity,
        quantity: item.quantity,
    }));
}
updateOrderItemsData();
