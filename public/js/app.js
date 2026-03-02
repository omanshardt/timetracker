// public/js/app.js

document.addEventListener('DOMContentLoaded', () => {
    const datePicker = document.getElementById('date-picker');
    const typeSelector = document.getElementById('type-selector');
    const headline = document.getElementById('day-headline');
    const totalDurationEl = document.getElementById('total-duration');
    const tableFooterSumEl = document.getElementById('table-detailed-footer-sum');

    // State
    const storedType = localStorage.getItem('timetracker_type');
    const storedDate = localStorage.getItem('timetracker_date');
    const today = new Date().toISOString().split('T')[0];

    let currentData = null;
    let currentType = storedType || 'real'; // 'real' or 'tracking'
    let currentDate = storedDate || today;

    // Modal Elements
    const btnOpenModal = document.getElementById('btn-open-add-modal');
    const btnCancelModal = document.getElementById('btn-cancel-modal');
    const addEntryModal = document.getElementById('add-entry-modal');
    const addEntryForm = document.getElementById('add-entry-form');
    const modalReportingDate = document.getElementById('modal-reporting-date');

    // Hidden Fields
    const modalStartReported = document.getElementById('modal-start-reported');
    const modalEndReported = document.getElementById('modal-end-reported');
    const modalTaskName = document.getElementById('modal-task-name');
    const modalDescriptionLong = document.getElementById('modal-description-long');
    const modalTransfer = document.getElementById('modal-transfer');
    const modalTransferIntern = document.getElementById('modal-transfer-intern');
    const modalTransferJira = document.getElementById('modal-transfer-jira');

    // Inline Edit Elements
    const inlineEditModal = document.getElementById('inline-edit-modal');
    const btnInlineCancel = document.getElementById('btn-inline-cancel');
    const btnInlineSave = document.getElementById('btn-inline-save');
    const inlineEditTitle = document.getElementById('inline-edit-title');
    let inlineEditState = { id: null, field: null, triggerBtn: null };

    // Init
    typeSelector.value = currentType;
    datePicker.value = currentDate;
    fetchData(currentDate);

    // Modal Events
    if (btnOpenModal) {
        btnOpenModal.addEventListener('click', () => {
            addEntryForm.reset(); // Reset form first

            // Clear hidden fields manually for safety
            modalStartReported.value = '';
            modalEndReported.value = '';
            modalTaskName.value = '';
            modalDescriptionLong.value = '';
            modalTransfer.value = '';
            modalTransferIntern.value = '';
            modalTransferJira.value = '';

            modalReportingDate.value = today; // Prefill today's date
            addEntryModal.classList.remove('hidden');
        });
    }

    if (btnCancelModal) {
        btnCancelModal.addEventListener('click', () => {
            addEntryModal.classList.add('hidden');
            addEntryForm.reset();
        });
    }

    // Close modals on outside click
    window.addEventListener('click', (e) => {
        if (e.target === addEntryModal) {
            addEntryModal.classList.add('hidden');
            addEntryForm.reset();
        }

        // If clicking outside inline modal AND not clicking a trigger button, close it
        if (inlineEditModal && !inlineEditModal.classList.contains('hidden') && !inlineEditModal.contains(e.target)) {
            // Check if what we clicked is one of our trigger buttons
            const isTrigger = e.target.closest('.inline-edit-trigger');
            if (!isTrigger) {
                closeInlineModal();
            }
        }
    });

    // Inline Edit Logic
    function closeInlineModal() {
        inlineEditModal.classList.add('hidden');
        inlineEditState = { id: null, field: null, triggerBtn: null };
    }

    if (btnInlineCancel) {
        btnInlineCancel.addEventListener('click', closeInlineModal);
    }

    if (btnInlineSave) {
        btnInlineSave.addEventListener('click', async () => {
            const selectedRadio = document.querySelector('input[name="inline-edit-value"]:checked');
            if (!selectedRadio || !inlineEditState.id) return;

            const value = selectedRadio.value;
            const payload = {
                id: inlineEditState.id,
                field: inlineEditState.field,
                value: value
            };

            const originalText = inlineEditState.triggerBtn.innerText;
            inlineEditState.triggerBtn.innerText = '...'; // loading state

            try {
                const res = await fetch('api/update_transfer_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();
                if (data.success) {
                    closeInlineModal();
                    fetchData(currentDate); // Refresh completely to recalculate downstream tables
                } else {
                    inlineEditState.triggerBtn.innerText = originalText;
                    alert('Error updating status: ' + (data.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                inlineEditState.triggerBtn.innerText = originalText;
                alert('Connection error while saving.');
            }
        });
    }

    if (addEntryForm) {
        addEntryForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const payload = {
                reporting_date: document.getElementById('modal-reporting-date').value,
                start_time: document.getElementById('modal-start-time').value,
                end_time: document.getElementById('modal-end-time').value,
                task_id: document.getElementById('modal-task-id').value,
                description: document.getElementById('modal-description').value
            };

            // Add hidden fields if they are set (from Copy action)
            if (modalStartReported.value !== '') payload.start_time_reported = modalStartReported.value;
            if (modalEndReported.value !== '') payload.end_time_reported = modalEndReported.value;
            if (modalTaskName.value !== '') payload.task_name = modalTaskName.value;
            if (modalDescriptionLong.value !== '') payload.description_long = modalDescriptionLong.value;
            if (modalTransfer.value !== '') payload.transfer = modalTransfer.value;
            if (modalTransferIntern.value !== '') payload.transfered_intern = modalTransferIntern.value;
            if (modalTransferJira.value !== '') payload.transfered_jira = modalTransferJira.value;

            try {
                const res = await fetch('api/add_entry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                });

                const data = await res.json();

                if (data.success) {
                    addEntryModal.classList.add('hidden');
                    addEntryForm.reset();
                    fetchData(currentDate); // Refresh data
                } else {
                    alert('Error saving entry: ' + (data.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Connection error while saving entry.');
            }
        });
    }

    // Events
    datePicker.addEventListener('change', (e) => {
        currentDate = e.target.value;
        localStorage.setItem('timetracker_date', currentDate);
        fetchData(currentDate);
    });

    typeSelector.addEventListener('change', (e) => {
        currentType = e.target.value;
        localStorage.setItem('timetracker_type', currentType);
        renderAll();
    });

    function fetchData(date) {
        headline.innerText = `Loading data for ${date}...`;
        fetch(`api/get_day_data.php?date=${date}`)
            .then(res => res.json())
            .then(data => {
                currentData = data;

                // Format headline date
                const d = new Date(date);
                const options = { weekday: 'long', year: 'numeric', month: '2-digit', day: '2-digit' };
                headline.innerText = d.toLocaleDateString('de-DE', options);

                renderAll();
            })
            .catch(err => {
                console.error(err);
                headline.innerText = "Error loading data";
            });
    }

    function renderAll() {
        if (!currentData) return;
        renderDetailed();
        renderConsecutive();
        renderGrouped();
        updateSummary();
    }

    function updateSummary() {
        // Summary row logic from API
        const sum = currentData.summary[currentType];
        const txt = sum || "00:00";
        totalDurationEl.innerText = txt;
        if (tableFooterSumEl) {
            tableFooterSumEl.innerText = txt;
        }
    }

    function getStatusColor(status) {
        if (status === 'green') return 'bg-green-100 text-green-800';
        if (status === 'yellow') return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    }

    function renderDetailed() {
        const tbody = document.getElementById('table-detailed-body');
        tbody.innerHTML = '';

        currentData.raw.forEach((row, index) => {
            const tr = document.createElement('tr');

            const isTransfer0 = (row.transfer == 0);
            const isPause = (row.task_id && row.task_id.includes('PAUSE'));

            // Dimming requirements: transfer=0 OR PAUSE
            if (isTransfer0 || isPause) {
                tr.classList.add('opacity-50');
            }

            // Columns depend on type
            let start, end, duration, gapPrev, gapNext;

            if (currentType === 'real') {
                start = row.start_time;
                end = row.end_time;
                duration = row.duration_real_formatted;
                gapPrev = row.gap_real_prev;
                gapNext = row.gap_real_next;
            } else {
                start = row.start_time_reported;
                end = row.end_time_reported;
                duration = row.duration_tracking_formatted;
                gapPrev = row.gap_tracking_prev;
                gapNext = row.gap_tracking_next;
            }

            // Cell Styles for Gap/Overlap
            const startClass = gapPrev ? 'bg-red-100 text-red-900' : '';
            const endClass = gapNext ? 'bg-red-100 text-red-900' : '';

            const startTitle = gapPrev ? `Previous ended before/after start (${gapPrev})` : '';
            const endTitle = gapNext ? `Next starts after/before end (${gapNext})` : '';

            // Using buttons instead of text
            const transStatusText = row.transfer == 1 ? 'Yes' : 'No';
            const intStatusText = row.transfered_intern == 1 ? 'Yes' : 'No';
            const jiraStatusText = row.transfered_jira == 1 ? 'Yes' : 'No';

            // Note: We only allow inline edit if the row exists in DB (has id)
            const btnTrans = `<button class="inline-edit-trigger text-blue-600 hover:text-blue-800 underline" data-id="${row.id}" data-field="transfer" data-val="${row.transfer}">${transStatusText}</button>`;
            const btnInt = `<button class="inline-edit-trigger text-blue-600 hover:text-blue-800 underline" data-id="${row.id}" data-field="transfered_intern" data-val="${row.transfered_intern}">${intStatusText}</button>`;
            const btnJira = `<button class="inline-edit-trigger text-blue-600 hover:text-blue-800 underline" data-id="${row.id}" data-field="transfered_jira" data-val="${row.transfered_jira}">${jiraStatusText}</button>`;

            tr.innerHTML = `
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono ${startClass}" title="${startTitle}">
                    ${start ? start.substring(0, 5) : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono ${endClass}" title="${endTitle}">
                    ${end ? end.substring(0, 5) : '-'}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                    ${row.task_id || ''}
                </td>
                <td class="px-6 py-4 text-sm text-gray-500">
                    ${row.description || ''}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                    ${duration}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    ${btnTrans}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    ${btnInt}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                    ${btnJira}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                    <button class="btn-copy text-indigo-600 hover:text-indigo-900" data-index="${index}">Copy</button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Attach click events to Copy buttons
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                addEntryForm.reset();

                const index = parseInt(btn.getAttribute('data-index'), 10);
                const row = currentData.raw[index];

                // Visible fields
                modalReportingDate.value = today; // Forced to today
                document.getElementById('modal-start-time').value = row.start_time;
                document.getElementById('modal-end-time').value = row.end_time;
                document.getElementById('modal-task-id').value = row.task_id || '';
                document.getElementById('modal-description').value = row.description || '';

                // Hidden fields
                modalStartReported.value = row.start_time_reported || '';
                modalEndReported.value = row.end_time_reported || '';
                modalTaskName.value = row.task_name || '';
                modalDescriptionLong.value = row.description_long || '';

                // Forced overrides for transfer status
                modalTransfer.value = row.transfer !== undefined && row.transfer !== null ? row.transfer : '1';
                modalTransferIntern.value = '0';
                modalTransferJira.value = '0';

                addEntryModal.classList.remove('hidden');
            });
        });

        // Attach click events to the new buttons
        document.querySelectorAll('.inline-edit-trigger').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();

                const id = btn.getAttribute('data-id');
                const field = btn.getAttribute('data-field');
                const val = btn.getAttribute('data-val');

                inlineEditState = { id, field, triggerBtn: btn };

                let fieldName = 'Transfer';
                if (field === 'transfered_intern') fieldName = 'Internal';
                if (field === 'transfered_jira') fieldName = 'Jira';

                inlineEditTitle.innerText = `Transfer to ${fieldName}?`;

                // Set radio
                const radio = document.querySelector(`input[name="inline-edit-value"][value="${val}"]`);
                if (radio) radio.checked = true;

                // Position Modal
                const rect = btn.getBoundingClientRect();
                inlineEditModal.style.top = (rect.bottom + window.scrollY + 5) + 'px';

                // Prevent modal from going off-screen on the right
                let leftPos = rect.left + window.scrollX - 50;
                if (leftPos + 200 > window.innerWidth) { // 200 is approx modal width
                    leftPos = window.innerWidth - 210;
                }
                inlineEditModal.style.left = leftPos + 'px';

                inlineEditModal.classList.remove('hidden');
            });
        });
    }

    function renderConsecutive() {
        const tbody = document.getElementById('table-consecutive-body');
        tbody.innerHTML = '';

        currentData.consecutive.forEach(row => {
            renderAggRow(tbody, row, false);
        });
    }

    function renderGrouped() {
        const tbody = document.getElementById('table-grouped-body');
        tbody.innerHTML = '';

        currentData.grouped.forEach(row => {
            renderAggRow(tbody, row, true);
        });
    }

    function renderAggRow(tbody, row, isGrouped) {
        const tr = document.createElement('tr');

        if (row.is_pause) {
            tr.classList.add('bg-gray-50');
            tr.classList.add('text-gray-400');
        }

        // Columns
        let start, end, duration;
        if (currentType === 'real') {
            start = row.start_real;
            end = row.end_real;
            duration = row.duration_real_formatted;
        } else {
            start = row.start_tracking;
            end = row.end_tracking;
            duration = row.duration_tracking_formatted;
        }

        const intClass = getStatusColor(row.status_intern);
        const jiraClass = getStatusColor(row.status_jira);

        let html = '';

        if (!isGrouped) {
            html += `
             <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                ${start ? start.substring(0, 5) : '-'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                ${end ? end.substring(0, 5) : '-'}
            </td>`;
        }

        html += `
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium font-mono">
                ${row.task_id}
            </td>
            <td class="px-6 py-4 text-sm text-gray-500">
                ${row.description}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">
                ${duration}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${intClass}">
                    ${row.status_intern}
                </span>
            </td>
             <td class="px-6 py-4 whitespace-nowrap text-center">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${jiraClass}">
                    ${row.status_jira}
                </span>
            </td>
        `;

        tr.innerHTML = html;
        tbody.appendChild(tr);
    }

});
