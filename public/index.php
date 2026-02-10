<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Time Tracker Overview</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .dimmed { opacity: 0.5; }
        .bg-gap { background-color: #fee2e2; } /* red-100 placeholder */
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
                <!-- Date Picker -->
                <div>
                    <label for="date-picker" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                    <input type="date" id="date-picker" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <!-- Type Selector -->
                <div>
                    <label for="type-selector" class="block text-sm font-medium text-gray-700 mb-1">Time Type</label>
                    <select id="type-selector" class="border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 p-2 border bg-white">
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
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task ID</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Int</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jira</th>
                        </tr>
                    </thead>
                    <tbody id="table-detailed-body" class="bg-white divide-y divide-gray-200">
                        <!-- Content rendered by JS -->
                    </tbody>
                    <tfoot class="bg-gray-50">
                        <tr>
                            <td colspan="4" class="px-6 py-3 text-right font-medium text-gray-900">Total (Active tasks, no Pause):</td>
                            <td class="px-6 py-3 text-left font-bold text-gray-900" id="table-detailed-footer-sum">--:--</td>
                            <td colspan="2"></td>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Int</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jira</th>
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Task ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Duration</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Int</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Jira</th>
                        </tr>
                    </thead>
                    <tbody id="table-grouped-body" class="bg-white divide-y divide-gray-200">
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="js/app.js"></script>
</body>
</html>