document.addEventListener('DOMContentLoaded', function () {
  // Global variables for order management
  let lastOrderIds = [];
  const modal = document.getElementById('editOrderModal');
  const list = document.getElementById('order-list');
  const totalDisplay = document.getElementById('order-total');
  let currentItems = [];
  let currentOrderId = null;

  // ========== ORDER FETCHING AND AUTO-UPDATE ==========
  function fetchOrdersAndUpdate() {
  fetch('fetch_orders.php')
    .then(res => res.json())
    .then(data => {
      const newIds = data.map(o => o.id);
      const isNewOrder = lastOrderIds.length > 0 && newIds[0] !== lastOrderIds[0];
      if (isNewOrder) {
        const alertSound = document.getElementById('order-alert-sound');
        if (alertSound) {
          alertSound.play();
        }
      }
      lastOrderIds = newIds;

      const tbody = document.querySelector('.order-table tbody');
      if (!tbody) return;
      tbody.innerHTML = '';

      data.forEach(row => {
        tbody.innerHTML += `
          <tr data-status="${row.status.toLowerCase()}">
            <td>#ORD-${row.id}</td>
            <td>${row.customer_name}</td>
            <td>${row.table_id || '<span style="color:#888;">-</span>'}</td>
            <td>
              <div>${row.date}</div>
              <div class="timestamp">${row.time}</div>
            </td>
            <td><div class="order-details">${row.order_details_html}</div></td>
            <td>฿${parseFloat(row.total).toFixed(2)}</td>
            <td><span class="badge badge-${row.status.toLowerCase()}">${row.status}</span></td>
            <td>
              ${row.status === 'Pending'
                ? `<div class="order-actions">
                     <button class="order-btn edit-order-btn" data-order-id="${row.id}">Edit</button>
                     <button class="order-btn accept-btn" data-order-id="${row.id}">Accept</button>
                   </div>`
                   : row.status === 'Accepted'
                ? `<div class="order-actions">
                    <button class="order-btn checkout-btn" data-order-id="${row.id}">Checkout</button>
                  </div>`
                : `<div class="order-btn completed"><i>✓</i> Completed</div>`}
            </td>
          </tr>
        `;
      });

      attachEventListeners();
      
      const activeFilter = document.querySelector('.filter-btn.active');
      if (activeFilter) activeFilter.click();
    })
    .catch(error => {
      console.error('Error fetching orders:', error);
    });
}


  // ========== EVENT LISTENERS ATTACHMENT ==========
  function attachEventListeners() {
    // Checkout buttons
    const checkoutButtons = document.querySelectorAll('.checkout-btn');
    checkoutButtons.forEach(button => {
      button.removeEventListener('click', handleCheckoutClick);
      button.addEventListener('click', handleCheckoutClick);
    });

    // Filter buttons
    const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('.order-table tbody tr');

    filterButtons.forEach(button => {
      // Remove existing listeners to prevent duplicates
      button.removeEventListener('click', handleFilterClick);
      button.addEventListener('click', handleFilterClick);
    });

    // Accept buttons
    const acceptButtons = document.querySelectorAll('.accept-btn');
    acceptButtons.forEach(button => {
      button.removeEventListener('click', handleAcceptClick);
      button.addEventListener('click', handleAcceptClick);
    });

    // Edit buttons
    const editButtons = document.querySelectorAll('.edit-order-btn');
    editButtons.forEach(button => {
      button.removeEventListener('click', handleEditClick);
      button.addEventListener('click', handleEditClick);
    });
  }

  // ========== EVENT HANDLER FUNCTIONS ==========
  function handleFilterClick() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const rows = document.querySelectorAll('.order-table tbody tr');
    
    // ปรับปุ่ม active
    filterButtons.forEach(btn => btn.classList.remove('active'));
    this.classList.add('active');

    const filter = this.textContent.trim().toLowerCase();

    rows.forEach(row => {
      const status = row.dataset.status;
      const match =
        filter === 'all orders' ||
        (filter === 'pending' && status === 'pending') ||        
        (filter === 'cancelled' && status === 'cancelled')||
        (filter === 'accepted' && status === 'accepted') ||
        (filter === 'completed' && status === 'completed');


      row.style.display = match ? 'table-row' : 'none';
    });
  }

  function handleAcceptClick(e) {
    e.preventDefault(); // ป้องกันการ submit form ปกติ

    // ป้องกันการคลิกซ้ำ
    this.disabled = true;
    this.textContent = 'Processing...';

    const row = this.closest('tr');
    const orderId = this.dataset.orderId;

    // ส่ง AJAX request ไปยัง backend เพื่ออัพเดท status ใน database
    const formData = new FormData();
    formData.append('order_id', orderId);
    
    fetch('update_order_status.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const statusCell = row.querySelector('td:nth-child(7)');
            const badge = statusCell.querySelector('.badge');

            // อัปเดตป้ายสถานะ
            badge.classList.remove('badge-pending');
            badge.classList.add('badge-accepted');
            badge.textContent = 'Accepted';
            
            // อัปเดต data-status ของแถว เพื่อให้ Filter ทำงานถูก
            row.setAttribute('data-status', 'accepted');

            // ✅ จุดที่ 1 (แก้ไข): สร้างปุ่ม Checkout ขึ้นมาแทนที่ปุ่ม Accept/Edit
            // สังเกตว่าเราใช้ `orderId` ที่เก็บไว้ตอนต้นฟังก์ชัน
            const actionCell = row.querySelector('td:last-child');
            if (actionCell) {
                 actionCell.innerHTML = `
                    <div class="order-actions">
                        <button class="order-btn checkout-btn" data-order-id="${orderId}">Checkout</button>
                    </div>`;
            }

            // ✅ จุดที่ 2 (สำคัญมาก): เรียกใช้ฟังก์ชัน attachEventListeners() อีกครั้ง
            // เพื่อให้ JavaScript รู้จักปุ่ม Checkout ที่เราเพิ่งสร้างขึ้นมาใหม่
            attachEventListeners();

        } else {
            throw new Error(data.error || 'Unknown error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the order status.');
        // คืนค่าปุ่มให้กลับมาคลิกได้ถ้าเกิด error
        this.disabled = false;
        this.textContent = 'Accept';
    });
}

  function handleEditClick() {
    const row = this.closest('tr');
    const orderId = this.dataset.orderId;
    const orderDetailsElement = row.querySelector('.order-details');
    
    if (!orderDetailsElement) {
      console.error('Order details not found');
      return;
    }
    
    const lines = orderDetailsElement.innerHTML.trim().split('<br>').filter(Boolean);

    currentItems = [];
    currentOrderId = orderId;

    lines.forEach(line => {
      const cleanLine = line.trim();
      if (!cleanLine) return;

      // ✅ แบบมี group + qty: Pizza (Main) *2 - 500 ฿
      const matchWithQty = cleanLine.match(/^(.+?)\s*\((.+?)\)\s*\*(\d+)\s*-\s*([\d,]+(?:\.\d+)?)\s*฿$/);
      
      // ✅ แบบมี group ไม่มี qty: Pizza (Main) - 250 ฿
      const matchWithoutQty = cleanLine.match(/^(.+?)\s*\((.+?)\)\s*-\s*([\d,]+(?:\.\d+)?)\s*฿$/);
      
      // ✅ แบบไม่มี group แต่มี qty: Pizza *2 - 500 ฿
      const matchNoGroupWithQty = cleanLine.match(/^(.+?)\s*\*(\d+)\s*-\s*([\d,]+(?:\.\d+)?)\s*฿$/);

      // ✅ แบบไม่มี group ไม่มี qty: Pizza - 250 ฿
      const matchNoGroupWithoutQty = cleanLine.match(/^(.+?)\s*-\s*([\d,]+(?:\.\d+)?)\s*฿$/);

      if (matchWithQty) {
        const [, name, group, qtyStr, totalPriceStr] = matchWithQty;
        const qty = parseInt(qtyStr, 10);
        const totalPrice = parseFloat(totalPriceStr.replace(/,/g, ''));
        const pricePerItem = parseFloat((totalPrice / qty).toFixed(2));

        currentItems.push({
          name: name.trim(),
          group: group.trim(),
          qty,
          price: pricePerItem
        });
      } else if (matchWithoutQty) {
        const [, name, group, priceStr] = matchWithoutQty;
        const price = parseFloat(priceStr.replace(/,/g, ''));

        currentItems.push({
          name: name.trim(),
          group: group.trim(),
          qty: 1,
          price
        });
      } else if (matchNoGroupWithQty) {
        const [, name, qtyStr, totalPriceStr] = matchNoGroupWithQty;
        const qty = parseInt(qtyStr, 10);
        const totalPrice = parseFloat(totalPriceStr.replace(/,/g, ''));
        const pricePerItem = parseFloat((totalPrice / qty).toFixed(2));

        currentItems.push({
          name: name.trim(),
          group: '', // ไม่มีกลุ่ม
          qty,
          price: pricePerItem
        });
      } else if (matchNoGroupWithoutQty) {
        const [, name, priceStr] = matchNoGroupWithoutQty;
        const price = parseFloat(priceStr.replace(/,/g, ''));

        currentItems.push({
          name: name.trim(),
          group: '',
          qty: 1,
          price
        });
      } else {
        console.warn('❌ ไม่สามารถแปลงบรรทัดนี้ได้:', cleanLine);
      }
    });

    console.log('✅ รายการที่แปลงได้:', currentItems);
    renderOrderItems();
    if (modal) {
      modal.classList.remove('hidden');
    }
  }

  // เพิ่มฟังก์ชัน handleCheckoutClick ใหม่:
