document.addEventListener('DOMContentLoaded', () => {
    function hideAllMenuLists() {
    menuLists.forEach(list => {
        list.classList.remove('active', 'slide-in-left', 'slide-in-right', 'slide-out-left', 'slide-out-right');
        list.classList.add('hidden');
    });
}

    const modal = document.getElementById('formModal');
    const closeBtn = document.querySelector('.close');

    // Modal toggle
    document.querySelectorAll('.add-card').forEach(card => {
        card.addEventListener('click', () => {
            modal.style.display = 'block';
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }

    window.addEventListener('click', e => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });

    // Tab order for slide direction
    const tabOrder = ['recommend', 'food', 'drink', 'dessert', 'alcohol'];
    let currentMain = 'recommend';
    let currentSub = 'new';

    const subcategories = {
        recommend: ["New", "Best Sale"],
        food: ["Pizza", "Pasta", "Burger", "Main"],
        drink: ["Coffee", "Milk", "Fruit", "Tea"],
        dessert: ["Cake", "Kaki", "Toast", "Crepe"],
        alcohol: ["Beer", "Wine"]
    };

    const mainTabs = document.querySelectorAll('.tab-button');
    const subcategoryTabs = document.getElementById('subcategoryTabs');
    const menuLists = document.querySelectorAll('.menu-list');
    const menuContainer = document.getElementById('menuContainer');

    function renderSubTabs(mainCat) {
        subcategoryTabs.innerHTML = '';
        if (!subcategories[mainCat]) return;

        subcategories[mainCat].forEach((sub, index) => {
            const btn = document.createElement('button');
            btn.className = 'sub-tab-button' + (index === 0 ? ' active' : '');
            btn.setAttribute('data-sub', sub.toLowerCase());
            btn.textContent = sub;
            subcategoryTabs.appendChild(btn);
        });
    }
    
function showMenu(main, sub, direction = 'right') {
    menuLists.forEach(list => {
        const isMatch = list.dataset.main === main && list.dataset.sub === sub;

        if (!isMatch) {
            list.classList.remove(
                'active',
                'slide-in-left',
                'slide-in-right',
                'slide-out-left',
                'slide-out-right'
            );
            list.classList.add('hidden');
        } else {
            // เริ่ม transition จากด้านที่ต้องการ
            list.classList.remove(
                'active',
                'hidden',
                'slide-in-left',
                'slide-in-right',
                'slide-out-left',
                'slide-out-right'
            );
            list.classList.add(`slide-in-${direction}`);

            // ✅ รอให้ browser apply class ก่อน แล้วค่อย activate
            setTimeout(() => {
                list.classList.remove(`slide-in-${direction}`);
                list.classList.add('active');
            }, 200); // ต้องใช้ delay นิดนึงให้ transition ทำงาน
        }
    });
}


    // Main
    mainTabs.forEach(tab => {
    tab.addEventListener('click', () => {
        const newMain = tab.getAttribute('data-main');
        if (newMain === currentMain) return;

        const oldIndex = tabOrder.indexOf(currentMain);
        const newIndex = tabOrder.indexOf(newMain);
        const direction = newIndex > oldIndex ? 'right' : 'left';
        

        mainTabs.forEach(t => t.classList.remove('active'));
        tab.classList.add('active');

        renderSubTabs(newMain);
        const newSub = subcategories[newMain][0].toLowerCase();

        currentMain = newMain;
        currentSub = newSub;

        hideAllMenuLists();
        setTimeout(() => {
            showMenu(newMain, newSub, direction);
        }, 300);
    });
});



    // Subcategory tab click
   subcategoryTabs.addEventListener('click', (e) => {
    if (e.target.matches('.sub-tab-button')) {
        const all = subcategoryTabs.querySelectorAll('button');
        const previousSub = currentSub;
        all.forEach(btn => btn.classList.remove('active'));
        e.target.classList.add('active');

        const mainCat = document.querySelector('.tab-button.active').getAttribute('data-main');
        const newSub = e.target.getAttribute('data-sub');

        // คำนวณทิศทางย่อย
        const subList = subcategories[mainCat];
        const oldIndex = subList.findIndex(s => s.toLowerCase() === previousSub);
        const newIndex = subList.findIndex(s => s.toLowerCase() === newSub);
        const direction = newIndex > oldIndex ? 'right' : 'left';

        currentSub = newSub;
        showMenu(mainCat, newSub, direction);
    }
});


    // เริ่มต้น
    renderSubTabs('recommend');
    showMenu('recommend', 'new');

    // Form dropdown binding
    const groupSelect = document.getElementById('menu_group');
    const typeSelect = document.getElementById('menu_type');
    const drinkGroup = document.getElementById('drink_type_group');

    const typeOptions = {
        // Recommend: ["New", "Best Sale"],
        Food: ["Pizza", "Pasta", "Burger", "Main"],
        Drink: ["Coffee", "Milk", "Fruit", "Tea"],
        Dessert: ["Cake", "Kaki", "Toast", "Crepe"],
        Alcohol: ["Beer", "Wine"]
    };

    groupSelect.addEventListener('change', () => {
        const selectedGroup = groupSelect.value;
        typeSelect.innerHTML = '<option value="">-- Select Type --</option>';

        if (typeOptions[selectedGroup]) {
            typeOptions[selectedGroup].forEach(type => {
                const opt = document.createElement('option');
                opt.value = type;
                opt.textContent = type;
                typeSelect.appendChild(opt);
            });
        }

        drinkGroup.style.display = (selectedGroup === 'Drink') ? 'block' : 'none';
    });

const cart = [];
const orderContent = document.getElementById('orderContent');

// ปุ่มเปิด/ปิด modal
const orderModal = document.getElementById('orderModal');
const openOrderBtn = document.getElementById('openOrderBtn');
const closeOrderBtn = document.querySelector('.close-order');

openOrderBtn.addEventListener('click', () => {
  orderModal.style.display = 'block';
});

closeOrderBtn.addEventListener('click', () => {
  orderModal.style.display = 'none';
});

window.addEventListener('click', (e) => {
  if (e.target === orderModal) {
    orderModal.style.display = 'none';
  }
});

// รายการในตะกร้า
    function renderCart() {
    orderContent.innerHTML = '';

    if (cart.length === 0) {
        orderContent.innerHTML = '<p>No items in cart.</p>';
        return;
    }

    // 🔄 รวมรายการซ้ำ
    const grouped = {};
    cart.forEach(item => {
        const key = item.name + '|' + (item.group || '');
        if (!grouped[key]) {
            grouped[key] = { ...item, qty: 1 };
        } else {
            grouped[key].qty++;
        }
    });

    // 🧾 แสดงรายการแบบตาราง 3 คอลัมน์
    let total = 0;
    let index = 1;
    for (const key in grouped) {
        const item = grouped[key];
        const subtotal = item.qty * parseFloat(item.price);
        total += subtotal;

        const row = document.createElement('div');
        row.className = 'order-row';
        row.innerHTML = `
            <div class="order-name">${index}. ${item.name} <span style="color:#888;font-size:0.9em;">(${item.group || '-'})</span></div>
            <div class="order-qty">
                <button class="qty-btn decrease" data-name="${item.name}" data-group="${item.group || ''}">-</button>
                <span>${item.qty}</span>
                <button class="qty-btn increase" data-name="${item.name}" data-group="${item.group || ''}">+</button>
            </div>
            <div class="order-subtotal">${subtotal.toFixed(2)} ฿</div>
            `;
        orderContent.appendChild(row);
        index++;
    }

    // 💰 รวมยอดทั้งหมด
    const totalP = document.createElement('p');
    totalP.className = 'order-total';
    totalP.innerHTML = `<strong>Total: ${total.toFixed(2)} ฿</strong>`;
    orderContent.appendChild(totalP);

    // 🛒 ปุ่มสั่งซื้อ
    const orderBtn = document.createElement('button');
    orderBtn.className = 'checkout-btn';
    orderBtn.textContent = 'สั่งออเดอร์';
    orderBtn.addEventListener('click', () => {
        if (cart.length === 0) return alert('Cart is empty.');

        const tableId = document.getElementById('tableId').value;
        if (!tableId) return alert('Please enter table number.');

        const payload = {
            customer: 'Admin',
            table: tableId,
            items: cart
        };

        fetch('submit_order.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            alert('Order placed!');
            cart.length = 0;
            renderCart();
            orderModal.style.display = 'none';
        })
        .catch(err => {
            console.error('Error placing order:', err);
            alert('Failed to place order.');
        });
    });

    orderContent.appendChild(orderBtn);
}

   document.addEventListener('click', (e) => {
  if (e.target.classList.contains('add-btn')) {
    const name = e.target.dataset.name;
    const price = e.target.dataset.price;
    const group = e.target.dataset.group || '';

    cart.push({ name, price, group });
    renderCart();

    // if (!name || !price) {
    //   console.warn('ปุ่ม Add ไม่มีข้อมูล:', e.target);
    //   return;
    // }

    // cart.push({ name, price });
    // renderCart();
  }

  // จัดการปุ่ม เพิ่ม/ลด
   if (e.target.classList.contains('qty-btn')) {
    const name = e.target.dataset.name;
    const group = e.target.dataset.group || '';
    const isIncrease = e.target.classList.contains('increase');

    const index = cart.findIndex(item => item.name === name && (item.group || '') === group);
    if (index !== -1) {
      if (isIncrease) {
        cart.push({ name: cart[index].name, price: cart[index].price, group: cart[index].group });
      } else {
        cart.splice(index, 1); // ลบหนึ่งรายการ
      }
    }

    renderCart();
  }
    });
    const fileDisplay = document.getElementById('fileDisplay');
    const fileInput = document.getElementById('foodImg');
    const imagePreview = document.getElementById('imagePreview');

    if (fileDisplay && fileInput && imagePreview) {
        const uploadText = fileDisplay.querySelector('.upload-text');
        const uploadIcon = fileDisplay.querySelector('.upload-icon');

        fileDisplay.addEventListener('click', () => {
            fileInput.click();
        });

        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                    if (uploadText) uploadText.textContent = file.name;
                    if (uploadIcon) uploadIcon.textContent = '✓';
                    fileDisplay.classList.add('active');
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // ✅ สถานะปุ่ม Submit
    const form = document.querySelector('#formModal form');
    const submitBtn = document.getElementById('add-btn');
    if (form && submitBtn) {
        form.addEventListener('submit', () => {
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Adding Menu...';
        });
    }

    // เปลี่ยน getClosedMenus/setClosedMenus ให้ใช้ AJAX
    function updateMenuClosedStatus(id, closed, cb) {
        fetch('update_menus_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, closed })
        })
        .then(res => res.json())
        .then(cb)
        .catch(() => cb({ success: false }));
    }

    // เมื่อโหลดหน้า ให้เช็คเมนูที่ปิด (จาก data-is-closed)
    document.querySelectorAll('.menu-list li, .drink-card').forEach(item => {
        const btn = item.querySelector('.toggle-menu-btn');
        const id = btn?.dataset.id;
        if (!id) return;
        // อ่านจาก data-is-closed ที่ฝังมากับ HTML
        if (btn.dataset.isClosed === "1") {
            item.classList.add('menu-closed');
            btn.textContent = 'เปิดเมนู';
            btn.dataset.status = 'closed';
            item.querySelector('.soldout-label').style.display = 'inline';
        }
    });

    // จัดการคลิกปุ่มเปิด/ปิดเมนู
    document.addEventListener('click', e => {
        if (e.target.classList.contains('toggle-menu-btn')) {
            const btn = e.target;
            const item = btn.closest('li') || btn.closest('.drink-card');
            const id = btn.dataset.id;
            const willClose = btn.dataset.status === 'open';

            updateMenuClosedStatus(id, willClose ? 1 : 0, (res) => {
                if (res.success) {
                    if (willClose) {
                        item.classList.add('menu-closed');
                        btn.textContent = 'เปิดเมนู';
                        btn.dataset.status = 'closed';
                        item.querySelector('.soldout-label').style.display = 'inline';
                    } else {
                        item.classList.remove('menu-closed');
                        btn.textContent = 'ปิดเมนู';
                        btn.dataset.status = 'open';
                        item.querySelector('.soldout-label').style.display = 'none';
                    }
                    btn.dataset.isClosed = willClose ? "1" : "0";
                } else {
                    alert('เกิดข้อผิดพลาดในการอัปเดตสถานะเมนู');
                }
            });
        }
    });
});