# Online Food Ordering System (MVP)

## 📌 Project Overview
This project is an **Online Food Ordering Website** developed as a **Minimum Viable Product (MVP)** within a limited time frame.  
The goal of the project is to provide a simple and functional system that allows users to browse food menus, place orders, and track order status, while enabling the restaurant administrator to manage foods, users, orders, and costs.

Due to time constraints and MVP scope, the project was implemented using a **simple architecture** without applying complex patterns such as MVC.

---

## 👥 Team Members & Roles

| Name | Role |
|---|---|
| Rahil Ahmadi | Scrum Master / Frontend Developer |
| Zahra Habibollahi | Backend Developer |
| Faizeh Ehsanfar | Database Manager / Backend Developer |
| Fatemeh Dadavand | UI Designer / Frontend Developer |

---

## ❓ Problem Statement
Traditional food ordering methods (phone calls or in-person ordering) often cause:
- Time consumption for customers and restaurants
- Human errors in order registration
- Lack of order tracking
- Poor centralized management of users and orders

There is a need for a **simple online system** that allows customers to place orders easily and enables restaurant administrators to manage all operations centrally.

---

## 💡 Proposed Solution
To address these issues, a web-based food ordering system was designed with two main sections:
1. **User Panel** for browsing food, placing orders, and tracking order status
2. **Admin Panel** for managing foods, users, orders, discounts, and delivery costs

The system focuses on core functionalities and delivers a working MVP.

---

## project preview
![main page](./website/asset/img/img2.png)
## food menu page
![food menu](./website/asset/img/img3.png)
## Admin Dashboard
![Admin Dashboard](./website/asset/img/img1.png)


## 🛠 Technologies & Tools

### Frontend
- HTML
- CSS
- JavaScript

### Backend
- PHP

### Database
- PHP MyAdmin
**Reason for selection:**
- Lightweight and simple
- Suitable for small to medium projects
- Supports relational data (users, orders, foods)

---

## 🗂 Project Structure
```text
Dastpokht2/
│
├── website/
│   │
│   ├── index.php                  # Main landing page
│   ├── login.php                  # User login logic
│   ├── register.php               # User registration logic
│   ├── shoppingCart.php           # Shopping cart backend
│   ├── sync_cart.php              # Sync cart data between client and server
│   ├── wallet.php                 # User wallet logic
│   ├── wallet_success.php         # Wallet payment success page
│   ├── order.php                  # Order placement logic
│   ├── process_payment.php        # Payment processing logic
│   │
│   ├── style.css                  # Main website styles
│   │
│   ├── Admin/                     # Admin panel
│   │   ├── index.html             # Admin landing page
│   │   ├── login.html             # Admin login page
│   │   ├── panel.php              # Admin dashboard
│   │   ├── users.php              # Users management page
│   │   ├── orders.php             # Orders management page
│   │   ├── shipping.php           # Shipping overview
│   │   ├── FoodManager.php        # Food/products management
│   │   ├── api_foods.php          # API endpoint for foods data
│   │   ├── getDashboardStats.php  # Fetch dashboard statistics
│   │   ├── get_messages.php       # Retrieve user messages
│   │   ├── delete_message.php     # Delete user messages
│   │   │
│   │   └── ...
│   │
│   ├── asset/                     # static assets
│   │       ├── font/               
│   │       ├── icon/
│   │       └── img/
│   │            └── FoodImage/
│   │      
│   ├── js                     # Retrieve user messages
│   │    ├── AddFood.js        # Handle adding new food items in admin panel
│   │    ├── chart.js          # Render charts and statistics for admin dashboard
│   │    ├── dargah.js         # Payment gateway client-side logic
│   │    ├── food.js           # Food listing and selection logic
│   │    ├── foodManager.js    # Admin-side food management interactions
│   │    ├── information.js    # Handle user information form actions
│   │    ├── login.js          # User login form validation and actions
│   │    ├── order.js          # Order submission and status handling
│   │    ├── register.js       # User registration form validation
│   │    ├── shipping.js       # Shipping management interactions (admin)
│   │    ├── shoppingCart.js   # Shopping cart client-side logic
│   │    ├── users.js          # Admin users management interactions
│   │    └── wallet.js         # Wallet charge and balance update logic
│   │
│   │                                            
│   ├── css/
│   │    ├── index.css          # Main styles for the home page
│   │    ├── about.css          # Styles for the About page
│   │    ├── contact.css        # Styles for the Contact page
│   │    ├── login.css          # Login page styles
│   │    ├── register.css       # User registration page styles
│   │    ├── panel.css          # User/Admin dashboard styles
│   │    ├── AddFood.css        # Styles for adding new food items
│   │    ├── food.css           # Food listing and details styles
│   │    ├── shoppingCart.css   # Shopping cart page styles
│   │    ├── orderStatus.css    # Order status and tracking styles
│   │    ├── wallet.css         # User wallet and balance styles
│   │    ├── information.css    # User information and profile styles
│   │    ├── dargah.css         # Payment gateway page styles
│   │        
│   │  
│   └── ...
│
└── README.md                      # Project documentation



---

## 🧱 Class Design & Responsibilities

### User Class
- Handles user registration and login
- Stores user information
- Manages user roles (admin / normal user)

### Food Class
- Stores food details
- Handles adding, editing, deleting foods
- Enables activating or deactivating food items

### Order Class
- Manages order creation
- Stores order status
- Links users with ordered food items

### Wallet Class
- Manages user wallet balance
- Handles refunds in case of order cancellation

### Discount Class
- Manages discount codes
- Applies discounts to orders
- Calculates final order price

---

## 🔍 Code Deep Dive

### Order Registration Process
1. User selects food items and submits the order
2. System validates user wallet or payment method
3. Order is saved in the database
4. Initial order status is set (e.g., "Registered")

### Order Status Management (Admin Side)
1. Admin reviews orders
2. Admin updates order status (Preparing / Sent / Cancelled)
3. In case of cancellation, the wallet balance is refunded

---

## 📦 Admin Panel Features
- Food management (Add / Edit / Delete / Activate / Deactivate)
- User management (Admin & Normal users)
- Order management and status updates
- Discount code management
- Delivery cost management
- User messages management (Contact Us)

---

## 📊 Project Management on GitHub
- Team collaboration using a shared GitHub repository
- Regular commits for each feature
- Project documentation maintained in `README.md`

---

## ✅ Conclusion
This project delivers a functional **MVP version of an online food ordering system** that covers essential requirements for both users and administrators.  
The system is designed with simplicity in mind and can be extended in the future with more advanced architectural patterns and features.

---

## 🚀 Future Improvements
- Integration with real payment gateways
- Notification system (SMS / Email)
- User rating and review system
- Improved UI/UX
