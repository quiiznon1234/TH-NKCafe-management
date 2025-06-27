document.addEventListener('DOMContentLoaded', () => {
    const {
        tableData,
        incomeData,
        topFoods,
        topDrinks,
        topDesserts,
        topAlcohols
    } = window.reportData;

    const {
        Foods,
        Drinks,
        Desserts,
        Alcohols
    } = window.allTimeTopData;

    const charts = {};

    // Switch Tab
    document.querySelectorAll('.tab-button').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-button').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            const targetId = btn.dataset.target;
            document.querySelectorAll('.section-content').forEach(sec => sec.classList.add('hidden'));
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.remove('hidden');
                initializeCharts(targetId);
            }
        });
    });

    window.toggleMonthDetails = function (id) {
        const row = document.getElementById(id);
        row.style.display = row.style.display === 'none' ? '' : 'none';
    };

    window.toggleYear = function (year) {
        document.querySelectorAll(`[data-year="${year}"].month-row`).forEach(row => row.classList.toggle('hidden'));
    };

    window.toggleMonth = function (year, month) {
        document.querySelectorAll(`[data-year="${year}"][data-month="${month}"].day-row`)
            .forEach(row => row.classList.toggle('hidden'));
    };

    function initializeCharts(sectionId) {
        switch (sectionId) {
            case 'tableSection':
                if (!charts.tableChart) {
                    const countsByDate = {};
                    tableData.forEach(r => {
                        countsByDate[r.date] = (countsByDate[r.date] ?? 0) + 1;
                    });
                    const labels = Object.keys(countsByDate);
                    const data = Object.values(countsByDate);
                    const ctx = document.getElementById('tableChart');
                    if (ctx) {
                        charts.tableChart = new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'จำนวนการจอง',
                                    data,
                                    borderColor: '#3498db',
                                    backgroundColor: 'rgba(52,152,219,0.1)',
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' } },
                                    x: { grid: { color: 'rgba(0,0,0,0.1)' } }
                                }
                            }
                        });
                    }
                }
                break;

            case 'incomeSection':
                if (!charts.incomeChart) {
                    const labels = [], data = [];
                    for (const year in incomeData) {
                        for (const month in incomeData[year].months) {
                            labels.push(`${year}-${month}`);
                            data.push(incomeData[year].months[month].total);
                        }
                    }
                    const ctx = document.getElementById('incomeChart');
                    if (ctx) {
                        charts.incomeChart = new Chart(ctx, {
                            type: 'bar',
                            data: {
                                labels,
                                datasets: [{
                                    label: 'รายได้ (บาท)',
                                    data,
                                    backgroundColor: 'rgba(52, 152, 219, 0.8)',
                                    borderRadius: 10,
                                    borderSkipped: false
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: { legend: { display: false } },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            callback: val => val.toLocaleString() + ' ฿'
                                        },
                                        grid: { color: 'rgba(0,0,0,0.1)' }
                                    },
                                    x: { grid: { display: false } }
                                }
                            }
                        });
                    }
                }
                break;

            case 'toptenSection':
                renderPopularChart('food'); // เรียกตอนแรก
                break;
        }
    }

    // ✅ ทำให้ฟังก์ชันเรียกจาก onclick ได้
window.renderPopularChart = function (type = 'food', customData = null) {
    const ctx = document.getElementById('popularChart');
    const title = document.getElementById('popularChartTitle');
    if (!ctx) return;

    // ป้องกัน error ถ้าไม่มี monthFilter
    const monthFilterElem = document.getElementById('monthFilter');
    const selectedMonth = monthFilterElem ? monthFilterElem.value : 'all';

    // ป้องกัน datasets undefined
    let datasets = customData;
    if (!datasets) {
        if (selectedMonth === 'all') {
            datasets = window.allTimeTopData;
        } else {
            datasets = window.monthlyTopData && window.monthlyTopData[selectedMonth] ? window.monthlyTopData[selectedMonth] : {};
        }
    }
    // ถ้า datasets เป็น undefined ให้ fallback เป็น object เปล่า
    if (!datasets) datasets = {};

    const labelsMap = {
        food: 'อาหาร',
        drink: 'เครื่องดื่ม',
        dessert: 'ของหวาน',
        alcohol: 'เครื่องดื่มแอลกอฮอล์'
    };

    const colors = {
        food: '#3498db',
        drink: '#2ecc71',
        dessert: '#e67e22',
        alcohol: '#9b59b6'
    };

    // ป้องกัน error ถ้า datasets ไม่มี property type
    const categoryData = (datasets && datasets[type]) ? datasets[type] : {};
    const labels = Object.keys(categoryData);
    const data = Object.values(categoryData);

    // เปลี่ยนหัวข้อกราฟ
    if (title && labelsMap[type]) {
        let period = 'เดือนนี้';
        if (monthFilterElem && selectedMonth !== 'all') {
            const date = selectedMonth.split('-');
            const thaiMonths = [
                '', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            const monthName = thaiMonths[parseInt(date[1])];
            const yearThai = parseInt(date[0]) + 543;
            period = `${monthName} ${yearThai}`;
        }
        title.textContent = `📈 กราฟรวมเมนู${labelsMap[type]}ยอดนิยม (${period})`;
    }

    if (window.charts && window.charts.popularChart) {
        window.charts.popularChart.destroy();
    }
    if (!window.charts) window.charts = {};

    window.charts.popularChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels,
            datasets: [{
                label: `จำนวนออเดอร์ (${labelsMap[type]})`,
                data,
                backgroundColor: colors[type],
                borderRadius: 10,
                borderSkipped: false
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.1)' } },
                x: { grid: { display: false } }
            }
        }
    });
};
    window.toggleOrderDayDetails = function (id, event) {
    event.stopPropagation();
    const row = document.getElementById('order-day-' + id);
    if (row) {
        row.classList.toggle('show');
    }
};

    // โหลดกราฟแรกเมื่อเข้าเพจ
    initializeCharts('tableSection');
});