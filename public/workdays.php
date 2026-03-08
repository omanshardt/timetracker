<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Work Days Administration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Apply theme immediately to prevent flash of light mode
        (function () {
            var theme = localStorage.getItem('timetracker_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="css/style.css">
</head>

<body class="font-sans">

    <div class="container mx-auto px-4 py-8 max-w-5xl">

        <!-- Header -->
        <header class="bg-white rounded-lg shadow p-6 mb-8 flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-0">Work Days</h1>
                <p class="text-gray-500 text-sm">Manage required work days and hours.</p>
            </div>

            <div class="flex flex-wrap items-end gap-4">
                <!-- Theme Toggle Button -->
                <div class="flex flex-col">
                    <button id="theme-toggle"
                        class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 p-2 rounded-lg transition-colors flex items-center gap-2"
                        aria-label="Toggle dark mode">
                        <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 hidden" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707.707M7.757 6.343l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                        </svg>
                        <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>

                <!-- Back Link -->
                <a href="index.php"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded shadow transition-colors flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                            clip-rule="evenodd" />
                    </svg>
                    Back
                </a>
            </div>
        </header>

        <!-- Month Navigation -->
        <div class="flex items-center justify-between mb-6">
            <button id="btn-prev-month"
                class="bg-white hover:bg-gray-100 text-gray-800 font-bold py-2 px-4 rounded shadow transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>
            <h2 id="month-display" class="text-2xl font-semibold text-gray-800"></h2>
            <button id="btn-next-month"
                class="bg-white hover:bg-gray-100 text-gray-800 font-bold py-2 px-4 rounded shadow transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        </div>

        <!-- Generate Button -->
        <div class="mb-6 flex gap-4">
            <button id="btn-generate"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition-colors flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z"
                        clip-rule="evenodd" />
                </svg>
                Generate Mon–Thu (8h)
            </button>
        </div>

        <!-- Calendar Grid -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Mon</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tue</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Wed</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Thu</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Fri</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sat</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sun</th>
                        </tr>
                    </thead>
                    <tbody id="calendar-body" class="bg-white divide-y divide-gray-200">
                        <!-- Rendered by JS -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Monthly Summary -->
        <div id="month-summary"
            class="bg-blue-600 rounded-lg shadow-md p-6 mb-10 text-white flex justify-between items-center">
            <div>
                <span class="text-blue-100 text-sm font-semibold uppercase tracking-wider">Monthly Summary</span>
                <p class="text-lg font-bold" id="summary-text">--</p>
            </div>
            <div class="bg-blue-500 rounded-full p-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-100" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

    </div>

    <!-- Add/Edit Modal -->
    <div id="workday-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden p-4"
        style="z-index: 50;">
        <div class="relative top-20 mx-auto p-8 border max-w-md w-full shadow-2xl rounded-lg bg-white">
            <div class="text-left">
                <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="workday-modal-title">Add Work Day</h3>
                <div class="mt-2">
                    <form id="workday-form">
                        <input type="hidden" id="wd-modal-id">
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="wd-modal-date">Date</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="wd-modal-date" type="date" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="wd-modal-time">Required Time
                                (HH:MM)</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="wd-modal-time" type="time" value="08:00" required>
                        </div>
                        <div class="flex items-center justify-between">
                            <button id="btn-wd-cancel" type="button"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="delete-modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 flex flex-col items-center">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10 mb-4">
                        <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="delete-modal-title">Delete Work Day</h3>
                    <div class="mt-2 text-center">
                        <p class="text-sm text-gray-500">Are you sure you want to delete this work day entry? This
                            action cannot be undone.</p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="btn-confirm-delete"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" id="btn-cancel-delete"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // State
            let currentYear = new Date().getFullYear();
            let currentMonth = new Date().getMonth(); // 0-based
            let workDaysData = [];
            let deleteTargetId = null;

            // Theme
            const storedTheme = localStorage.getItem('timetracker_theme') || 'light';
            let currentTheme = storedTheme;
            const themeToggleBtn = document.getElementById('theme-toggle');
            const sunIcon = document.getElementById('sun-icon');
            const moonIcon = document.getElementById('moon-icon');

            function applyTheme(theme) {
                currentTheme = theme;
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('timetracker_theme', theme);
                if (theme === 'dark') {
                    sunIcon.classList.remove('hidden');
                    moonIcon.classList.add('hidden');
                } else {
                    sunIcon.classList.add('hidden');
                    moonIcon.classList.remove('hidden');
                }
            }
            applyTheme(currentTheme);
            if (themeToggleBtn) {
                themeToggleBtn.addEventListener('click', () => {
                    applyTheme(currentTheme === 'light' ? 'dark' : 'light');
                });
            }

            // Elements
            const monthDisplay = document.getElementById('month-display');
            const calendarBody = document.getElementById('calendar-body');
            const summaryText = document.getElementById('summary-text');
            const workdayModal = document.getElementById('workday-modal');
            const workdayForm = document.getElementById('workday-form');
            const wdModalId = document.getElementById('wd-modal-id');
            const wdModalDate = document.getElementById('wd-modal-date');
            const wdModalTime = document.getElementById('wd-modal-time');
            const wdModalTitle = document.getElementById('workday-modal-title');
            const deleteConfirmModal = document.getElementById('delete-confirm-modal');

            // Month names
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'];

            function getMonthStr() {
                return `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}`;
            }

            function updateMonthDisplay() {
                monthDisplay.textContent = `${monthNames[currentMonth]} ${currentYear}`;
            }

            // Navigation
            document.getElementById('btn-prev-month').addEventListener('click', () => {
                currentMonth--;
                if (currentMonth < 0) { currentMonth = 11; currentYear--; }
                loadMonth();
            });
            document.getElementById('btn-next-month').addEventListener('click', () => {
                currentMonth++;
                if (currentMonth > 11) { currentMonth = 0; currentYear++; }
                loadMonth();
            });

            // Load month data
            async function loadMonth() {
                updateMonthDisplay();
                try {
                    const res = await fetch(`api/get_workdays.php?month=${getMonthStr()}`);
                    const json = await res.json();
                    workDaysData = json.data || [];
                    renderCalendar();
                } catch (err) {
                    console.error('Failed to load work days:', err);
                }
            }

            // Render calendar
            function renderCalendar() {
                calendarBody.innerHTML = '';

                const firstDay = new Date(currentYear, currentMonth, 1);
                const lastDay = new Date(currentYear, currentMonth + 1, 0);
                const totalDays = lastDay.getDate();

                // Day of week for first day (1=Mon, 7=Sun in ISO)
                let startDow = firstDay.getDay(); // 0=Sun
                startDow = startDow === 0 ? 7 : startDow; // Convert to 1=Mon

                // Index workdays by date
                const wdMap = {};
                let totalRequiredMin = 0;
                workDaysData.forEach(wd => {
                    wdMap[wd.work_date] = wd;
                    const parts = wd.required_time.split(':');
                    totalRequiredMin += parseInt(parts[0]) * 60 + parseInt(parts[1]);
                });

                // Summary
                const totalHours = Math.floor(totalRequiredMin / 60);
                const totalMins = totalRequiredMin % 60;
                summaryText.textContent = `${workDaysData.length} work days, ${totalHours}h ${totalMins > 0 ? totalMins + 'm' : ''} total required`;

                let row = document.createElement('tr');
                // Empty cells before first day
                for (let i = 1; i < startDow; i++) {
                    const td = document.createElement('td');
                    td.className = 'px-2 py-3 text-center border border-gray-100';
                    row.appendChild(td);
                }

                for (let day = 1; day <= totalDays; day++) {
                    const dateStr = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const date = new Date(currentYear, currentMonth, day);
                    let dow = date.getDay();
                    dow = dow === 0 ? 7 : dow;

                    const td = document.createElement('td');
                    td.className = 'px-2 py-3 text-center border border-gray-100 align-top';
                    td.style.minWidth = '100px';
                    td.style.height = '80px';

                    const dayNum = document.createElement('div');
                    dayNum.className = 'text-sm font-medium text-gray-600 mb-1';
                    dayNum.textContent = day;
                    td.appendChild(dayNum);

                    const wd = wdMap[dateStr];

                    if (wd) {
                        // Has a work_day entry
                        td.style.backgroundColor = currentTheme === 'dark' ? 'rgba(59, 130, 246, 0.15)' : '#eff6ff';

                        const timeLabel = document.createElement('div');
                        const timeParts = wd.required_time.split(':');
                        timeLabel.className = 'text-sm font-bold text-blue-700 mb-1';
                        timeLabel.textContent = `${timeParts[0]}:${timeParts[1]}`;
                        td.appendChild(timeLabel);

                        const actions = document.createElement('div');
                        actions.className = 'flex justify-center gap-1';

                        // Edit button
                        const editBtn = document.createElement('button');
                        editBtn.className = 'text-blue-500 hover:text-blue-700 p-1 transition-colors';
                        editBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>';
                        editBtn.title = 'Edit';
                        editBtn.addEventListener('click', () => openEditModal(wd));
                        actions.appendChild(editBtn);

                        // Delete button
                        const delBtn = document.createElement('button');
                        delBtn.className = 'text-red-500 hover:text-red-700 p-1 transition-colors';
                        delBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>';
                        delBtn.title = 'Delete';
                        delBtn.addEventListener('click', () => openDeleteModal(wd.id));
                        actions.appendChild(delBtn);

                        td.appendChild(actions);
                    } else {
                        // No entry — show add button
                        const addBtn = document.createElement('button');
                        addBtn.className = 'text-gray-300 hover:text-blue-500 p-1 transition-colors mt-1';
                        addBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>';
                        addBtn.title = 'Add work day';
                        addBtn.addEventListener('click', () => openAddModal(dateStr));
                        td.appendChild(addBtn);
                    }

                    // Weekend styling
                    if (dow >= 6) {
                        td.style.backgroundColor = currentTheme === 'dark' ? 'rgba(255,255,255,0.03)' : '#f9fafb';
                        dayNum.className = 'text-sm font-medium text-gray-400 mb-1';
                    }

                    row.appendChild(td);

                    // End of week row
                    if (dow === 7) {
                        calendarBody.appendChild(row);
                        row = document.createElement('tr');
                    }
                }

                // Fill remaining cells in last row
                const lastDow = lastDay.getDay();
                const lastDowIso = lastDow === 0 ? 7 : lastDow;
                if (lastDowIso < 7) {
                    for (let i = lastDowIso + 1; i <= 7; i++) {
                        const td = document.createElement('td');
                        td.className = 'px-2 py-3 text-center border border-gray-100';
                        row.appendChild(td);
                    }
                    calendarBody.appendChild(row);
                }
            }

            // Add Modal
            function openAddModal(dateStr) {
                wdModalId.value = '';
                wdModalDate.value = dateStr;
                wdModalTime.value = '08:00';
                wdModalTitle.textContent = 'Add Work Day';
                workdayModal.classList.remove('hidden');
            }

            // Edit Modal
            function openEditModal(wd) {
                wdModalId.value = wd.id;
                wdModalDate.value = wd.work_date;
                const parts = wd.required_time.split(':');
                wdModalTime.value = `${parts[0]}:${parts[1]}`;
                wdModalTitle.textContent = 'Edit Work Day';
                workdayModal.classList.remove('hidden');
            }

            // Delete Modal
            function openDeleteModal(id) {
                deleteTargetId = id;
                deleteConfirmModal.classList.remove('hidden');
            }

            // Cancel modals
            document.getElementById('btn-wd-cancel').addEventListener('click', () => {
                workdayModal.classList.add('hidden');
                workdayForm.reset();
            });
            document.getElementById('btn-cancel-delete').addEventListener('click', () => {
                deleteConfirmModal.classList.add('hidden');
                deleteTargetId = null;
            });

            // Close modals on outside click
            workdayModal.addEventListener('click', (e) => {
                if (e.target === workdayModal) {
                    workdayModal.classList.add('hidden');
                    workdayForm.reset();
                }
            });

            // Save work day
            workdayForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const id = wdModalId.value;
                const payload = {
                    work_date: wdModalDate.value,
                    required_time: wdModalTime.value,
                };

                const endpoint = id ? 'api/update_workday.php' : 'api/add_workday.php';
                if (id) payload.id = id;

                try {
                    const res = await fetch(endpoint, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify(payload)
                    });
                    const data = await res.json();
                    if (data.success) {
                        workdayModal.classList.add('hidden');
                        workdayForm.reset();
                        loadMonth();
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Connection error while saving.');
                }
            });

            // Confirm delete
            document.getElementById('btn-confirm-delete').addEventListener('click', async () => {
                if (!deleteTargetId) return;
                try {
                    const res = await fetch('api/delete_workday.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: deleteTargetId })
                    });
                    const data = await res.json();
                    if (data.success) {
                        deleteConfirmModal.classList.add('hidden');
                        deleteTargetId = null;
                        loadMonth();
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Connection error while deleting.');
                }
            });

            // Generate button
            document.getElementById('btn-generate').addEventListener('click', async () => {
                const monthStr = getMonthStr();
                if (!confirm(`Generate Mon–Thu work days (8h each) for ${monthNames[currentMonth]} ${currentYear}?`)) return;

                try {
                    const res = await fetch('api/generate_workdays.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ month: monthStr })
                    });
                    const data = await res.json();
                    if (data.success) {
                        alert(`Done! Inserted: ${data.inserted}, Skipped (already exist): ${data.skipped}`);
                        loadMonth();
                    } else {
                        alert('Error: ' + (data.error || 'Unknown error'));
                    }
                } catch (err) {
                    console.error(err);
                    alert('Connection error while generating.');
                }
            });

            // Initial load
            loadMonth();
        });
    </script>

</body>

</html>