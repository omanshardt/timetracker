<!DOCTYPE html>
<html lang="de">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Tracker Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .dimmed {
            opacity: 0.5;
        }

        .bg-gap {
            background-color: #fee2e2;
        }

        /* red-100 placeholder */
    </style>
</head>

<body class="bg-gray-50 text-gray-800 font-sans">

    <div class="container mx-auto px-4 py-8 max-w-7xl">

        <!-- Header / Controls -->
        <div class="bg-white rounded-lg shadow p-6 mb-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Time Tracker Overview</h1>
            </div>

            <div class="flex items-center gap-4">
                <!-- Add Entry Button -->
                <div>
                    <button id="btn-open-add-modal"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow mt-6">
                        + Add Entry
                    </button>
                </div>

                <!-- Date Picker -->
                <div>
                    <label for="date-picker" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="date-picker"
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border"
                        value="<?php echo date('Y-m-d'); ?>">
                </div>

                <!-- Type Selector -->
                <div>
                    <label for="type-selector" class="block text-sm font-medium text-gray-700 mb-1">Time Type</label>
                    <select id="type-selector"
                        class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-white">
                        <option value="real">Real Time</option>
                        <option value="tracking">Tracking Time</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Headline -->
        <h2 id="day-headline" class="text-xl font-semibold mb-4 text-gray-800">Loading...</h2>

        <!-- Summary Widget (Top) -->
        <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-8">
            <div class="flex justify-between">
                <span class="font-medium text-blue-700">Total Duration (Active):</span>
                <span id="total-duration" class="font-bold text-blue-900 text-lg">--:--</span>
            </div>
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
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Int</th>
                            <th scope="col"
                                class="w-px whitespace-nowrap px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jira</th>
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
    <div id="inline-edit-modal" class="absolute hidden bg-white border shadow-lg rounded p-3 z-40 text-sm w-48">
        <div class="mb-2 font-medium text-gray-800" id="inline-edit-title">Update Status</div>
        <div class="flex items-center gap-4 mb-3">
            <label class="inline-flex items-center">
                <input type="radio" name="inline-edit-value" value="1" class="form-radio text-blue-600">
                <span class="ml-2">Yes</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="inline-edit-value" value="0" class="form-radio text-blue-600">
                <span class="ml-2">No</span>
            </label>
        </div>
        <div class="flex justify-between mt-2">
            <button id="btn-inline-cancel" type="button"
                class="bg-gray-200 hover:bg-gray-300 text-gray-800 py-1 px-3 rounded text-xs">Cancel</button>
            <button id="btn-inline-save" type="button"
                class="bg-blue-600 hover:bg-blue-700 text-white py-1 px-3 rounded text-xs">Save</button>
        </div>
    </div>

    <!-- Add Entry Modal -->
    <div id="add-entry-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden"
        style="z-index: 50;">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="add-modal-title">Add New Entry</h3>
                <div class="mt-2 px-7 py-3 text-left">
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