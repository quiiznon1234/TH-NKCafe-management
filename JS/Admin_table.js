window.addEventListener('DOMContentLoaded', () => {
            const table = document.getElementById('reservedTable');
            const reservations = JSON.parse(localStorage.getItem('tableReservations')) || [];
        
            reservations.forEach((res, index) => {
                const row = table.insertRow(-1);
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${res.name}</td>
                    <td>${res.seats}</td>
                    <td>${res.date}</td>
                    <td>${res.time}</td>
                    <td><button onclick="editReservation(${index})">แก้ไข</button></td>
                    <td><button onclick="deleteReservation(${index})">ลบ</button></td>
                `;
            });
        });

        function editReservation(index) {
            const reservations = JSON.parse(localStorage.getItem('tableReservations')) || [];
            const res = reservations[index];
    
            const name = prompt("ชื่อ:", res.name);
            const seats = prompt("จำนวนที่นั่ง:", res.seats);
            const date = prompt("วันที่ (YYYY-MM-DD):", res.date);
            const time = prompt("เวลา:", res.time);
    
            if (name && seats && date && time) {
                reservations[index] = { name, seats, date, time };
                localStorage.setItem('tableReservations', JSON.stringify(reservations));
                location.reload();
            }
        }

        function deleteReservation(index) {
            let reservations = JSON.parse(localStorage.getItem('tableReservations')) || [];
            reservations.splice(index, 1);
            localStorage.setItem('tableReservations', JSON.stringify(reservations));
            location.reload();
        }