function handleCheckoutClick(e) {
  e.preventDefault();
  
  // ป้องกันการคลิกซ้ำ
  this.disabled = true;
  this.textContent = 'Processing...';
  
  const row = this.closest('tr');
  const orderId = this.dataset.orderId;
  
  // แสดง confirmation dialog
  if (!confirm('Are you sure you want to checkout this order?')) {
    this.disabled = false;
    this.textContent = 'Checkout';
    return;
  }
  
  // ส่ง AJAX request ไปยัง backend เพื่ออัพเดท status เป็น Completed
  const formData = new FormData();
  formData.append('order_id', orderId);
  formData.append('action', 'checkout');
  
  fetch('update_order_status.php', {
    method: 'POST',
    body: formData,
    headers: {
    'X-Requested-With': 'XMLHttpRequest'  // ✅ ต้องมี!
  }
  })
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      // อัพเดท UI เมื่อ backend อัพเดทสำเร็จ
      const statusCell = row.querySelector('td:nth-child(7)');
      const badge = statusCell.querySelector('.badge');

      // Update status
      badge.classList.remove('badge-accepted');
      badge.classList.add('badge-completed');
      badge.textContent = 'Completed';

      // แก้ data-status ให้ JS filter ใช้ได้
      row.setAttribute('data-status', 'completed');

      // Replace ปุ่ม
      this.parentNode.innerHTML = '<div class="order-btn completed"><i>✓</i> Completed</div>';
      
      alert('Order has been completed successfully!');
    } else {
      throw new Error(data.error || 'Unknown error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    alert('An error occurred while completing the order.');
    this.disabled = false;
    this.textContent = 'Checkout';
  });
}

  // ========== ORDER ITEM RENDERING ==========
  function renderOrderItems() {
    if (!list || !totalDisplay) return;
    
    list.innerHTML = '';
    let total = 0;

    currentItems.forEach((item, i) => {
      const row = document.createElement('div');
      row.className = 'order-item';

      const itemTotal = item.qty * item.price;
      total += itemTotal;

      row.innerHTML = `
        <span class="index">${i + 1}.</span>
        <span class="name">${item.name}</span>
        <div class="qty-controls">
          <button class="qty-btn dec" data-i="${i}">−</button>
          <input type="text" value="${item.qty}" readonly>
          <button class="qty-btn inc" data-i="${i}">+</button>
        </div>
        <span class="price">${itemTotal.toFixed(2)} ฿</span>
      `;

      list.appendChild(row);
    });

    totalDisplay.textContent = `${total.toFixed(2)} ฿`;
  }

  // ========== MODAL EVENT LISTENERS ==========
  // Qty controls
  if (list) {
    list.addEventListener('click', e => {
      if (e.target.classList.contains('qty-btn')) {
        const i = parseInt(e.target.dataset.i);

        if (e.target.classList.contains('inc')) {
          currentItems[i].qty++;
        }

        if (e.target.classList.contains('dec')) {
          if (currentItems[i].qty > 1) {
            currentItems[i].qty--;
          } else {
            currentItems.splice(i, 1);
          }
        }

        renderOrderItems();
      }
    });
  }

  // Save button
  const saveButton = document.getElementById('save-order-edit');
  if (saveButton) {
    saveButton.addEventListener('click', () => {
      const items = currentItems.map(i => ({
        name: i.name,
        group: i.group,
        price: i.price,
        qty: i.qty // ✅ ใส่ qty เพื่อให้ฝั่ง PHP คำนวณได้ถูกต้อง
      }));

      fetch('update_order_details.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ order_id: currentOrderId, items })
      })
      .then(async res => {
        const text = await res.text();
        try {
          const json = JSON.parse(text);
          if (json.success) {
            alert(json.message || 'อัปเดตออเดอร์เรียบร้อย');
            location.reload();
          } else {
            alert(json.error || 'Unknown error');
          }
        } catch (err) {
          console.error('⚠️ Invalid JSON from server:', text);
          alert('Server error: อาจมี error/warning ใน PHP ทำให้ JSON ผิดพลาด');
        }
      });
    });
  }

  // Cancel Order button
  const cancelOrderBtn = document.getElementById('cancel-order-btn');
  if (cancelOrderBtn) {
    cancelOrderBtn.addEventListener('click', function () {
      if (!currentOrderId) return;
      if (!confirm('Are you sure you want to cancel this order?')) return;

      fetch('update_order_status.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `order_id=${encodeURIComponent(currentOrderId)}&action=cancel`
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Order has been cancelled.');
            location.reload();
          } else {
            alert(data.error || 'Failed to cancel order.');
          }
        })
        .catch(() => alert('Failed to cancel order.'));
    });
  }

  // Close modal
  const closeButton = document.getElementById('close-modal');
  if (closeButton) {
    closeButton.onclick = () => {
      if (modal) {
        modal.classList.add('hidden');
      }
    };
  }
  
  fetchOrdersAndUpdate();
  setInterval(fetchOrdersAndUpdate, 5000);

  attachEventListeners();
});