<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Tracker Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Apply theme immediately to prevent flash of light mode
        (function () {
            var theme = localStorage.getItem('timetracker_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="stylesheet" href="css/style.css">
    <style>
        .dimmed {
            opacity: 0.5;
        }

        .bg-gap {
            background-color: #fee2e2;
        }
    </style>
</head>

<body class="font-sans">

    <div id="app-container" class="container mx-auto px-4 pb-8 pt-0 max-w-7xl"
        style="opacity: 0; transition: opacity 0.15s ease-in;">

        <!-- Sticky Header Wrapper -->
        <div class="sticky top-0 z-40 bg-[var(--color-bg-base)] pt-4 pb-2 mb-6">
            <!-- Combined Header & Status Bar Container -->
            <header class="bg-white rounded-lg shadow flex flex-col divide-y divide-gray-200 dark:divide-gray-700">
                
                <!-- Row 1: Header / Controls -->
                <div class="px-6 py-3.5 flex flex-col md:flex-row justify-between items-center md:items-end gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 mb-0">Time Tracker Overview</h1>
                        <p class="text-gray-500 text-xs">Manage and track your daily tasks efficiently.</p>
                    </div>

                    <div class="flex flex-wrap items-end gap-3">
                        <!-- Theme Toggle Button -->
                        <div class="flex flex-col">
                            <button id="theme-toggle"
                                class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 p-2 rounded-lg transition-colors flex items-center gap-2"
                                aria-label="Toggle dark mode">
                                <!-- Sun Icon (Light Mode) -->
                                <svg id="sun-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364-6.364l-.707.707M6.343 17.657l-.707.707M16.243 17.657l.707.707M7.757 6.343l.707.707M12 8a4 4 0 100 8 4 4 0 000-8z" />
                                </svg>
                                <!-- Moon Icon (Dark Mode) -->
                                <svg id="moon-icon" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </button>
                        </div>

                        <!-- Date Navigation Buttons -->
                        <div class="flex items-center gap-1 bg-gray-100 dark:bg-gray-800 p-1 rounded-lg">
                            <!-- Gehe einen Tag zurück -->
                            <button id="btn-prev-day" 
                                class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md text-gray-700 dark:text-gray-300 transition-colors" 
                                title="Vorheriger Tag">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                            <!-- Gehe zu heute -->
                            <button id="btn-today" 
                                class="px-2.5 py-0.5 text-xs font-semibold hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md text-gray-700 dark:text-gray-300 transition-colors" 
                                title="Heute">
                                Heute
                            </button>
                            <!-- Gehe einen Tag nach vorne -->
                            <button id="btn-next-day" 
                                class="p-1.5 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-md text-gray-700 dark:text-gray-300 transition-colors" 
                                title="Nächster Tag">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        </div>

                        <!-- Date Picker -->
                        <div class="flex flex-col">
                            <label for="date-picker" class="block text-xs font-medium text-gray-500 mb-0.5">Date</label>
                            <input type="date" id="date-picker"
                                class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 py-1.5 px-2 border text-sm"
                                value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <!-- Work Days Link -->
                        <div>
                            <a href="workdays.php"
                                class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-1.5 px-3 rounded shadow transition-colors flex items-center gap-1.5 text-sm"
                                aria-label="Manage work days">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z"
                                        clip-rule="evenodd" />
                                </svg>
                                Work Days
                            </a>
                        </div>

                        <!-- Add Entry Button -->
                        <div>
                            <button id="btn-open-add-modal"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-1.5 px-3 rounded shadow transition-colors flex items-center gap-1.5 text-sm"
                                aria-label="Add new time entry">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                        clip-rule="evenodd" />
                                </svg>
                                Add Entry
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Row 2: Unified Status Bar -->
                <div class="px-6 py-2.5 flex flex-wrap items-center gap-6">
                    <!-- Day Headline -->
                    <div class="flex-1 min-w-[180px]">
                        <h2 id="day-headline" class="text-lg font-semibold text-gray-800 mb-0">Loading...</h2>
                    </div>

                    <!-- Total Duration -->
                    <div class="flex items-center gap-2.5">
                        <div class="bg-blue-100 rounded-full p-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Worked</span>
                            <p class="text-base font-bold text-gray-900 leading-tight" id="total-duration">--:--</p>
                        </div>
                    </div>

                    <!-- Today's Delta (only visible on work days) -->
                    <div id="balance-delta-section" class="flex items-center gap-2.5 hidden">
                        <div id="delta-icon" class="rounded-full p-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Delta</span>
                            <p class="text-base font-bold leading-tight" id="balance-delta">--:--</p>
                        </div>
                    </div>

                    <!-- Running Balance (always visible) -->
                    <div id="balance-cumulative-section" class="flex items-center gap-2.5">
                        <div id="balance-icon" class="rounded-full p-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Balance</span>
                            <p class="text-base font-bold leading-tight" id="balance-cumulative">--:--</p>
                        </div>
                    </div>
                </div>

            </header>
        </div>


        <!-- Table 1: Detailed View -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Detailed View</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start</th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                End</th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Task ID</th>
                            <th scope="col"
                                class="w-full px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration</th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Transfer</th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex flex-col items-center gap-1">
                                    <span>Int</span>
                                    <button class="inline-edit-all-trigger bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 px-1.5 py-0.5 rounded text-[10px] tracking-normal normal-case font-semibold transition-colors mt-0.5"
                                            data-field="transfered_intern" title="Set all to yes/no">
                                        All
                                    </button>
                                </div>
                            </th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-2 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex flex-col items-center gap-1">
                                    <span>Jira</span>
                                    <button class="inline-edit-all-trigger bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-100 px-1.5 py-0.5 rounded text-[10px] tracking-normal normal-case font-semibold transition-colors mt-0.5"
                                            data-field="transfered_jira" title="Set all to yes/no">
                                        All
                                    </button>
                                </div>
                            </th>
                            <th scope="col" class="w-px whitespace-nowrap relative px-6 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody id="table-detailed-body" class="bg-white divide-y divide-gray-200">
                        <!-- Content rendered by JS -->
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-900">Total (Active tasks,
                                no Pause):</td>
                            <td class="px-6 py-3 text-left font-bold text-gray-900" id="table-detailed-footer-sum">--:--
                            </td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Table 2: Consecutive Aggregation -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-8">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Consecutive Tasks</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Start</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                End</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Task ID</th>
                            <th
                                class="w-full px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Duration</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Int</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jira</th>
                        </tr>
                    </thead>
                    <tbody id="table-consecutive-body" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Table 3: Grouped Aggregation -->
        <div class="bg-white rounded-lg shadow overflow-hidden mb-12">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-medium text-gray-900">Grouped Tasks (All Day)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Task ID</th>
                            <th
                                class="w-full px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Description</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Total Duration</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Int</th>
                            <th
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jira</th>
                        </tr>
                    </thead>
                    <tbody id="table-grouped-body" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Inline Edit Modal (Hidden, positioned dynamically via JS) -->
    <div id="inline-edit-modal"
        class="absolute hidden bg-white border border-gray-200 shadow-2xl rounded-lg p-5 z-40 text-sm w-56 transform transition-all">
        <div class="mb-4 font-bold text-gray-900 text-base border-b border-gray-100 pb-2" id="inline-edit-title">Update
            Status</div>
        <div class="flex items-center gap-6 mb-5">
            <label class="inline-flex items-center cursor-pointer group">
                <input type="radio" name="inline-edit-value" value="1"
                    class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="ml-2 text-gray-700 group-hover:text-blue-600 transition-colors">Yes</span>
            </label>
            <label class="inline-flex items-center cursor-pointer group">
                <input type="radio" name="inline-edit-value" value="0"
                    class="form-radio h-4 w-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                <span class="ml-2 text-gray-700 group-hover:text-red-600 transition-colors">No</span>
            </label>
        </div>
        <div class="flex justify-between items-center mt-2 gap-3">
            <button id="btn-inline-cancel" type="button"
                class="flex-1 bg-gray-100 hover:bg-gray-200 text-gray-800 font-medium py-2 px-3 rounded transition-colors text-xs">Cancel</button>
            <button id="btn-inline-save" type="button"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-3 rounded shadow transition-colors text-xs">Save</button>
        </div>
    </div>

    <!-- Add Entry Modal -->
    <div id="add-entry-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden p-4"
        style="z-index: 50;">
        <div class="relative top-10 mx-auto p-8 border max-w-md w-full shadow-2xl rounded-lg bg-white">
            <div class="text-left">
                <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="add-modal-title">Add New Entry</h3>
                <div class="mt-2">
                    <form id="add-entry-form">
                        <input type="hidden" id="modal-id">
                        <input type="hidden" id="modal-start-reported">
                        <input type="hidden" id="modal-end-reported">
                        <input type="hidden" id="modal-task-name">
                        <input type="hidden" id="modal-description-long">
                        <input type="hidden" id="modal-transfer">
                        <input type="hidden" id="modal-transfer-intern">
                        <input type="hidden" id="modal-transfer-jira">

                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2"
                                for="modal-reporting-date">Reporting Date</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="modal-reporting-date" type="date" required>
                        </div>
                        <div class="mb-4 flex gap-4">
                            <div class="w-1/2">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="modal-start-time">Start
                                    Time</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="modal-start-time" type="time" required>
                            </div>
                            <div class="w-1/2">
                                <label class="block text-gray-700 text-sm font-bold mb-2" for="modal-end-time">End
                                    Time</label>
                                <input
                                    class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                    id="modal-end-time" type="time" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700 text-sm font-bold mb-2" for="modal-task-id">Task
                                ID</label>
                            <input
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="modal-task-id" type="text" placeholder="e.g. PROJ-123" required>
                        </div>
                        <div class="mb-6">
                            <label class="block text-gray-700 text-sm font-bold mb-2"
                                for="modal-description">Description</label>
                            <textarea
                                class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
                                id="modal-description" rows="3" placeholder="Optional notes..."></textarea>
                        </div>
                        <div class="flex items-center justify-between">
                            <button id="btn-cancel-modal" type="button"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Cancel
                            </button>
                            <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save Entry
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirm Modal -->
    <div id="delete-confirm-modal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog"
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
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Delete Entry</h3>
                    <div class="mt-2 text-center">
                        <p class="text-sm text-gray-500">Are you sure you want to completely delete this entry? This
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

    <script src="js/app.js"></script>
</body>

</html>