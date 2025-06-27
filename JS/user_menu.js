document.addEventListener('DOMContentLoaded', () => {
  // 1. Menu Switching
  const tabOrder = ['recommend', 'food', 'drink', 'dessert', 'alcohol'];
  const subcategories = {
    recommend: ["New", "Best Sale"],
    food: ["Pizza", "Pasta", "Burger", "Main"],
    drink: ["Coffee", "Milk", "Fruit", "Tea"],
    dessert: ["Cake", "Kaki", "Toast", "Crepe"],
    alcohol: ["Beer", "Wine"]
  };

  let currentMain = 'recommend';
  let currentSub = 'new';

  const mainTabs = document.querySelectorAll('.tab-button');
  const subcategoryTabs = document.getElementById('subcategoryTabs');
  const menuLists = document.querySelectorAll('.menu-list');

  function hideAllMenuLists() {
    menuLists.forEach(list => {
      list.classList.remove('active', 'slide-in-left', 'slide-in-right', 'slide-out-left', 'slide-out-right');
      list.classList.add('hidden');
    });
  }

  function renderSubTabs(mainCat) {
    subcategoryTabs.innerHTML = '';
    if (!subcategories[mainCat]) return;

    subcategories[mainCat].forEach((sub, i) => {
      const btn = document.createElement('button');
      btn.className = 'sub-tab-button' + (i === 0 ? ' active' : '');
      btn.dataset.sub = sub.toLowerCase();
      btn.textContent = sub;
      subcategoryTabs.appendChild(btn);
    });
  }

  function showMenu(main, sub, direction = 'right') {
    menuLists.forEach(list => {
      const isMatch = list.dataset.main === main && list.dataset.sub === sub;

      if (!isMatch) {
        list.classList.remove('active', 'slide-in-left', 'slide-in-right', 'slide-out-left', 'slide-out-right');
        list.classList.add('hidden');
      } else {
        list.classList.remove('active', 'hidden', 'slide-in-left', 'slide-in-right', 'slide-out-left', 'slide-out-right');
        list.classList.add(`slide-in-${direction}`);
        setTimeout(() => {
          list.classList.remove(`slide-in-${direction}`);
          list.classList.add('active');
        }, 200);
      }
    });
  }

  mainTabs.forEach(tab => {
    tab.addEventListener('click', () => {
      const newMain = tab.dataset.main;
      if (newMain === currentMain) return;

      const direction = tabOrder.indexOf(newMain) > tabOrder.indexOf(currentMain) ? 'right' : 'left';
      mainTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      renderSubTabs(newMain);
      const newSub = subcategories[newMain][0].toLowerCase();
      currentMain = newMain;
      currentSub = newSub;

      hideAllMenuLists();
      setTimeout(() => showMenu(newMain, newSub, direction), 300);
    });
  });

  subcategoryTabs.addEventListener('click', e => {
    if (!e.target.matches('.sub-tab-button')) return;

    subcategoryTabs.querySelectorAll('button').forEach(btn => btn.classList.remove('active'));
    e.target.classList.add('active');

    const mainCat = document.querySelector('.tab-button.active').dataset.main;
    const newSub = e.target.dataset.sub;

    const direction = subcategories[mainCat].findIndex(s => s.toLowerCase() === newSub) >
      subcategories[mainCat].findIndex(s => s.toLowerCase() === currentSub)
      ? 'right' : 'left';

    currentSub = newSub;
    showMenu(mainCat, newSub, direction);
  });

  renderSubTabs(currentMain);
  showMenu(currentMain, currentSub);

  // 2. Cart System
  const cart = [];
  const orderModal = document.getElementById('orderModal');
  const orderContent = document.getElementById('orderContent');
  const openOrderBtn = document.getElementById('openOrderBtn');
  const closeOrderBtn = document.querySelector('.close-order');

  openOrderBtn.addEventListener('click', () => {
    orderModal.style.display = 'block';
    renderCart();
  });

  closeOrderBtn.addEventListener('click', () => {
    orderModal.style.display = 'none';
  });

  window.addEventListener('click', e => {
    if (e.target === orderModal) {
      orderModal.style.display = 'none';
    }
  });

  document.addEventListener('click', e => {
    if (e.target.classList.contains('add-btn')) {
      const { name, price, group = '' } = e.target.dataset;
      cart.push({ name, price, group });
      renderCart();
    }

    if (e.target.classList.contains('qty-btn')) {
      const { name, group = '' } = e.target.dataset;
      const isIncrease = e.target.classList.contains('increase');

      const index = cart.findIndex(item => item.name === name && (item.group || '') === group);
      if (index !== -1) {
        if (isIncrease) {
          cart.push({ ...cart[index] });
        } else {
          cart.splice(index, 1);
        }
        renderCart();
      }
    }
  });

  function renderCart() {
    orderContent.innerHTML = '';
    if (cart.length === 0) {
      orderContent.innerHTML = '<p>No items in cart.</p>';
      return;
    }

    const grouped = {};
    cart.forEach(item => {
      const key = item.name + '|' + (item.group || '');
      if (!grouped[key]) grouped[key] = { ...item, qty: 1 };
      else grouped[key].qty++;
    });

    let total = 0;
    let index = 1;
    for (const key in grouped) {
      const item = grouped[key];
      const subtotal = item.qty * parseFloat(item.price);
      total += subtotal;

      const row = document.createElement('div');
      row.className = 'order-row';
      row.innerHTML = `
        <div class="order-name">${index++}. ${item.name} <span style="color:#888;font-size:0.9em;">(${item.group || '-'})</span></div>
        <div class="order-qty">
          <button class="qty-btn decrease" data-name="${item.name}" data-group="${item.group || ''}">-</button>
          <span>${item.qty}</span>
          <button class="qty-btn increase" data-name="${item.name}" data-group="${item.group || ''}">+</button>
        </div>
        <div class="order-subtotal">${subtotal.toFixed(2)} ฿</div>
        <textarea class="comment-box" placeholder="รายละเอียดเพิ่มเติม..." data-name="${item.name}" data-group="${item.group || ''}"></textarea>
      `;
      orderContent.appendChild(row);
    }

    const totalP = document.createElement('p');
    totalP.className = 'order-total';
    totalP.innerHTML = `<strong>Total: ${total.toFixed(2)} ฿</strong>`;
    orderContent.appendChild(totalP);

    const orderBtn = document.createElement('button');
    orderBtn.className = 'checkout-btn';
    orderBtn.textContent = 'สั่งออเดอร์';
    orderBtn.addEventListener('click', () => {
      if (cart.length === 0) return alert('Cart is empty.');
      
      document.querySelectorAll('.comment-box').forEach(textarea => {
        const name = textarea.dataset.name;
        const group = textarea.dataset.group;
        const comment = textarea.value.trim();

        const found = cart.find(item =>
          item.name === name && (item.group || '') === group
        );
        if (found) found.comment = comment;
      });

      // ✅ สร้าง payload พร้อม comment
      const payload = {
        customer: window.CURRENT_USERNAME || 'Guest',
        items: cart,
        table: window.TABLE_ID || ''
      };

      fetch('submit_order.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            alert('Order placed!');
            cart.length = 0;
            renderCart();
            orderModal.style.display = 'none';
          } else {
            alert(data.error || 'Failed to place order.');
          }
        })
        .catch(err => {
          console.error('Error placing order:', err);
          alert('Failed to place order.');
        });
    });

    orderContent.appendChild(orderBtn);
  }

  // 3. Order History Modal
  const historyModal = document.getElementById('historyModal');
  const openHistoryBtn = document.getElementById('orderHistoryBtn');
  const closeHistoryBtn = document.querySelector('.close-history');

  openHistoryBtn.addEventListener('click', () => {
    historyModal.style.display = 'block';
    // loadOrderHistory();
    
    fetch('user_history.php')
    .then(res => res.json())
    .then(data => {
      const historyContent = document.getElementById('historyContent');
      historyContent.innerHTML = '';

      const orders = data.orders || [];

      // ✅ แสดง alert ว่าออเดอร์ถูกยกเลิก แม้จะยังมีใบเสร็จอื่นอยู่
      if (data.cancelled) {
        alert('❌ ออเดอร์ของคุณถูกยกเลิกแล้ว');
      }

      if (orders.length === 0) {
        historyContent.innerHTML = '<p style="color: red;"> ไม่มีประวัติการสั่งซื้อ</p>';
        return;
      }

        orders.forEach(order => {
          const div = document.createElement('div');
          const status = order.status.trim().toLowerCase();
          let statusLabel = '';
          if (status === 'accepted') {
            statusLabel = '<span class="status-tag accepted">✔️ Accepted</span>';
          } else if (status === 'pending') {
            statusLabel = '<span class="status-tag pending">⏳ Pending</span>';
          } else if (status === 'checkout' || status === 'completed') {
            statusLabel = '<span class="status-tag completed">✅ Checkout</span>';
          } else if (status === 'cancelled') {
            statusLabel = '<span class="status-tag cancelled" style="background:#e74c3c;color:#fff;">❌ Cancelled</span>';
          } else {
            statusLabel = `<span class="status-tag unknown">❓ ${order.status}</span>`;
          }

        div.className = 'receipt';
        div.innerHTML = `
        <h4>Order #${order.id}</h4>
        <small>${order.created_at}</small>
        ${statusLabel}
        <hr>
        <pre>${order.details}</pre>
        <div class="total">Total: ${parseFloat(order.total).toFixed(2)} ฿</div>
      `;
        historyContent.appendChild(div);
      });
    })
    .catch(err => {
      console.error('Error loading history:', err);
      document.getElementById('historyContent').innerHTML =
        '<p style="color:red;">ไม่สามารถโหลดข้อมูลได้</p>';
    });

  });
  
  closeHistoryBtn.addEventListener('click', () => {
    historyModal.style.display = 'none';
  });

  window.addEventListener('click', e => {
    if (e.target === historyModal) {
      historyModal.style.display = 'none';
    }
  });

  document.querySelectorAll('.menu-list li, .drink-card').forEach(item => {
      const btn = item.querySelector('.add-btn');
      const isClosed = btn?.dataset.isClosed === "1";
      if (isClosed) {
          item.classList.add('menu-closed');
          btn.disabled = true;
          item.querySelector('.soldout-label').style.display = 'inline';
      }
  });
});