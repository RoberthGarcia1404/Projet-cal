// js/calendar.js

document.addEventListener('DOMContentLoaded', () => {
    const calendarEl = document.getElementById('calendar');
    const monthYearEl = document.getElementById('month-year');
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    const timeListEl = document.getElementById('time-list');
    const modal = document.getElementById('appointment-modal');
    const modalClose = document.getElementById('modal-close');
    const appointmentSummaryEl = document.getElementById('appointment-summary');
    const appointmentForm = document.getElementById('appointment-form');
    const appointmentDateInput = document.getElementById('appointment_date');
    const appointmentTimeInput = document.getElementById('appointment_time');
  
    let currentDate = new Date();
    let currentMonth = currentDate.getMonth();
    let currentYear = currentDate.getFullYear();
    let selectedDate = null;
    let selectedTime = null;
  
    function renderCalendar(month, year) {
      calendarEl.innerHTML = '';
      const monthNames = ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 
                          'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'];
      monthYearEl.textContent = `${monthNames[month]} ${year}`;
  
      let firstDay = new Date(year, month, 1).getDay();
      firstDay = (firstDay + 6) % 7; // Ajuste: lunes = 0
  
      const daysInMonth = new Date(year, month + 1, 0).getDate();
  
      // Celdas vacías antes del primer día
      for (let i = 0; i < firstDay; i++) {
        const emptyCell = document.createElement('div');
        emptyCell.classList.add('empty-cell');
        calendarEl.appendChild(emptyCell);
      }
  
      for (let day = 1; day <= daysInMonth; day++) {
        const cell = document.createElement('div');
        cell.textContent = day;
        cell.classList.add('calendar-day');
        cell.addEventListener('click', () => {
          document.querySelectorAll('.calendar-day').forEach(el => el.classList.remove('selected'));
          cell.classList.add('selected');
          selectedDate = new Date(year, month, day);
          renderTimeSlots(selectedDate);
        });
        calendarEl.appendChild(cell);
      }
    }
  
    function renderTimeSlots(date) {
      timeListEl.innerHTML = '';
      const dayOfWeek = date.getDay() === 0 ? 7 : date.getDay();
      if (!workingHours[dayOfWeek]) {
        timeListEl.innerHTML = `<p>No hay horarios disponibles para este día.</p>`;
        return;
      }
      const work = workingHours[dayOfWeek];
      const [startHour, startMin] = work.start_time.split(':').map(Number);
      const [endHour, endMin] = work.end_time.split(':').map(Number);
      const appointmentDuration = work.appointment_duration;
  
      const startTime = new Date(date);
      startTime.setHours(startHour, startMin, 0, 0);
      const endTime = new Date(date);
      endTime.setHours(endHour, endMin, 0, 0);
  
      let slots = [];
      for (let time = new Date(startTime); time < endTime; time.setMinutes(time.getMinutes() + appointmentDuration)) {
        if (time.getTime() + appointmentDuration * 60000 <= endTime.getTime()) {
          slots.push(new Date(time));
        }
      }
  
      // Filtrar turnos ya reservados
      const selectedDateStr = date.getFullYear() + "-" +
        String(date.getMonth() + 1).padStart(2, "0") + "-" +
        String(date.getDate()).padStart(2, "0");
  
      slots = slots.filter(slot => {
        const hrs = slot.getHours().toString().padStart(2, '0');
        const mins = slot.getMinutes().toString().padStart(2, '0');
        const slotTime = `${hrs}:${mins}`;
        const isBooked = bookedAppointments.some(app => {
          // Compara la fecha y la hora (solo HH:MM)
          return app.appointment_date === selectedDateStr && app.appointment_time.substring(0,5) === slotTime;
        });
        return !isBooked;
      });
  
      if (slots.length === 0) {
        timeListEl.innerHTML = `<p>No hay horarios disponibles para este día.</p>`;
        return;
      }
  
      const header = document.createElement('h3');
      header.textContent = `Horarios para el día ${date.getDate()}:`;
      timeListEl.appendChild(header);
  
      slots.forEach(slot => {
        const btn = document.createElement('button');
        const hrs = slot.getHours().toString().padStart(2, '0');
        const mins = slot.getMinutes().toString().padStart(2, '0');
        btn.textContent = `${hrs}:${mins}`;
        btn.addEventListener('click', () => {
          selectedTime = btn.textContent;
          openModal(date, selectedTime);
        });
        timeListEl.appendChild(btn);
      });
    }
  
    function openModal(date, time) {
      const day = date.getDate();
      appointmentSummaryEl.textContent = `Has seleccionado el ${day} de ${monthYearEl.textContent} a las ${time}.`;
      const year = date.getFullYear();
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const dayStr = day.toString().padStart(2, '0');
      appointmentDateInput.value = `${year}-${month}-${dayStr}`;
      appointmentTimeInput.value = time;
      modal.classList.remove('hidden');
    }
  
    prevBtn.addEventListener('click', () => {
      currentMonth--;
      if (currentMonth < 0) { currentMonth = 11; currentYear--; }
      renderCalendar(currentMonth, currentYear);
      timeListEl.innerHTML = `<p>Selecciona un día para ver horarios</p>`;
    });
  
    nextBtn.addEventListener('click', () => {
      currentMonth++;
      if (currentMonth > 11) { currentMonth = 0; currentYear++; }
      renderCalendar(currentMonth, currentYear);
      timeListEl.innerHTML = `<p>Selecciona un día para ver horarios</p>`;
    });
  
    modalClose.addEventListener('click', () => {
      modal.classList.add('hidden');
    });
  
    renderCalendar(currentMonth, currentYear);
  });
  