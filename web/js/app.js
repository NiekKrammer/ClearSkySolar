// Form hide/show Section
const deliveryOption = document.getElementById("order_deliveryOption");
const commentSection = document.getElementById("commentSection");

if (deliveryOption != null) {
    deliveryOption.addEventListener("change", function () {
        if (this.value === "bezorgen") {
            commentSection.style.display = "block";
        } else {
            commentSection.style.display = "none";
        }
    });
}

// Filter Buttons
function openTab(tabName) {
    const tabContents = document.getElementsByClassName('tab-content');
    for (let i = 0; i < tabContents.length; i++) {
        tabContents[i].style.display = 'none';
    }

    const tabButtons = document.getElementsByClassName('tab-button');
    for (let i = 0; i < tabButtons.length; i++) {
        tabButtons[i].classList.remove('active'); // Use classList to remove the class
    }

    const selectedTab = document.getElementById(tabName);

    if (!selectedTab) {
        return;
    }

    selectedTab.style.display = 'block';
    const correspondingButton = document.querySelector(`[onclick="openTab('${tabName}')"]`);
    correspondingButton.classList.add('active');
}

// Clear local storage after submitting order form
document.addEventListener("DOMContentLoaded", function () {
    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);

    if (urlParams.get('clearStorage') == 1) {
        localStorage.clear();
        app = [];
        updateSidebar();
        displayCart();
    }
});
