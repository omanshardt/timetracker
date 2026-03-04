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
    const storedTheme = localStorage.getItem('timetracker_theme') || 'light';
    const today = new Date().toISOString().split('T')[0];

    let currentData = null;
    let currentType = storedType || 'real'; // 'real' or 'tracking'
    let currentDate = storedDate || today;
    let currentTheme = storedTheme;

    // Theme Toggle Logic
    const themeToggleBtn = document.getElementById('theme-toggle');
    const sunIcon = document.getElementById('sun-icon');
    const moonIcon = document.getElementById('moon-icon');

    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        if (theme === 'dark') {
            sunIcon.classList.remove('hidden');
            moonIcon.classList.add('hidden');
        } else {
            sunIcon.classList.add('hidden');
            moonIcon.classList.remove('hidden');
        }
        localStorage.setItem('timetracker_theme', theme);
        currentTheme = theme;
    }

    // Initialize theme
    applyTheme(currentTheme);

    if (themeToggleBtn) {
        themeToggleBtn.addEventListener('click', () => {
            const nextTheme = currentTheme === 'light' ? 'dark' : 'light';
            applyTheme(nextTheme);
        });
    }

    // Modal Elements
    const btnOpenModal = document.getElementById('btn-open-add-modal');
    const btnCancelModal = document.getElementById('btn-cancel-modal');
    const addEntryModal = document.getElementById('add-entry-modal');
    const addEntryForm = document.getElementById('add-entry-form');
    const modalReportingDate = document.getElementById('modal-reporting-date');
    const addModalTitle = document.getElementById('add-modal-title');

    // Hidden Fields
    const modalId = document.getElementById('modal-id');
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

    // Delete Modal Elements
    const deleteConfirmModal = document.getElementById('delete-confirm-modal');
    const btnConfirmDelete = document.getElementById('btn-confirm-delete');
    const btnCancelDelete = document.getElementById('btn-cancel-delete');
    let deleteTargetId = null;

    // Init
    typeSelector.value = currentType;
    datePicker.value = currentDate;
    fetchData(currentDate);

    // Modal Events
    if (btnOpenModal) {
        btnOpenModal.addEventListener('click', () => {
            addEntryForm.reset(); // Reset form first

            addModalTitle.innerText = 'Add New Entry';

            // Clear hidden fields manually for safety
            modalId.value = '';
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

        if (e.target === deleteConfirmModal) {
            deleteConfirmModal.classList.add('hidden');
            deleteTargetId = null;
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

    // Delete Confirmation Logic
    if (btnCancelDelete) {
        btnCancelDelete.addEventListener('click', () => {
            deleteConfirmModal.classList.add('hidden');
            deleteTargetId = null;
        });
    }

    if (btnConfirmDelete) {
        btnConfirmDelete.addEventListener('click', async () => {
            if (!deleteTargetId) return;

            try {
                const res = await fetch('api/delete_entry.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id: deleteTargetId })
                });

                const data = await res.json();
                if (data.success) {
                    deleteConfirmModal.classList.add('hidden');
                    deleteTargetId = null;
                    fetchData(currentDate); // Refresh completely
                } else {
                    alert('Error deleting entry: ' + (data.error || 'Unknown error'));
                }
            } catch (err) {
                console.error(err);
                alert('Connection error while deleting.');
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

            // Edit vs Add check
            const isEdit = modalId.value !== '';
            if (isEdit) {
                payload.id = modalId.value;
            }

            // Add hidden fields if they are set (from Copy action)
            if (modalStartReported.value !== '') payload.start_time_reported = modalStartReported.value;
            if (modalEndReported.value !== '') payload.end_time_reported = modalEndReported.value;
            if (modalTaskName.value !== '') payload.task_name = modalTaskName.value;
            if (modalDescriptionLong.value !== '') payload.description_long = modalDescriptionLong.value;
            if (modalTransfer.value !== '') payload.transfer = modalTransfer.value;
            if (modalTransferIntern.value !== '') payload.transfered_intern = modalTransferIntern.value;
            if (modalTransferJira.value !== '') payload.transfered_jira = modalTransferJira.value;

            const endpoint = isEdit ? 'api/update_entry.php' : 'api/add_entry.php';

            try {
                const res = await fetch(endpoint, {
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

    function getStatusBadge(val, field) {
        const isYes = val == 1;
        const text = isYes ? 'Yes' : 'No';
        const colorClass = isYes ? 'badge-success' : 'badge-error';
        const fieldName = field === 'transfer' ? 'Transfer' : (field === 'transfered_intern' ? 'Int' : 'Jira');
        
        return `<button class="inline-edit-trigger badge ${colorClass} hover:opacity-80 transition-opacity" 
                        data-id="${inlineEditState.id}" data-field="${field}" data-val="${val}" 
                        aria-label="Change ${fieldName} status (currently ${text})">
                    ${text}
                </button>`;
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
            const startClass = gapPrev ? 'bg-red-100 text-red-900 rounded px-1' : '';
            const endClass = gapNext ? 'bg-red-100 text-red-900 rounded px-1' : '';

            const startTitle = gapPrev ? `Previous ended before/after start (${gapPrev})` : '';
            const endTitle = gapNext ? `Next starts after/before end (${gapNext})` : '';

            // Using badges instead of emojis
            const btnTrans = `<button class="inline-edit-trigger badge ${row.transfer == 1 ? 'badge-success' : 'badge-error'} hover:opacity-80 transition-opacity" 
                                data-id="${row.id}" data-field="transfer" data-val="${row.transfer}" aria-label="Toggle Transfer status">
                                ${row.transfer == 1 ? 'Yes' : 'No'}
                              </button>`;
            const btnInt = `<button class="inline-edit-trigger badge ${row.transfered_intern == 1 ? 'badge-success' : 'badge-error'} hover:opacity-80 transition-opacity" 
                                data-id="${row.id}" data-field="transfered_intern" data-val="${row.transfered_intern}" aria-label="Toggle Internal status">
                                ${row.transfered_intern == 1 ? 'Yes' : 'No'}
                              </button>`;
            const btnJira = `<button class="inline-edit-trigger badge ${row.transfered_jira == 1 ? 'badge-success' : 'badge-error'} hover:opacity-80 transition-opacity" 
                                data-id="${row.id}" data-field="transfered_jira" data-val="${row.transfered_jira}" aria-label="Toggle Jira status">
                                ${row.transfered_jira == 1 ? 'Yes' : 'No'}
                              </button>`;

            tr.innerHTML = `
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 font-mono" title="${startTitle}">
                    <span class="${startClass}">${start ? start.substring(0, 5) : '-'}</span>
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 font-mono" title="${endTitle}">
                    <span class="${endClass}">${end ? end.substring(0, 5) : '-'}</span>
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-medium font-mono">
                    ${row.task_id || ''}
                </td>
                <td class="w-full px-6 py-4 text-sm text-gray-500 break-words">
                    ${row.description || ''}
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 font-mono">
                    ${duration}
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 text-center">
                    ${btnTrans}
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 text-center">
                    ${btnInt}
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 text-center">
                    ${btnJira}
                </td>
                <td class="w-px whitespace-nowrap px-6 py-4 text-center text-sm font-medium space-x-2">
                    <button class="btn-edit text-blue-600 hover:text-blue-900 transition-colors p-1" data-index="${index}" aria-label="Edit Entry" title="Edit Entry">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </button>
                    <button class="btn-copy text-indigo-600 hover:text-indigo-900 transition-colors p-1" data-index="${index}" aria-label="Copy Entry" title="Copy Entry">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </button>
                    <button class="btn-delete text-red-600 hover:text-red-900 transition-colors p-1" data-id="${row.id}" aria-label="Delete Entry" title="Delete Entry">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </td>
            `;
            tbody.appendChild(tr);
        });

        // Attach click events to Copy buttons
        document.querySelectorAll('.btn-copy').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                addEntryForm.reset();
                addModalTitle.innerText = 'Add New Entry (Copy)';

                const index = parseInt(btn.getAttribute('data-index'), 10);
                const row = currentData.raw[index];

                // Visible fields
                modalId.value = ''; // Ensure ID is clear for copy
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

        // Attach click events to Edit buttons
        document.querySelectorAll('.btn-edit').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                addEntryForm.reset();
                addModalTitle.innerText = 'Edit Entry';

                const index = parseInt(btn.getAttribute('data-index'), 10);
                const row = currentData.raw[index];

                // Make sure we have an ID to edit
                modalId.value = row.id;

                // Visible fields
                modalReportingDate.value = row.reporting_date; // Keep original date!
                document.getElementById('modal-start-time').value = row.start_time || '';
                document.getElementById('modal-end-time').value = row.end_time || '';
                document.getElementById('modal-task-id').value = row.task_id || '';
                document.getElementById('modal-description').value = row.description || '';

                // Hidden fields
                modalStartReported.value = row.start_time_reported || '';
                modalEndReported.value = row.end_time_reported || '';
                modalTaskName.value = row.task_name || '';
                modalDescriptionLong.value = row.description_long || '';

                // Original transfer status
                modalTransfer.value = row.transfer !== undefined && row.transfer !== null ? row.transfer : '';
                modalTransferIntern.value = row.transfered_intern !== undefined && row.transfered_intern !== null ? row.transfered_intern : '';
                modalTransferJira.value = row.transfered_jira !== undefined && row.transfered_jira !== null ? row.transfered_jira : '';

                addEntryModal.classList.remove('hidden');
            });
        });

        // Attach click events to Delete buttons
        document.querySelectorAll('.btn-delete').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                deleteTargetId = btn.getAttribute('data-id');
                deleteConfirmModal.classList.remove('hidden');
            });
        });

        // Attach click events to the new inline buttons
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

        const getBadge = (status) => {
            if (status === 'green') return '<span class="badge badge-success">Yes</span>';
            if (status === 'yellow') return '<span class="badge badge-warning">Partial</span>';
            return '<span class="badge badge-error">No</span>';
        };

        const iconInt = getBadge(row.status_intern);
        const iconJira = getBadge(row.status_jira);

        let html = '';

        if (!isGrouped) {
            html += `
             <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 font-mono">
                ${start ? start.substring(0, 5) : '-'}
            </td>
            <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 font-mono">
                ${end ? end.substring(0, 5) : '-'}
            </td>`;
        }

        html += `
            <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-900 font-medium font-mono">
                ${row.task_id}
            </td>
            <td class="w-full px-6 py-4 text-sm text-gray-500 break-words">
                ${row.description}
            </td>
            <td class="w-px whitespace-nowrap px-6 py-4 text-sm text-gray-500 font-mono">
                ${duration}
            </td>
            <td class="w-px whitespace-nowrap px-6 py-4 text-center">
                ${iconInt}
            </td>
             <td class="w-px whitespace-nowrap px-6 py-4 text-center">
                ${iconJira}
            </td>
        `;

        tr.innerHTML = html;
        tbody.appendChild(tr);
    }

});
