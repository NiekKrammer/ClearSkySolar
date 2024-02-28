// Shopping Cart
let cart = getLocalStorage() || [];

const addButton = document.querySelectorAll(".add_btn");
const cartContent = document.getElementById("app-content");
const cartCounter = document.getElementById("app-counter");
const totalPrice = document.getElementById("total-price");
const orderList = document.getElementById("order-list");
const totalPriceOrder = document.getElementById("total-price-order");
const orderForm = document.querySelector('.order_form');

updateSidebar();
displayCart();

// Add to app Button
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
    localStorage.setItem('shopping-app', JSON.stringify(cart));
}

function getLocalStorage() {
    const cartJSON = localStorage.getItem('shopping-app');
    return JSON.parse(cartJSON) || [];
}

function updateSidebar() {
    const totalQuantity = cart.reduce((total, item) => total + item.quantity, 0);
    const total = cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
    cartCounter.textContent = totalQuantity;
    totalPrice.textContent = "Totale prijs: € " + total;

    if (totalQuantity === 0) {
        cartCounter.textContent = '';
    }
}

// Show Cart Content
function displayCart() {
    const cartHTML = cart.map(generateCartItems).join('');
    cartContent.innerHTML = cartHTML;

    decreaseButton();
    increaseButton();
}

function generateCartItems(item) {
    return `
    <div class="cart-item mb-3">
        <p class="m-0">${item.name} - € ${(item.price * item.quantity).toFixed(2)}</p>
        <div class="d-flex align-items-center gap-2">
            <button class="decrease-item" data-product-id="${item.productID}">
                ${item.quantity === 1 ? '<i class="fa-solid fa-trash-can fa-sm delete_icon"></i>' : '-'}
            </button>
            <p class="item-quantity cart_quantity">${item.quantity}</p>
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

// Display app items in the orderList
function displayOrderList() {
    cart.forEach(item => {
        orderList.innerHTML += `
            <div class="order w-50 my-3 py-3 m-auto">
                <p style="font-size: 18px;">${item.name}</p>
                <p class="py-2" style="color: rgba(0,0,0,0.67);">Aantal: ${item.quantity}</p>
                <p style="color: rgba(0,0,0,0.67);">Prijs: € ${(item.price * item.quantity).toFixed(2)}</p>
            </div>
        `;
    });
    totalPriceOrder.innerText = "Totale Prijs: € " + cart.reduce((total, item) => total + item.price * item.quantity, 0).toFixed(2);
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
