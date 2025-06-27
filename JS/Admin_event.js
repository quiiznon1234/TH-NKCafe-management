window.addEventListener('DOMContentLoaded', () => {
            const table = document.getElementById('reservedEvent');
            const reservations = JSON.parse(localStorage.getItem('reservations')) || [];
        
            reservations.forEach((res, index) => {
                const row = table.insertRow(-1);
        
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${res.event}</td>
                    <td>${res.name}</td>
                    <td>${res.guests}</td>
                    <td>${res.date}</td>
                    <td>${res.time}</td>
                    <td><button onclick="editReservation(${index})">แก้ไข</button></td>
                    <td><button onclick="deleteReservation(${index})">ลบ</button></td>
                `;
            });
        });

        function editReservation(index) {
            const reservations = JSON.parse(localStorage.getItem('reservations')) || [];
            const res = reservations[index];

            const event = prompt("ประเภทกิจกรรม:", res.event);
            const name = prompt("ชื่อผู้จอง:", res.name);
            const guests = prompt("จำนวนแขก:", res.guests);
            const date = prompt("วันที่ (YYYY-MM-DD):", res.date);
            const time = prompt("เวลา:", res.time);

            if (event && name && guests && date && time) {
                reservations[index] = { event, name, guests, date, time };
                localStorage.setItem('reservations', JSON.stringify(reservations));
                location.reload();
            }
        }
        
        function deleteReservation(index) {
            let reservations = JSON.parse(localStorage.getItem('reservations')) || [];
            reservations.splice(index, 1); // ลบรายการที่เลือก
            localStorage.setItem('reservations', JSON.stringify(reservations));
            location.reload(); // refresh หน้า
        }